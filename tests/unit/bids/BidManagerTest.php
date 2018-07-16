<?php

use App\Bid;
use Mockery as m;
use WasteMaster\v1\Bids\BidManager;

class BidManagerTest extends UnitTestCase
{
    protected $bids;

    /**
     * @var \WasteMaster\v1\Bids\BidManager
     */
    protected $manager;

    public function setUp()
    {
        parent::setUp();

        $this->bids = m::mock('\App\Bid');
        $this->manager = new BidManager($this->bids);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testGetBidSuccess()
    {
        $this->bids->shouldReceive('with->find')
                      ->once()
                      ->andReturn((object)[
                'id' => 1
            ]);

        $this->assertEquals(1, $this->manager->find(1)->id);
    }

    public function testGetThrowsOnFailure()
    {
        $this->setExpectedException('WasteMaster\v1\Bids\BidNotFound');

        $this->bids->shouldReceive('with->find')
                      ->once()
                      ->andReturn();

        $this->manager->find(1);
    }

    public function testCreateThrowsOnNoData()
    {
        $this->setExpectedException('WasteMaster\v1\Bids\MissingRequiredFields');

        $this->manager->create();
    }

    public function testCreateThrowsWhenBidExistsByAddress()
    {
        $this->setExpectedException('WasteMaster\v1\Bids\BidExists');

        $this->bids->shouldReceive('where->count')
                      ->once()
                      ->andReturn(1);

        $bid = $this->manager
            ->setHaulerID(3)
            ->setHaulerEmail('foo@example.com')
            ->setLeadID(13)
            ->setStatus(Bid::STATUS_LIVE)
            ->setWaste(1, 2, 3)
            ->setRecycling(4,5,6)
            ->setWastePrice(132)
            ->setRecyclePrice(423)
            ->setNet(543)
            ->create();
    }

    public function testCreateSuccess()
    {
        $bid  = m::mock('App\Bid[setAttribute,getAttribute,save]');
        $lead = m::mock('App\Lead[setAttribute,getAttribute,save]');

        $expects = [
            'hauler_id' => 3,
            'hauler_email' => 'foo@example.com',
            'lead_id' => 13,
            'status' => Bid::STATUS_LIVE,
            'notes' => 'Schnotes!',
            'msw_price' => 123,
            'rec_price' => 456,
            'rec_offset' => 20,
            'fuel_surcharge' => 15.2,
            'env_surcharge' => 12.5,
            'recovery_fee' => 5.35,
            'admin_fee' => 10.2,
            'other_fees' => 1.23,
            'net_monthly' => 565,
        ];

        $this->bids->shouldReceive('where->count')
                      ->once()
                      ->andReturn(0);
        $this->bids->shouldReceive('create')
                      ->once()
                      ->with(Mockery::subset($expects))
                      ->andReturn($bid);
        $bid->shouldReceive('getAttribute')
            ->once()
            ->with('lead')
            ->andReturn($lead);
        $bid->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(4);
        $lead->shouldReceive('getAttribute')
            ->with('bid_count')
            ->andReturn(1);
        $lead->shouldReceive('setAttribute')
            ->with('bid_count', 2);
        $lead->shouldReceive('save')
            ->once();

        $Bid = $this->manager
            ->setHaulerID(3)
            ->setHaulerEmail('foo@example.com')
            ->setLeadID(13)
            ->setStatus(Bid::STATUS_LIVE)
            ->setNotes('Schnotes!')
            ->setWaste(1, 2, 3)
            ->setRecycling(4,5,6)
            ->setWastePrice(123)
            ->setRecyclePrice(456)
            ->setRecycleOffset(20)
            ->setFuelSurcharge(15.2)
            ->setEnvironmentalSurcharge(12.5)
            ->setRecoveryFee(5.35)
            ->setAdminFee(10.2)
            ->setOtherFees(1.23)
            ->setNet(565)
            ->create();

        $this->assertEquals(4, $Bid->id);
    }

    public function testUpdateFailsNotFound()
    {
        $this->setExpectedException('WasteMaster\v1\Bids\BidNotFound');

        $this->bids->shouldReceive('find')
                      ->once()
                      ->with(12)
                      ->andReturn();

        $this->manager->update(12);
    }

    public function testUpdateFailsNoData()
    {
        $this->setExpectedException('WasteMaster\v1\Bids\NothingToUpdate');

        $this->bids->shouldReceive('find')
                      ->once()
                      ->with(12)
                      ->andReturn($this->bids);

        $this->manager->update(12);
    }

    public function testUpdateSuccess()
    {
        $this->bids->shouldReceive('find')
                      ->once()
                      ->with(12)
                      ->andReturn($this->bids);
        $this->bids->shouldReceive('fill')
                      ->once();
        $this->bids->shouldReceive('save')
                      ->once();

        $response = $this->manager
            ->setStatus(Bid::STATUS_ACCEPTED)
            ->update(12);

        $this->assertEquals($this->bids, $response);
    }

    public function testDeleteThrowsOnNotFound()
    {
        $this->setExpectedException('WasteMaster\v1\Bids\BidNotFound');

        $this->bids->shouldReceive('with->find')
                      ->once()
                      ->with(12)
                      ->andReturn();

        $this->manager->delete(12);
    }

    public function testDeleteSuccess()
    {
        $bid = m::mock('App\Bid[setAttribute,getAttribute,save]');
        $lead = m::mock('App\Lead[setAttribute,getAttribute,save]');

        $this->bids->shouldReceive('with->find')
            ->once()
            ->andReturn($bid);
        $bid->shouldReceive('getAttribute')
            ->once()
            ->with('lead')
            ->andReturn($lead);
        $lead->shouldReceive('getAttribute')
             ->with('bid_count')
             ->andReturn(1);
        $lead->shouldReceive('setAttribute')
             ->with('bid_count', 0);
        $lead->shouldReceive('save')
             ->once();

        $this->manager->delete(12);
    }


    public function testAcceptBid()
    {
        $this->expectsEvents(App\Events\AcceptedBid::class);

        $bid = m::mock('App\Bid[setAttribute,getAttribute,save]');
        $lead = m::mock('App\Lead[setAttribute,getAttribute,save]');

        $bid->shouldReceive('getAttribute')
            ->with('lead_id')
            ->andReturn(22);
        $bid->shouldReceive('setAttribute')
            ->with('status', Bid::STATUS_ACCEPTED);
        $bid->shouldReceive('setAttribute')
            ->with('gross_profit', 123);
        $bid->shouldReceive('save');
        $bid->shouldReceive('getAttribute')
            ->with('lead')
            ->andReturn($lead);
        $lead->shouldReceive('setAttribute')
            ->once()
            ->andReturn($lead);
        $lead->shouldReceive('save')
            ->once()
            ->andReturn($lead);

        // Find the existing lead
        $this->bids->shouldReceive('with->find')
            ->once()
            ->andReturn($bid);

        // Close all bids
        $this->bids->shouldReceive('where')
            ->with('lead_id', 22)
            ->andReturn($this->bids);
        $this->bids->shouldReceive('update')
            ->once()
            ->with(['status' => \App\Bid::STATUS_CLOSED]);

        // Set and save the current bid
        $this->bids->shouldReceive('setAttribute')
            ->with('status', \App\Bid::STATUS_ACCEPTED);

        $this->manager->acceptBid(3, 123);
    }

    public function testRescindBid()
    {
        $bid = m::mock('App\Bid[setAttribute,getAttribute,save]');
        $lead = m::mock('App\Lead[setAttribute,getAttribute,save]');

        $bid->shouldReceive('getAttribute')
            ->with('lead_id')
            ->andReturn(22);
        $bid->shouldReceive('getAttribute')
            ->once()
            ->with('lead')
            ->andReturn($lead);
        $lead->shouldReceive('setAttribute')
             ->once()
             ->andReturn($lead);
        $lead->shouldReceive('save')
             ->once()
             ->andReturn($lead);

        $this->bids->shouldReceive('with->find')
                   ->once()
                   ->andReturn($bid);
        $this->bids->shouldReceive('where')
            ->with('lead_id', 22)
            ->andReturn($this->bids);
        $this->bids->shouldReceive('update')
            ->with(['status' => \App\Bid::STATUS_LIVE]);

        $this->manager->rescindBid(3);

    }

}
