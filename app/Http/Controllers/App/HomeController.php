<?php namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard as Auth;

class HomeController extends Controller {

    /**
     * Home page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function index(Auth $auth)
    {
        // Already logged in? Take use home
        if ($auth->check())
        {
            return redirect('/admin/dashboard');
        }

        return view('app.accounts.login');
    }

}
