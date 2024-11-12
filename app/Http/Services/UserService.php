<?php

namespace App\Http\Services;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;


class UserService
{
    /*
     * @param Request $request 
     * @return array containing paginated user resources.
     */
    public function getAllUsers(Request $request): array
{
    // Paginate the results
    $users = User::paginate(10);

    // Return the paginated users wrapped in a UserResource collection
    return UserResource::collection($users)->toArray($request);
}

    /**
     * Store a new User.
     * @param array $data array containing 'name', 'email', 'password'.
     * @return array array containing the created user resource.
     * @throws \Exception
     * Throws an exception if the user creation fails */
    public function storeUser(array $data): array
    {
        // Create a new user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], 
        ]);
        
        // if the user was created successfully
        if (!$user) {
            throw new \Exception('Failed to create user.');
        }

        // Return the created user as a resource
        return UserResource::make($user)->toArray(request());
    }

    /*Retrieve a specific user by its ID.
     * @param int $id of the user.
     * @return array containing the user resource.
     * @throws \Exception exception if the user is not found.*/
    /*Retrieve a specific user by its ID.
     * @param int $id of the user.
     * @return array containing the user resource.
     * @throws \Exception exception if the user is not found.*/
    public function showUser(int $id)
    {
        try {
            // Find user by ID or fail
            $user = User::findOrFail($id);
            if(!$user){
                throw new \Exception('user not found');
            }
            return $user;
        }
        
        catch (\Exception $e) {
            Log::error('Faild to retrive user:'.$e->getMessage());
            throw new \Exception('User not found.');
        }

    }
    

    /**
     * Update an user.
     * @param User $user
     * update The user model.
     * @param array $data array containing the fields to update (name, email, password).
     * @return array containing the updated user resource.
     */
    public function updateUser(User $user, array $data): array
    {
        $user->update(array_filter([
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'password' => $data['password'] ?? $user->password,
        ]));
    
        return UserResource::make($user)->toArray(request());
    }
    

    /**
     * Delete user by ID.
     * @param int $id of user to delete.
     * @return void
     * @throws \Exception an exception if the user is not found.
     */
    public function deleteUser(int $id): void
{
    // Find the user by ID
    $user = User::find($id);

    // Detach role (roles instead of role)
    $user->roles()->detach($user->role_id);

    // If no user is found, throw an exception
    if (!$user) {
        throw new \Exception('user not found.');
    }

    // Delete user
    $user->delete();
}
    public function addRoleToUser(int $id,array $roles)
{try{
    // Find the user by ID
    $user = User::findOrFail($id);

    // Attach the role (roles instead of role)
    $user->grantRole($roles);
    $user->load('roles.permission');
}catch(\Exception $e){
    Log::error('faild in grant roles to User: '.$e->getMessage());
    throw  new \Exception('error in server');

}

}
}
