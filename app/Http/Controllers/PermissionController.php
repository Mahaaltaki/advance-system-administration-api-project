<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Trait\ApiResponceTrait;
use App\Http\Requests\PermissionRequest;
use App\Http\Services\PermissionService;
use Symfony\Component\HttpFoundation\JsonResponse;

class PermissionController extends Controller
{protected $permissionService;
    use ApiResponceTrait;
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try{
            $permission=$this->permissionService->getAllpermissions($request);
            return $this->successResponse($permission, 'bring all permission successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, ' error with bring all permission.');
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request): JsonResponse
    {
        try {
            $validatedRequest = $request->validated();
            $permission = $this->permissionService->storePermission($validatedRequest);
            return $this->successResponse($permission, 'Permission stored successfully.', 201);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error storing permission.');
        }
    }

    /** 
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
        
            $permission=$this->permissionService->showpermission($id);
            return $this->successResponse($permission,'the permission has been showing successfuly',200);
        }catch(\Exception $e){
         return $this->handleException($e,'error with showing the permission');
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request,  $id)
    {try{
        $permission=Permission::where('id',$id)->first();
        if(!$permission->exists && !$permission){
            return $this->notFound('the permission not found');
        }
        $validated=$request->validated();
        $updatedpermission=$this->permissionService->updatepermission($permission,$validated);
       return $this->successResponse($updatedpermission,'the permission updated successfuly',200);
    }catch(\Exception $e){
        return $this->handleException($e,'error with updating permission');

    }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id):JsonResponse
    {
         try {
            $this->permissionService->deletePermission($id);
            return $this->successResponse([], 'the permission deleted successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'error with deleting the permission');
        }
    }
    public function addPermissionToRole(PermissionRequest $request): JsonResponse
    {
        try {
            // لا تستخدم validated لأنك لم تربط الـ Request بفورم مخصص
            $validated = $request->all(); 
            $result = $this->permissionService->addPermissionToRole($validated);
            return $this->successResponse($result, 'Permission added to role successfully.');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error adding permission to role.');
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
