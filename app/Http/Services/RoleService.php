<?php

namespace App\Http\Services;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Resources\RoleResource;
use App\Http\Requests\RoleRequest;
use Illuminate\Support\Facades\Log;



class RoleService
{
    /*
     * @param Request $request 
     * @return array containing paginated role resources.
     */
    public function getAllRoles(Request $request)
    { try{
        // query builder instance for the role model with permission ,paginate the result
        return Role::with('permissions')->paginate(5);
        }catch(\Exception $e){
        Log::error('Faild proccess:'.$e->getMessage());
        throw new \Exception('error in server');
        }}

    /**
     * Store a new role.
     * @param array $data array containing 'id', 'name', 'description'.
     * @return array array containing the created role resource.
     * @throws \Exception
     * Throws an exception if the role creation fails */
    public function storeRole(array $data,array $idPermission): array
    {   try{
        // Create a new role
        $role = Role::create([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);
        $role->permission()->grantPermissions($idPermission);
        $role->load('permission');
        // if the role was created successfully
        if (!$role) {
            throw new \Exception('Failed to create role.');
        }

        // Return the created role as a resource
        return $role;
    }catch(\Exception $e){
        throw new \Exception('error in server');
    }}

    /*Retrieve a specific role by its ID.
     * @param int $id of the role.
     * @return array containing the role resource.
     * @throws \Exception exception if the role is not found.*/
    public function showRole(int $id): array
    {try{
        // Find role by ID
        $role = Role::with('permission')->findOrFailfind($id);
        // If role is not found, throw an exception
        if (!$role) {
            throw new \Exception('role not found.');
        }

        // Return the found role
        return $role;}catch(\Exception $e){
            throw new \Exception("Error Processing Request", 1);
            
        }
    }

    /**
     * Update an role.
     * @param role $role
     * update The role model.
     * @param array $data array containing the fields to update (name, email, password).
     * @return array containing the updated role resource.
     */
    public function updateRole(string $id,array $data): array
{try{
    $role=Role::findOrFail($id);
    $role->update(array_filter($data));

    return $role;}catch(\Exception $e){
        throw new \Exception("Error Processing Request", 1);
        
    }
}


    /**
     * Delete role by ID.
     * @param int $id of role to delete.
     * @return void
     * @throws \Exception an exception if the role is not found.
     */
    public function deleteRole(int $id): void
    {try{
        // Find the role by ID
        $role = Role::findOrFail($id);

        // If no role is found, throw an exception
        if (!$role) {
            throw new \Exception('role not found.');
        }

        // Delete role
        return $role->delete();
    }catch(\Exception $e){
        throw new \Exception("Error Processing Request", 1);
        
    }
    }
    public function listsOfRolesDeleted($id){
        try{
            $r=Role::onlyTrashed()->paginate(4);
            return $r;
        }catch(\Exception $e){
            throw new \Exception("Error Processing Request", 1);
            
        }
    }
}
