<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Development Seeds
        if(env('APP_ENV') == 'local') {
            $this->call(DevUserSeeder::class);
        }

        // Testing Seeds
        if(env('APP_ENV') == 'testing') {
            $this->call(TestUserSeeder::class);
            $this->call(TestHaulerSeeder::class);
            $this->call(TestServiceAreaSeeder::class);
        }
    }
}
