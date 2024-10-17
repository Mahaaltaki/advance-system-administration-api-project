<?php

namespace App\Http\Services;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Resources\RoleResource;
use Illuminate\Http\Request;



class RoleService
{
    /*
     * @param Request $request 
     * @return array containing paginated role resources.
     */
    public function getAllRoles(Request $request): array
    {
        // query builder instance for the role model
        $query = Role::query();
        // Paginate the results
        $roles = $query->paginate(10);

        // Return the paginated roles wrapped in a roleResource collection
        return RoleResource::collection($roles)->toArray(request());
    }

    /**
     * Store a new role.
     * @param array $data array containing 'id', 'name', 'description'.
     * @return array array containing the created role resource.
     * @throws \Exception
     * Throws an exception if the role creation fails */
    public function storeRole(array $data): array
    {   
        // Create a new role
        $role = Role::create([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
        // if the role was created successfully
        if (!$role) {
            throw new \Exception('Failed to create role.');
        }

        // Return the created role as a resource
        return RoleResource::make($role)->toArray(request());
    }

    /*Retrieve a specific role by its ID.
     * @param int $id of the role.
     * @return array containing the role resource.
     * @throws \Exception exception if the role is not found.*/
    public function showRole(int $id): array
    {
        // Find role by ID
        $role = Role::find($id);
        // If role is not found, throw an exception
        if (!$role) {
            throw new \Exception('role not found.');
        }

        // Return the found role
        return RoleResource::make($role)->toArray(request());
    }

    /**
     * Update an role.
     * @param role $role
     * update The role model.
     * @param array $data array containing the fields to update (name, email, password).
     * @return array containing the updated role resource.
     */
    public function updateRole(Role $role, array $data): array
{
    $role->update($data);

    return RoleResource::make($role)->toArray(request());
}


    /**
     * Delete role by ID.
     * @param int $id of role to delete.
     * @return void
     * @throws \Exception an exception if the role is not found.
     */
    public function deleteRole(int $id): void
    {
        // Find the role by ID
        $role = Role::find($id);

        // If no role is found, throw an exception
        if (!$role) {
            throw new \Exception('role not found.');
        }

        // Delete role
        $role->delete();
    }
}
