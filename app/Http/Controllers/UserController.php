<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Contracts\Services\UserServiceInterface;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private UserServiceInterface $userService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['delete']]);
    }


    /**
     * List User
     * @param Request $request
     * @return View
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $users = $this->userService->getUsers($request);
        return view('users.index', ['users' => $users]);
    }

    /**
     * Create User
     * @return View
     * @author Darshan Baraiya
     */
    public function create()
    {
        $roles = $this->userService->getRoles();
        return view('users.add', ['roles' => $roles]);
    }

    /**
     * Store User
     * @param Request $request
     * @return View Users
     * @author Darshan Baraiya
     */
    public function store(Request $request)
    {
        // Validations using service
        $validationRules = $this->userService->getStoreValidationRules();
        $request->validate($validationRules, [
            'new_password.regex' => 'The new password format is invalid. It must contain at least one number, one special character, one uppercase letter, one lowercase letter, and be between 8 and 16 characters long.',
        ]);

        try {
            // Create user through service
            $user = $this->userService->createUser($request->all());

            // Assign roles through service
            $this->userService->assignRoles($user, [$request->role_id]);

            return redirect()->route('users.index')->with('success', 'User Created Successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Update Status Of User
     * @param Integer $user_id
     * @param Integer $status
     * @return List Page With Success
     * @author Darshan Baraiya
     */
    public function updateStatus($user_id, $status)
    {
        // Validation
        $validate = Validator::make([
            'user_id'   => $user_id,
            'status'    => $status
        ], [
            'user_id'   =>  'required|exists:users,id',
            'status'    =>  'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return redirect()->back()->with('error', $validate->errors()->first());
        }

        try {
            // Update status through service
            $this->userService->updateStatus($user_id, $status);

            return redirect()->back()->with('success', 'User Status Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Edit User
     * @param User $user
     * @return View
     * @author Darshan Baraiya
     */
    public function edit(User $user)
    {
        $roles = $this->userService->getRoles();
        return view('users.edit')->with([
            'roles' => $roles,
            'user'  => $user
        ]);
    }

    /**
     * Update User
     * @param Request $request
     * @param User $user
     * @return View Users
     * @author Darshan Baraiya
     */
    public function update(Request $request, User $user)
    {
        // Validations using service
        $validationRules = $this->userService->getUpdateValidationRules($user);
        $request->validate($validationRules);

        // Check if new password is not empty in the request
        if (!empty($request->input('new_password'))) {
            $passwordRules = $this->userService->getPasswordValidationRules();
            $customMessages = [
                'new_password.regex' => 'The new password format is invalid. It must contain at least one number, one special character, one uppercase letter, one lowercase letter, and be between 8 and 16 characters long.',
            ];

            // Perform the validation
            $validator = Validator::make($request->all(), $passwordRules, $customMessages);

            // Check if validation fails
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        try {
            // Update user through service
            $this->userService->updateUser($user, $request->all());

            // Assign roles through service
            $this->userService->assignRoles($user, [$request->role_id]);

            // Handle password change if provided
            if (!empty($request->input('new_password'))) {
                $this->userService->changePassword($user, $request->new_password);
            }

            return redirect()->back()->with('success', 'User Updated Successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Delete User
     * @param User $user
     * @return Index Users
     * @author Darshan Baraiya
     */
    public function delete(User $user)
    {
        try {
            // Delete user through service
            $this->userService->deleteUser($user);

            return redirect()->back()->with('success', 'User Deleted Successfully!.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Import Users
     * @return View
     */
    public function importUsers()
    {
        return view('users.import');
    }

    /**
     * Upload Users
     * @param Request $request
     * @return Redirect
     */
    public function uploadUsers(Request $request)
    {
        Excel::import(new UsersImport, $request->file);

        return redirect()->back()->with('success', 'User Imported Successfully');
    }

    /**
     * Export Users
     * @return BinaryFileResponse
     */
    public function export()
    {
        return $this->userService->exportUsers();
    }
}
