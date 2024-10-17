<?php

namespace App\Http\Services;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Resources\PermissionResource;



class PermissionService
{
    /*
     * @param Request $request 
     * @return array containing paginated permission resources.
     */
    public function getAllpermissions(Request $request): array
    {
        // query builder instance for the permission model
        $query = Permission::query();
        // Paginate the results
        $permissions = $query->paginate(10);

        // Return the paginated permissions wrapped in a permissionResource collection
        return PermissionResource::collection($permissions)->toArray(request());
    }

    /**
     * Store a new permission.
     * @param array $data array containing 'id', 'name', 'description'.
     * @return array array containing the created permission resource.
     * @throws \Exception
     * Throws an exception if the permission creation fails */
    public function storepermission(array $data): array
    {
        // Create a new permission
        $permission = Permission::create([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        
        // if the permission was created successfully
        if (!$permission) {
            throw new \Exception('Failed to create permission.');
        }

        // Return the created permission as a resource
        return PermissionResource::make($permission)->toArray(request());
    }

    /*Retrieve a specific permission by its ID.
     * @param int $id of the permission.
     * @return array containing the permission resource.
     * @throws \Exception exception if the permission is not found.*/
    public function showpermission(int $id): array
    {
        // Find permission by ID
        $permission = Permission::find($id);
        // If permission is not found, throw an exception
        if (!$permission) {
            throw new \Exception('permission not found.');
        }

        // Return the found permission
        return PermissionResource::make($permission)->toArray(request());
    }

    /**
     * Update an permission.
     * @param permission $permission
     * update The permission model.
     * @param array $data array containing the fields to update (name, email, password).
     * @return array containing the updated permission resource.
     */
    public function updatepermission(Permission $permission, array $data): array
    {
        // Update only the fields that are provided in the data array
        $permission->update(array_filter([
            'name' => $data['name'] ?? $permission->name,
            'description' => $data['description'] ?? $permission->description,
        ]));
        // Update the pivot table for roles if role_id is provided
        if (isset($data['role_id'])) {
            $permission->roles()->sync([$data['role_id']]);
        }
        // Return the updated permission as a resource
        return PermissionResource::make($permission)->toArray(request());
    }

    /**
     * Delete permission by ID.
     * @param int $id of permission to delete.
     * @return void
     * @throws \Exception an exception if the permission is not found.
     */
    public function deletepermission(int $id): void
    {
        // Find the permission by ID
        $permission = Permission::find($id);

        // If no permission is found, throw an exception
        if (!$permission) {
            throw new \Exception('permission not found.');
        }

        // Delete permission
        $permission->delete();
        $permission->roles()->detach($permission->role_id);
    }
    public function addPermissionToRole(Request $request ){
        $permission=Permission::query();
        $permission->role()->attach( $request['role_id']->role_id ,$request['permision_id']->permision_id);

    }
}
