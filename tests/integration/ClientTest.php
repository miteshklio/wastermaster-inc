<?php

class ClientTest extends IntegrationTestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $model = app('App\User');
        $this->user = $model->find(1);
    }

    public function insertClient(int $leadID = 1)
    {
        \DB::table('clients')->insert([
            'lead_id' => $leadID,
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
            'prior_total' => 300,
            'msw_price' => 75,
            'rec_price' => 50,
            'rec_offset' => 0,
            'fuel_surcharge' => 0,
            'env_surcharge' => 0,
            'recovery_fee' => 0,
            'admin_fee' => 0,
            'other_fees' => 0,
            'net_monthly' => 125,
            'gross_profit' => 50,
            'total' => 175,
            'archived' => 0
        ]);

        return \DB::table('clients')
            ->where('company', 'Company A')
            ->first();
    }

    protected function insertLead($status = \App\Lead::NEW)
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

    public function testCanCreateClient()
    {
        $this->actingAs($this->user)
            ->visit(route('clients::home'))
            ->see('Manage Clients')
            ->click('New Client')
            ->see('Create New Client')
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
            ->type(300, 'prior_total')
            ->type(75, 'msw_price')
            ->type(50, 'rec_price')
            ->type(125, 'net_monthly')
            ->type(0, 'rec_offset')
            ->type(0, 'fuel_surcharge')
            ->type(0, 'env_surcharge')
            ->type(0, 'recovery_fee')
            ->type(0, 'admin_fee')
            ->type(0, 'other_fees')
            ->type(50, 'gross_profit')
            ->type(175, 'total')
            ->press('Save Client')
            ->see('Manage Clients')
            ->seeInDatabase('clients', [
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
                'prior_total' => 300,
                'msw_price' => 75,
                'rec_price' => 50,
                'net_monthly' => 125,
                'archived' => 0,
            ]);
    }

    public function testCanEditClient()
    {
        $lead = $this->insertClient();

        $this->actingAs($this->user)
            ->visit(route('clients::show', ['id' => $lead->id]))
            ->see('Update Client')
            ->select(2, 'service_area_id')
            ->press('Save Client')
            ->see('Update Client')
            ->seeInDatabase('clients', [
                'company' => 'Company A',
                'service_area_id' => 2
            ]);
    }

    public function testRebidClientWithExistingLead()
    {
        $lead   = $this->insertLead();
        $client = $this->insertClient($lead->id);

        $this->actingAs($this->user)
            ->visit(route('clients::home'))
            ->click('Rebid')
            ->seeInDatabase('leads', [
                'id' => $lead->id,
                'status' => \App\Lead::REBIDDING,
                'service_area_id' => $client->service_area_id
            ]);
    }

}
