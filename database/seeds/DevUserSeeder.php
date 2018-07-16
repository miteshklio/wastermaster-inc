<?php

use Illuminate\Database\Seeder;

class DevUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = DB::table('user_roles')->where('name', 'Admin')->first();

        DB::table('users')->insert([
            [
                'name' => env('DEV_NAME'),
                'email' => env('DEV_EMAIL'),
                'password' => bcrypt(env('DEV_PASS')),
                'role_id' => $role->id
            ]
        ]);
    }
}
