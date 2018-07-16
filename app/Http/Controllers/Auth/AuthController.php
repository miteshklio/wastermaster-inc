<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Translation\Translator as Lang;
use Illuminate\Http\Request;
use Illuminate\Validation\Factory as Validator;

class AuthController extends Controller {

    /**
     * Login user
     *
     * @param Auth $auth
     * @param Request $request
     * @param Validator $validator
     * @param Lang $lang
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function login(Auth $auth, Request $request, Validator $validator, Lang $lang)
    {
        //Validate
        $validate = $validator->make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if($validate->fails())
        {
            $messages = implode(', ', $validate->errors()->all());
            return redirect()->back()->with('message', $messages);
        }

        // Remember me
        $remember = (!empty($request->get('remember-me')) and $request->get('remember-me') === 'on') ? true : false;

        // Attempt login
        if($auth->attempt(['email' => $request->input('email'), 'password' => $request->input('password')], $remember))
        {
            return redirect($request->session()->pull('redirect', '/admin/dashboard'));
        }

        //Failed. Redirect to previous page
        return redirect()->back()->with('message', $lang->get('messages.authFailed'));
    }

    /**
     * Logout user
     *
     * @param Auth $auth
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function logout(Auth $auth, Request $request)
    {
        // If not logged in, redirect them to homepage
        if(!$auth->check()) {
            return redirect('/');
        }

        // Logout and destroy session
        $auth->logout();
        $request->session()->flush();
        return redirect('/');
    }

}
