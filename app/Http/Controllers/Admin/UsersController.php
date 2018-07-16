<?php namespace App\Http\Controllers\Admin;

use WasteMaster\v1\Users\Manage;
use WasteMaster\v1\Helpers\DataTable;
use WasteMaster\v1\Users\UserRoleNotFound;
use WasteMaster\v1\Users\UserNotFound;
use WasteMaster\v1\Users\UserExists;
use App\User;
use App\UserRole;
use Illuminate\Contracts\Auth\Guard as Auth;
use Illuminate\Validation\Factory as Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller {

    /**
     * Dashboard
     *
     * @param User $user
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function index(User $user)
    {
        $datatable = new DataTable($user);

        $datatable->showColumns([
                'id'         => 'ID',
                'name'       => 'Name',
                'email'      => 'Email',
                'role_id'    => 'Role',
                'created_at' => 'Joined',
            ])
            ->searchColumns(['name', 'email'])
            ->setDefaultSort('created_at', 'desc')
            ->hideOnMobile(['id', 'role_id', 'created_at'])
            ->prepare(20);

        return view('app.admin.users.index')->with([
            'datatable' => $datatable
        ]);
    }

    /**
     * Get user
     *
     * @param Manage $users
     * @param UserRole $roles
     * @param int $id
     */
    protected function get(Manage $users, UserRole $roles, int $id)
    {
        try {
            $user = $users->setUser($id)->get();
        } catch(UserNotFound $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }

        return view('app.admin.users.form')->with([
            'roles' => $roles->all(),
            'user' => $user
        ]);
    }

    /**
     * Update user
     *
     * @param Request $request
     * @param Validator $validator
     * @param Manage $users
     * @param int $id
     *
     * @return redirect
     */
    protected function update(Request $request, Validator $validator, Manage $users, int $id)
    {
        $validate = $validator->make($request->all(), [
            'name' => 'required',
            'user_email' => 'required|email',
            'pass' => 'sometimes|min:8',
            'role' => 'required|alpha_num'
        ]);

        if ($validate->fails()) {
            return redirect()->back()->with('message', implode(', ', $validate->errors()->all()));
        }

        try {
            $users->setUser($id)
                ->setRole($request->input('role'))
                ->buildUser([
                    'name' => $request->input('name'),
                    'email' => $request->input('user_email'),
                    'password' => $request->input('pass')
                ])
                ->update();

            return redirect()->back()->with('message', trans('messages.userUpdated', [
                'email' => $request->input('user_email')
            ]));
        } catch(UserNotFound $e) {
            return redirect()->back()->with('message', $e->getMessage());
        } catch(UserRoleNotFound $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    /**
     * Delete user
     *
     * @param Auth $auth
     * @param Manage $users
     * @param int $id
     * @return redirect
     */
    protected function delete(Auth $auth, Manage $users, int $id)
    {
        // Check for morons...
        if($auth->user()->id === $id) {
            return redirect()->back()->with('message', trans('messages.userDeleteYoureADummy'));
        }

        try {
            $users->setUser($id)->delete();
            return redirect()->back()->with('message', trans('messages.userDeleted'));
        } catch(UserNotFound $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    /**
     * New user form
     *
     * @param UserRole $roles
     * @return view
     */
    protected function newUser(UserRole $roles)
    {
        return view('app.admin.users.form')->with([
            'roles' => $roles->all()
        ]);
    }

    /**
     * Create new user
     *
     * @param Request $request
     * @param Validator $validator
     * @param Manage $users
     *
     * @return redirect
     */
    protected function create(Request $request, Validator $validator, Manage $users)
    {
        $validate = $validator->make($request->all(), [
            'name' => 'required',
            'user_email' => 'required|email',
            'pass' => 'required|min:8',
            'role' => 'required|alpha_num'
        ]);

        if ($validate->fails()) {
            return redirect()->back()->with('message', implode(', ', $validate->errors()->all()));
        }

        // Create user
        try {
            $users->setRole($request->input('role'))
                ->buildUser([
                    'name' => $request->input('name'),
                    'email' => $request->input('user_email'),
                    'password' => $request->input('pass')
                ])
                ->create();

            return redirect()->to('admin/users')->with('message', trans('messages.userCreated', [
                'email' => $request->input('user_email')
            ]));
        } catch(UserExists $e) {
            return redirect()->back()->with('message', $e->getMessage());
        } catch(UserRoleNotFound $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

}
