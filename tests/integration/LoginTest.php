<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginTest extends IntegrationTestCase {

    /**
     * @group single
     */
    public function testAdminLoginSuccess()
    {
        // Admin Login
        $response = $this
            ->withSession(['redirect' => '/admin/dashboard'])
            ->call('POST', '/login', [
                'email' => 'boss@admin.com',
                'password' => 'bosspass'
            ]);

        $this->assertEquals(302, $response->status());
        $this->assertRedirectedTo('/admin/dashboard');

        // Check admin perms
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->isAdmin);

        // View Admin Page
        $response = $this->call('GET', '/admin/users');
        $this->assertEquals(200, $response->status());

        // Admin Logout
        $response = $this->call('GET', '/logout');
        $this->assertEquals(302, $response->status());
        $this->assertFalse(Auth::check());
    }

    public function testUserLoginSuccess()
    {
        // User Login
        $response = $this
            ->withSession(['redirect' => '/'])
            ->call('POST', '/login', [
                'email' => 'regular@jones.com',
                'password' => 'regularpass'
            ]);

        $this->assertEquals(302, $response->status());
        $this->assertRedirectedTo('/');

        // Check perms
        $this->assertTrue(Auth::check());
        $this->assertFalse(Auth::user()->isAdmin);

        // Admin Logout
        $response = $this->call('GET', '/logout');
        $this->assertEquals(302, $response->status());
        $this->assertFalse(Auth::check());
    }

    public function testUserLoginFails()
    {
        // User Login bad password
        $response = $this
            ->call('POST', '/login', [
                'email' => 'regular@jones.com',
                'password' => 'regularpass1'
            ]);

        $this->assertEquals(302, $response->status());
        $this->assertEquals("Sorry, your username and password don't match our records.", Session::get('message'));
        $this->assertFalse(Auth::check());

        // User Login bad email
        $response = $this
            ->call('POST', '/login', [
                'email' => 'regulars@jones.com',
                'password' => 'regularpass'
            ]);

        $this->assertEquals(302, $response->status());
        $this->assertEquals("Sorry, your username and password don't match our records.", Session::get('message'));
        $this->assertFalse(Auth::check());

        // User Login no email
        $response = $this
            ->call('POST', '/login', [
                'password' => 'regularpass1'
            ]);

        $this->assertEquals(302, $response->status());
        $this->assertEquals("The email field is required.", Session::get('message'));
        $this->assertFalse(Auth::check());

        // User Login no pass
        $response = $this
            ->call('POST', '/login', [
                'email' => 'regular@jones.com'
            ]);

        $this->assertEquals(302, $response->status());
        $this->assertEquals("The password field is required.", Session::get('message'));
        $this->assertFalse(Auth::check());

        // User login bad email
        $response = $this
            ->call('POST', '/login', [
                'email' => 'regular@jones',
                'password' => 'regularpass'
            ]);

        $this->assertEquals(302, $response->status());
        $this->assertEquals("The email must be a valid email address.", Session::get('message'));
        $this->assertFalse(Auth::check());

        // User login bad password
        $response = $this
            ->call('POST', '/login', [
                'email' => 'regular@jones.com',
                'password' => 'reg'
            ]);

        $this->assertEquals(302, $response->status());
        $this->assertEquals("The password must be at least 8 characters.", Session::get('message'));
        $this->assertFalse(Auth::check());
    }

}
