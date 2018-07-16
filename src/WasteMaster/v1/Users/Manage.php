<?php 

namespace WasteMaster\v1\Users;

use App\User;
use App\UserRole;

class Manage {

    /**
     * @var User
     */
    protected $users;

    /**
     * @var UserRole
     */
    protected $roles;

    /**
     * @var object
     */
    protected $user;

    /**
     * @var array
     */
    protected $newUser;

    /**
     * @var object
     */
    protected $role;

    /**
     * @param User $u
     * @param UserRole $r
     */
    public function __construct(User $u, UserRole $r)
    {
        $this->users = $u;
        $this->roles = $r;
    }

    /**
     * Set user
     *
     * @param int $id
     * @return $this
     */
    public function setUser(int $id)
    {
        $user = $this->users->find($id);

        if(empty($user)) {
            throw new UserNotFound(trans('messages.userNotFound'), 404);
        }

        $this->user = $user;
        return $this;
    }

    /**
     * Build new user
     *
     * @return $this
     */
    public function buildUser(array $user)
    {
        $this->newUser = array_filter($user);

        // Set role
        if(!empty($this->role)) {
            $this->newUser['role_id'] = $this->role->id;
        }

        // Set password
        if(isset($this->newUser['password'])) {
            $this->newUser['password'] = bcrypt($user['password']);
        }
        
        return $this;
    }

    /**
     * Get user
     *
     * @return object
     */
    public function get()
    {
        return $this->user;
    }

    /**
     * Set user role
     *
     * @param string $name
     * @return $this
     */
    public function setRole(string $name)
    {
        $role = $this->roles->where('name', $name)->first();

        if(empty($role)) {
            throw new UserRoleNotFound(trans('messages.userRoleNotFound'), 404);
        }

        $this->role = $role;
        return $this;
    }

    /**
     * Create new user
     *
     * @return object
     */
    public function create()
    {
        // Check if user exists
        $existing = $this->users->where('email', $this->newUser['email'])->first();

        if(!empty($existing)) {
            throw new UserExists(trans('messages.userExists', [
                'email' => $this->newUser['email']
            ]), 400);
        }

        return $this->users->create($this->newUser);
    }

    /**
     * Update user
     *
     * @return object
     */
    public function update() 
    {
        return $this->user->update($this->newUser);
    }

    /**
     * Delete user
     *
     * @return bool
     */
    public function delete()
    {
        return $this->user->delete();
    }
}
