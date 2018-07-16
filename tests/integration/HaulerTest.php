<?php

class HaulerTest extends IntegrationTestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $model = app('App\User');
        $this->user = $model->find(1);
    }

    public function insertHauler()
    {
        \DB::table('haulers')->insert([
            'name' => 'Hauler A',
            'service_area_id' => 1,
            'svc_recycle' => 1,
            'svc_waste' => 1,
            'emails' => serialize(['foo@example.com']),
            'archived' => 0
        ]);

        return \DB::table('haulers')
            ->where('name', 'Hauler A')
            ->first();
    }


    public function testCanCreateHauler()
    {
        $this->actingAs($this->user)
            ->visit(route('haulers::home'))
            ->see('Manage Haulers')
            ->click('New Hauler')
            ->see('Create New Hauler')
            ->type('New Hauler A', 'name')
            ->select(1, 'service_area_id')
            ->check('recycle')
            ->check('waste')
            ->type('foo@example.com', 'emails')
            ->press('Create Hauler')
            ->see('Manage Haulers')
            ->seeInDatabase('haulers', [
                'name' => 'New Hauler A',
                'service_area_id' => 1,
            ]);
    }

    public function testCanEditHauler()
    {
        $lead = $this->insertHauler();

        $this->actingAs($this->user)
            ->visit(route('haulers::show', ['id' => $lead->id]))
            ->see('Update Hauler')
            ->select(2, 'service_area_id')
            ->press('Save Hauler')
            ->see('Manage Haulers')
            ->seeInDatabase('haulers', [
                'name' => 'Hauler A',
                'service_area_id' => 2
            ]);
    }

}
