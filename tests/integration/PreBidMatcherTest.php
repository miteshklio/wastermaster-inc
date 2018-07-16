<?php

class PreBidMatcherTest extends IntegrationTestCase
{
    /**
     * @var \WasteMaster\v1\Bids\PreBidMatcher
     */
    protected $matcher;

    public function setUp()
    {
        parent::setUp();
        $this->matcher = app(\WasteMaster\v1\Bids\PreBidMatcher::class);
    }

    public function testMatchWasteDoesntMatchAcrossRecords()
    {
        $hauler = factory(\App\Hauler::class)->create();
        $leads = factory(\App\Lead::class, 2)->create(['hauler_id' => $hauler->id]);
        $bidYes = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[0]->id, 'net_monthly' => 200]);
        $bidNo = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[1]->id, 'net_monthly' => 100]);

        $leads[0]->fill([
            'msw_qty' => 1,
            'msw_yards' => 1.0,
            'msw_per_week' => 1
        ])->save();
        $leads[0]->fill([
            'msw_qty' => 1,
            'msw_yards' => 1.5,
            'msw_per_week' => 2
        ])->save();

        $testLead = factory(\App\Lead::class)->create([
            'msw_qty' => 1,
            'msw_yards' => 1.5,
            'msw_per_week' => 1
        ]);

        $result = $this->matcher->matchWaste($testLead);

        $this->assertNull($result);
    }

    public function testMatchWasteMatchesMSW()
    {
        $hauler = factory(\App\Hauler::class)->create();
        $leads = factory(\App\Lead::class, 2)->create(['hauler_id' => $hauler->id]);
        $bidYes = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[0]->id, 'net_monthly' => 200]);
        $bidNo = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[1]->id, 'net_monthly' => 100]);

        $leads[0]->fill([
            'msw_qty' => 1,
            'msw_yards' => 1.5,
            'msw_per_week' => 1
        ])->save();
        $leads[0]->fill([
            'msw_qty' => 1,
            'msw_yards' => 1.5,
            'msw_per_week' => 1
        ])->save();

        $testLead = factory(\App\Lead::class)->create([
            'msw_qty' => 1,
            'msw_yards' => 1.5,
            'msw_per_week' => 1
        ]);

        $result = $this->matcher->matchWaste($testLead);

        $this->assertInstanceOf(\App\Bid::class, $result);
    }

    public function testMatchWasteMatchesMSW2()
    {
        $hauler = factory(\App\Hauler::class)->create();
        $leads = factory(\App\Lead::class, 2)->create(['hauler_id' => $hauler->id]);
        $bidYes = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[0]->id, 'net_monthly' => 200]);
        $bidNo = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[1]->id, 'net_monthly' => 100]);

        $leads[0]->fill([
            'msw_qty' => 2,
            'msw_yards' => 2.5,
            'msw_per_week' => 2,
            'msw2_qty' => 1,
            'msw2_yards' => 1.5,
            'msw2_per_week' => 1,
        ])->save();
        $leads[0]->fill([
            'msw_qty' => 1,
            'msw_yards' => 1.5,
            'msw_per_week' => 1
        ])->save();

        $testLead = factory(\App\Lead::class)->create([
            'msw_qty' => 1,
            'msw_yards' => 1.5,
            'msw_per_week' => 1
        ]);

        $result = $this->matcher->matchWaste($testLead);

        $this->assertInstanceOf(\App\Bid::class, $result);
    }

    public function testMatchRecDoesntMatchAcrossRecords()
    {
        $hauler = factory(\App\Hauler::class)->create();
        $leads = factory(\App\Lead::class, 2)->create(['hauler_id' => $hauler->id]);
        $bidYes = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[0]->id, 'net_monthly' => 200]);
        $bidNo = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[1]->id, 'net_monthly' => 100]);

        $leads[0]->fill([
            'rec_qty' => 1,
            'rec_yards' => 1.0,
            'rec_per_week' => 1
        ])->save();
        $leads[0]->fill([
            'rec_qty' => 1,
            'rec_yards' => 1.5,
            'rec_per_week' => 2
        ])->save();

        $testLead = factory(\App\Lead::class)->create([
            'rec_qty' => 1,
            'rec_yards' => 1.5,
            'rec_per_week' => 1
        ]);

        $result = $this->matcher->matchRecycle($testLead);

        $this->assertNull($result);
    }

    public function testMatchRecMatchesREC()
    {
        $hauler = factory(\App\Hauler::class)->create();
        $leads = factory(\App\Lead::class, 2)->create(['hauler_id' => $hauler->id]);
        $bidYes = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[0]->id, 'net_monthly' => 200]);
        $bidNo = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[1]->id, 'net_monthly' => 100]);

        $leads[0]->fill([
            'rec_qty' => 1,
            'rec_yards' => 1.5,
            'rec_per_week' => 1
        ])->save();
        $leads[0]->fill([
            'rec_qty' => 1,
            'rec_yards' => 1.5,
            'rec_per_week' => 1
        ])->save();

        $testLead = factory(\App\Lead::class)->create([
            'rec_qty' => 1,
            'rec_yards' => 1.5,
            'rec_per_week' => 1
        ]);

        $result = $this->matcher->matchRecycle($testLead);

        $this->assertInstanceOf(\App\Bid::class, $result);
    }

    public function testMatchRecMatchesREC2()
    {
        $hauler = factory(\App\Hauler::class)->create();
        $leads = factory(\App\Lead::class, 2)->create(['hauler_id' => $hauler->id]);
        $bidYes = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[0]->id, 'net_monthly' => 200]);
        $bidNo = factory(\App\Bid::class)->create(['hauler_id' => $hauler->id, 'lead_id' => $leads[1]->id, 'net_monthly' => 100]);

        $leads[0]->fill([
            'rec_qty' => 2,
            'rec_yards' => 2.5,
            'rec_per_week' => 2,
            'rec2_qty' => 1,
            'rec2_yards' => 1.5,
            'rec2_per_week' => 1,
        ])->save();
        $leads[0]->fill([
            'rec_qty' => 1,
            'rec_yards' => 1.5,
            'rec_per_week' => 1
        ])->save();

        $testLead = factory(\App\Lead::class)->create([
            'rec_qty' => 1,
            'rec_yards' => 1.5,
            'rec_per_week' => 1
        ]);

        $result = $this->matcher->matchRecycle($testLead);

        $this->assertInstanceOf(\App\Bid::class, $result);
    }
}
