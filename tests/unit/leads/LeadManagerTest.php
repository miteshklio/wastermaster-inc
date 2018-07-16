<?php

use Mockery as m;

class LeadManagerTest extends UnitTestCase
{
    protected $leads;
    protected $cities;

    /**
     * @var \WasteMaster\v1\Leads\LeadManager
     */
    protected $manager;

    public function setUp()
    {
        parent::setUp();

        $this->leads = m::mock('\App\Lead');
        $this->cities = m::mock('\App\City');
        $this->manager = new \WasteMaster\v1\Leads\LeadManager($this->leads, $this->cities);
    }

    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    public function testGetLeadSuccess()
    {
        $this->leads->shouldReceive('with->find')
            ->once()
            ->andReturn((object)[
                'id' => 1
            ]);

        $this->assertEquals(1, $this->manager->find(1)->id);
    }

    public function testGetThrowsOnFailure()
    {
        $this->setExpectedException('WasteMaster\v1\Leads\LeadNotFound');

        $this->leads->shouldReceive('with->find')
            ->once()
            ->andReturn();

        $this->manager->find(1);
    }

    public function testCreateThrowsOnNoData()
    {
        $this->setExpectedException('WasteMaster\v1\Leads\MissingRequiredFields');

        $this->manager->create();
    }

    public function testCreateThrowsWhenLeadExistsByAddress()
    {
        $this->setExpectedException('WasteMaster\v1\Leads\LeadExists');

        $this->leads->shouldReceive('where->count')
            ->once()
            ->andReturn(1);

        $lead = $this->manager
            ->setCompany('company a')
            ->setAddress('123 Alphabet St')
            ->setCityID(2)
            ->setContactName('contact person')
            ->setContactEmail('foo@example.com')
            ->setAccountNum('abc123')
            ->setHaulerID(3)
            ->setWaste(1, 2, 3)
            ->setRecycling(4,5,6)
            ->setWaste2(2, 3, 4)
            ->setRecycling2(5,6,7)
            ->setMonthlyPrice(123)
            ->create();
    }

    public function testCreateSuccess()
    {
        $expects = [
            'company' => 'companya',
            'address' => '123AlphabetSt',
            'service_area_id' => 2,
            'contact_name' => 'contactperson',
            'contact_email' => 'foo@example.com',
            'account_num' => 'abc123',
            'hauler_id' => 3,
            'msw_qty' => 1,
            'msw_yards' => 2.5,
            'msw_per_week' => 3.2,
            'rec_qty' => 4,
            'rec_yards' => 5.1,
            'rec_per_week' => 6.2,
            'monthly_price' => 123,
            'status' => \App\Lead::NEW,
            'archived' => 0,
            'bid_count' => 0,
        ];

        $this->leads->shouldReceive('where->count')
                    ->once()
                    ->andReturn(0);
        $this->leads->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'id' => 4
            ]);

        $lead = $this->manager
            ->setCompany('companya')
            ->setAddress('123AlphabetSt')
            ->setServiceAreaID(2)
            ->setContactName('contactperson')
            ->setContactEmail('foo@example.com')
            ->setAccountNum('abc123')
            ->setHaulerID(3)
            ->setWaste(1, 2, 3)
            ->setRecycling(4,5,6)
            ->setMonthlyPrice(123)
            ->create();

        $this->assertEquals(4, $lead->id);
    }

    public function testUpdateFailsNotFound()
    {
        $this->setExpectedException('WasteMaster\v1\Leads\LeadNotFound');

        $this->leads->shouldReceive('find')
            ->once()
            ->with(12)
            ->andReturn();

        $this->manager->update(12);
    }

    public function testUpdateFailsNoData()
    {
        $this->setExpectedException('WasteMaster\v1\Leads\NothingToUpdate');

        $this->leads->shouldReceive('find')
                    ->once()
                    ->with(12)
                    ->andReturn($this->leads);

        $this->manager->update(12);
    }

    public function testUpdateSuccess()
    {
        $this->leads->shouldReceive('find')
            ->once()
            ->with(12)
            ->andReturn($this->leads);
        $this->leads->shouldReceive('fill')
            ->once();
        $this->leads->shouldReceive('save')
            ->once();

        $response = $this->manager
            ->setCompany('company b')
            ->setWaste2(2, 3, 4)
            ->setRecycling2(5,6,7)
            ->update(12);

        $this->assertEquals($this->leads, $response);
    }

    public function testDeleteThrowsOnNotFound()
    {
        $this->setExpectedException('WasteMaster\v1\Leads\LeadNotFound');

        $this->leads->shouldReceive('with->find')
                    ->once()
                    ->with(12)
                    ->andReturn();

        $this->manager->delete(12);
    }

    public function testArchiveThrowsOnNotFound()
    {
        $this->setExpectedException('WasteMaster\v1\Leads\LeadNotFound');

        $this->leads->shouldReceive('with->find')
                    ->once()
                    ->with(12)
                    ->andReturn();

        $this->manager->archive(12);
    }

    public function testShouldShowPostMatchBidReturnsFalseIfNoBids()
    {
        $lead = new class() extends \App\Lead {
            protected $status = \App\Lead::BIDS_REQUESTED;
            protected $monthly_price = 200;
            protected $gross_profit = 0;
            protected $bid_count = 0;
        };
        $bid = new class() extends \App\Bid {
            protected $net_monthly = 100;
        };

        $this->assertFalse($this->manager->shouldShowPostMatchBid($lead, $bid));
    }

    public function testShouldShowPostMatchBidReturnsTrueIfLeadHigherThanBid()
    {
        $lead = new \App\Lead([
            'status' => \App\Lead::BIDS_REQUESTED,
            'monthly_price' => 200,
            'gross_profit' => 0,
            'bid_count' => 1
        ]);
        $bid = new \App\Bid([
            'net_monthly' => 100
        ]);

        $this->assertTrue($this->manager->shouldShowPostMatchBid($lead, $bid));
    }

    public function testShouldShowPostMatchBidReturnsTrueIfLeadHigherThanBidRebidding()
    {
        $lead = new \App\Lead([
            'status' => \App\Lead::REBIDDING,
            'monthly_price' => 200,
            'gross_profit' => 50,
            'bid_count' => 1
        ]);
        $bid = new \App\Bid([
            'net_monthly' => 110
        ]);

        $this->assertTrue($this->manager->shouldShowPostMatchBid($lead, $bid));
    }

    public function testShouldShowPostMatchBidReturnsFalseIfLeadHigherThanBidRebidding()
    {
        $lead = new \App\Lead([
            'status' => \App\Lead::REBIDDING,
            'monthly_price' => 200,
            'gross_profit' => 50,
            'bid_count' => 1
        ]);
        $bid = new \App\Bid([
            'net_monthly' => 160
        ]);

        $this->assertFalse($this->manager->shouldShowPostMatchBid($lead, $bid));
    }


}
