<?php 

use WasteMaster\v1\Users\Manage;
use Mockery as m;

class UserManageTest extends UnitTestCase {

    protected $user;
    protected $userRole;

    /**
     * @var Manage
     */
    protected $users;

    public function setUp() 
    {
        $this->user = m::mock('\App\User');
        $this->userRole = m::mock('\App\UserRole');

        $this->users = new Manage($this->user, $this->userRole);

        parent::setUp();
    }

    public function tearDown() 
    {
        m::close();
        parent::tearDown();
    }

    public function testGetUserSuccess()
    {
        $this->user->shouldReceive('find')
            ->once()
            ->andReturn((object) [
                'id' => 1,
            ]);

        $user = $this->users->setUser(1)->get();
        $this->assertEquals(1, $user->id);
    }

    /**
     * @expectedException WasteMaster\v1\Users\UserNotFound
     */
    public function testGetUserNotFoundFails()
    {
        $this->user->shouldReceive('find')
            ->once()
            ->andReturn();

        $this->users->setUser(1)->get();
    }

    public function testCreateUserSuccess()
    {
        $this->userRole->shouldReceive('where->first')
            ->once()
            ->andReturn((object) [
                'id' => 1,
            ]);

        $this->user->shouldReceive('where->first')
            ->once()
            ->andReturn();

        $this->user->shouldReceive('create')
            ->once()
            ->andReturn((object) [
                'id' => 1,
            ]);

        $user = $this->users->setRole('User')
            ->buildUser([
                'name' => 'Terry Harmon',
                'email' => 'tharmon0717@gmail.com',
                'password' => 'supersecret',
            ])
            ->create();

        $this->assertEquals(1, $user->id);
    }

    /**
     * @expectedException WasteMaster\v1\Users\UserExists
     */
    public function testCreateUserAlreadyExistsFails()
    {
        $this->userRole->shouldReceive('where->first')
            ->once()
            ->andReturn((object) [
                'id' => 1,
            ]);

        $this->user->shouldReceive('where->first')
            ->once()
            ->andReturn((object) [
                'id' => 1
            ]);

        $this->users->setRole('User')
            ->buildUser([
                'name' => 'Terry Harmon',
                'email' => 'tharmon0717@gmail.com',
                'password' => 'supersecret',
            ])
            ->create();
    }

    /**
     * @expectedException WasteMaster\v1\Users\UserRoleNotFound
     */
    public function testCreateUserRoleDoesNotExistsFails() 
    {
        $this->userRole->shouldReceive('where->first')
            ->once()
            ->andReturn();

        $this->users->setRole('User')
            ->buildUser([
                'name' => 'Terry Harmon',
                'email' => 'tharmon0717@gmail.com',
                'password' => 'supersecret',
            ])
            ->create();
    }

    public function testUpdateUserSuccess()
    {
        $this->userRole->shouldReceive('where->first')
            ->once()
            ->andReturn((object) [
                'id' => 1,
            ]);

        $this->user->shouldReceive('find->update')
            ->once()
            ->andReturn((object) [
                'id' => 1
            ]);

        $user = $this->users->setUser(1)
            ->setRole('User')
            ->buildUser([
                'name' => 'Terry Harmon',
                'email' => 'tharmon0717@gmail.com',
                'password' => 'supersecret',
            ])
            ->update();

        $this->assertEquals(1, $user->id);
    }

    /**
     * @expectedException WasteMaster\v1\Users\UserNotFound
     */
    public function testUpdateUserNotFoundFails()
    {
        $this->user->shouldReceive('find')
            ->once()
            ->andReturn();

        $this->users->setUser(1)
            ->setRole('User')
            ->buildUser([
                'name' => 'Terry Harmon',
                'email' => 'tharmon0717@gmail.com',
                'password' => 'supersecret',
            ])
            ->update();
    }

    public function testDeleteUserSuccess()
    {
        $this->user->shouldReceive('find->delete')
            ->once()
            ->andReturn(true);

        $user = $this->users->setUser(1)->delete();
        $this->assertTrue($user);
    }

    /**
     * @expectedException WasteMaster\v1\Users\UserNotFound
     */
    public function testDeleteUserNotFoundFails()
    {
        $this->user->shouldReceive('find')
            ->once()
            ->andReturn();

        $this->users->setUser(1)->delete();
    }

}
