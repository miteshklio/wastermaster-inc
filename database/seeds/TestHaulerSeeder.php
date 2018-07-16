<?php

use Illuminate\Database\Seeder;

class TestHaulerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('haulers')->insert([

            // Create admin user
            [
                'name' => 'Hauler A',
                'city_id' => 1,
                'service_area_id' => 1,
                'svc_recycle' => 1,
                'svc_waste' => 1,
                'emails' => serialize(['hauler.a@example.com']),
                'archived' => 0
            ],

            // Create Regular User
            [
                'name' => 'Hauler B',
                'city_id' => 1,
                'service_area_id' => 1,
                'svc_recycle' => 1,
                'svc_waste' => 1,
                'emails' => serialize(['hauler.b@example.com']),
                'archived' => 0
            ]

        ]);
    }
}
