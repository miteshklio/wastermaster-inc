<?php

class LeadTest extends IntegrationTestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $model = app('App\User');
        $this->user = $model->find(1);
    }

    public function insertLead($status = \App\Lead::NEW)
    {
        \DB::table('leads')->insert([
            'company' => 'Company A',
            'address' => '123 That Street',
            'service_area_id' => 1,
            'contact_name' => 'Fred Durst',
            'contact_email' => 'fred.durst@example.com',
            'account_num' => '123abc',
            'hauler_id' => 1,
            'msw_qty' => 1,
            'msw_yards' => 2,
            'msw_per_week' => 3,
            'rec_qty' => 4,
            'rec_yards' => 5,
            'rec_per_week' => 6,
            'notes' => 'A grand scheme',
            'monthly_price' => 200,
            'archived' => 0,
            'bid_count' => 0,
            'status' => $status
        ]);

        return \DB::table('leads')
            ->where('company', 'Company A')
            ->first();
    }

    protected function insertBid(int $leadID)
    {
        \DB::table('bids')->insert([
            'hauler_id' => 2,
            'hauler_email' => 'hauler@example.com',
            'lead_id' => $leadID,
            'status' => \App\Bid::STATUS_ACCEPTED,
            'net_monthly' => 100,
            'msw_price' => 0,
            'rec_price' => 0,
            'rec_offset' => 0,
            'fuel_surcharge' => 0,
            'env_surcharge' => 0,
            'recovery_fee' => 0,
            'admin_fee' => 0,
            'other_fees' => 0,
            'gross_profit' => 50,
            'notes' => 'something'
        ]);

        return \DB::table('leads')
            ->where('lead_id', $leadID)
            ->where('status', \App\Bid::STATUS_ACCEPTED)
            ->first();
    }

    public function testCanCreateLead()
    {
        $this->actingAs($this->user)
            ->visit(route('leads::home'))
            ->see('Manage Leads')
            ->click('New Lead')
            ->see('Create New Lead')
            ->type('Company A', 'company')
            ->type('123 That Street', 'address')
            ->select(1, 'service_area_id')
            ->type('Fred Durst', 'contact_name')
            ->type('fred.durst@example.com', 'contact_email')
            ->type('123abc', 'account_num')
            ->select(1, 'hauler_id')
            ->type(1, 'msw_qty')
            ->type(2, 'msw_yards')
            ->type(3, 'msw_per_week')
            ->type(4, 'rec_qty')
            ->type(5, 'rec_yards')
            ->type(6, 'rec_per_week')
            ->type('A grand scheme', 'notes')
            ->type(200, 'monthly_price')
            ->press('submit')
            ->see('Update Lead')
            ->seeInDatabase('leads', [
                'company' => 'Company A',
                'address' => '123 That Street',
                'service_area_id' => 1,
                'contact_name' => 'Fred Durst',
                'contact_email' => 'fred.durst@example.com',
                'account_num' => '123abc',
                'hauler_id' => 1,
                'msw_qty' => 1,
                'msw_yards' => 2,
                'msw_per_week' => 3,
                'rec_qty' => 4,
                'rec_yards' => 5,
                'rec_per_week' => 6,
                'notes' => 'A grand scheme',
                'monthly_price' => 200,
                'archived' => 0,
                'bid_count' => 0
            ]);
    }

    public function testCanEditLead()
    {
        $lead = $this->insertLead();

        $this->actingAs($this->user)
            ->visit(route('leads::show', ['id' => $lead->id]))
            ->see('Update Lead')
            ->select(2, 'service_area_id')
            ->press('submit')
            ->see('Update Lead')
            ->seeInDatabase('leads', [
                'company' => 'Company A',
                'service_area_id' => 2
            ]);
    }

    /**
     * @group single
     */
    public function testCanConvertToClient()
    {
        $lead = $this->insertLead(\App\Lead::BID_ACCEPTED);
        $bid  = $this->insertBid($lead->id);

        $this->actingAs($this->user)
            ->visit(route('leads::home'))
            ->click('Convert')
            ->see('Update Client')
            ->seeInDatabase('leads', [
                'id' => $lead->id,
                'status' => \App\Lead::CONVERTED_TO_CLIENT
            ])
            ->seeInDatabase('clients', [
                'lead_id' => $lead->id,
                'company' => 'Company A',
                'address' => '123 That Street',
                'service_area_id' => 1,
                'contact_name' => 'Fred Durst',
                'contact_email' => 'fred.durst@example.com',
                'account_num' => '123abc',
                'hauler_id' => 2,
                'msw_qty' => 1,
                'msw_yards' => 2,
                'msw_per_week' => 3,
                'rec_qty' => 4,
                'rec_yards' => 5,
                'rec_per_week' => 6,
            ]);
    }

}
