<?php

use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([

            // Create admin user
            [
                'name' => 'Admin Jones',
                'email' => 'boss@admin.com',
                'password' => bcrypt('bosspass'),
                'role_id' => 2
            ],

            // Create Regular User
            [
                'name' => 'Regular Jones',
                'email' => 'regular@jones.com',
                'password' => bcrypt('regularpass'),
                'role_id' => 1
            ]

        ]);
    }
}
