<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use App\Http\Services\RoleService;
use Illuminate\Support\Facades\Log;
use App\Http\Trait\ApiResponceTrait;
use Symfony\Component\HttpFoundation\JsonResponse;

class RoleController extends Controller
{
    protected $roleService;
    use ApiResponceTrait;

    /**
     * Display a listing of the resource.
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try{
            $roles=$this->roleService->getAllRoles($request);
            return $this->successResponse($roles, 'bring all roles successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, ' error with bring all roles.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        try{
            $validatedRequest=$request->validated();
            $role=$this->roleService->storeRole($validatedRequest);
            return $this->successResponse($role,'role stored successfuly',201);
        }catch(\Exception $e){
            return $this->handleException($e, ' error with stored role',);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
        
            $role=$this->roleService->showRole($id);
            return $this->successResponse($role,'the role has been showing successfuly',200);
        }catch(\Exception $e){
         return $this->handleException($e,'error with showing the role');
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
{
    try {
        $role = Role::findOrFail($id);
        
        $validated = $request->validated();
        $updatedRole = $this->roleService->updateRole($role, $validated);

        return $this->successResponse($updatedRole, 'Role updated successfully', 200);
    } catch (\Exception $e) {
        return $this->handleException($e, 'Error updating role');
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id):JsonResponse
    {
         try {
            $this->roleService->deleteRole($id);
            return $this->successResponse([], 'the role deleted successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'error with deleting the role');
        }
    }
    /**
     * Handle exceptions and show a response.
     */
    protected function handleException(\Exception $e, string $message): JsonResponse
    {
        // Log the error with additional context if needed
        Log::error($message, ['exception' => $e->getMessage(), 'request' => request()->all()]);

        return $this->errorResponse($message, [$e->getMessage()], 500);
    }
}
