<?php

use Illuminate\Database\Seeder;

class TestServiceAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('service_area')->insert([

            // Create admin user
            [
                'name' => 'Area A',
            ],

            // Create Regular User
            [
                'name' => 'Area B',
            ]

        ]);
    }
}
