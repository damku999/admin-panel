<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected UserService $userService;
    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock repository to avoid database interaction
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        
        $this->userService = new UserService(
            $this->userRepository
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_users_returns_paginated_data()
    {
        $request = new Request([
            'search' => 'John',
            'role' => 'admin',
            'status' => 1
        ]);

        $expectedPaginator = Mockery::mock(LengthAwarePaginator::class);
        
        $this->userRepository
            ->shouldReceive('getPaginated')
            ->once()
            ->with($request)
            ->andReturn($expectedPaginator);

        $result = $this->userService->getUsers($request);

        $this->assertSame($expectedPaginator, $result);
    }

    public function test_create_user_hashes_password()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'roles' => ['admin']
        ];
        
        $user = Mockery::mock(User::class);
        $user->shouldReceive('assignRole')->once()->with(['admin']);
        
        // Mock Hash facade
        Hash::shouldReceive('make')
            ->once()
            ->with('password123')
            ->andReturn('$2y$10$hashedpassword');
        
        $this->userRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => '$2y$10$hashedpassword',
                'roles' => ['admin']
            ])
            ->andReturn($user);

        $result = $this->userService->createUser($userData);

        $this->assertSame($user, $result);
    }

    public function test_update_user_hashes_new_password()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('syncRoles')->once()->with(['admin']);
        
        $updateData = [
            'name' => 'Updated John',
            'email' => 'john.updated@example.com',
            'password' => 'newpassword123',
            'roles' => ['admin']
        ];
        
        Hash::shouldReceive('make')
            ->once()
            ->with('newpassword123')
            ->andReturn('$2y$10$newhashedpassword');
        
        $this->userRepository
            ->shouldReceive('update')
            ->once()
            ->with($user, [
                'name' => 'Updated John',
                'email' => 'john.updated@example.com',
                'password' => '$2y$10$newhashedpassword',
                'roles' => ['admin']
            ])
            ->andReturn($user);

        $result = $this->userService->updateUser($user, $updateData);

        $this->assertSame($user, $result);
    }

    public function test_update_user_without_password_change()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('syncRoles')->once()->with(['admin']);
        
        $updateData = [
            'name' => 'Updated John',
            'email' => 'john.updated@example.com',
            'roles' => ['admin']
        ];
        
        $this->userRepository
            ->shouldReceive('update')
            ->once()
            ->with($user, [
                'name' => 'Updated John',
                'email' => 'john.updated@example.com',
                'roles' => ['admin']
            ])
            ->andReturn($user);

        $result = $this->userService->updateUser($user, $updateData);

        $this->assertSame($user, $result);
    }

    public function test_delete_user()
    {
        $user = Mockery::mock(User::class);
        
        $this->userRepository
            ->shouldReceive('delete')
            ->once()
            ->with($user)
            ->andReturn(true);

        $result = $this->userService->deleteUser($user);

        $this->assertTrue($result);
    }

    public function test_get_active_users()
    {
        $expectedCollection = new Collection([new User(['id' => 1, 'name' => 'John'])]);
        
        $this->userRepository
            ->shouldReceive('getActive')
            ->once()
            ->andReturn($expectedCollection);

        $result = $this->userService->getActiveUsers();

        $this->assertSame($expectedCollection, $result);
    }

    public function test_update_status()
    {
        $this->userRepository
            ->shouldReceive('updateStatus')
            ->once()
            ->with(1, 1)
            ->andReturn(true);

        $result = $this->userService->updateStatus(1, 1);

        $this->assertTrue($result);
    }

    public function test_assign_roles()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('syncRoles')->once()->with(['admin', 'editor']);

        $this->userService->assignRoles($user, ['admin', 'editor']);
    }

    public function test_change_password()
    {
        $user = Mockery::mock(User::class);
        
        Hash::shouldReceive('make')
            ->once()
            ->with('newpassword')
            ->andReturn('$2y$10$newhashedpassword');
        
        $this->userRepository
            ->shouldReceive('updatePassword')
            ->once()
            ->with($user, '$2y$10$newhashedpassword')
            ->andReturn(true);

        $result = $this->userService->changePassword($user, 'newpassword');

        $this->assertTrue($result);
    }

    public function test_get_user_with_roles()
    {
        $user = new User(['id' => 1, 'name' => 'John']);
        
        $this->userRepository
            ->shouldReceive('findWithRoles')
            ->once()
            ->with(1)
            ->andReturn($user);

        $result = $this->userService->getUserWithRoles(1);

        $this->assertSame($user, $result);
    }
}