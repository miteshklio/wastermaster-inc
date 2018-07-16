<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\User;

class AddInitialAdminUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $role = DB::table('user_roles')->where('name', 'Admin')->first();

        User::create([
            'name' => 'Terry Harmon',
            'email' => 'terry@vaultinnovation.com',
            'password' => bcrypt(env('ADMIN_PASS')),
            'role_id' => $role->id
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')->where('id', 1)->delete();
    }
}
