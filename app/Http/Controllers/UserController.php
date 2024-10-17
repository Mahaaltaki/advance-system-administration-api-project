<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Services\UserService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Trait\ApiResponceTrait;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{  protected $userService;
    use ApiResponceTrait;

    /**
     * Display a listing of the resource.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request): JsonResponse
    {
        try{
            $users=$this->userService->getAllUsers($request);
            return $this->successResponse($users, 'bring all users successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, ' error with bring all users.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try{
            $validatedRequest=$request->validated();
            $user=$this->userService->storeUser($validatedRequest);
            return $this->successResponse($user,'user stored successfuly',201);
        }catch(\Exception $e){
            return $this->handleException($e, ' error with stored user',);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
        
            $user=$this->userService->showUser($id);
            return $this->successResponse($user,'the user has been showing successfuly',200);
        }catch(\Exception $e){
         return $this->handleException($e,'error with showing the user');
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {try{
        if(!$user->exists){
            return $this->notFound('the user not found');
        }
        $validated=$request->validated();
        $updatedUser=$this->userService->updateUser($user,$validated);
       return $this->successResponse($updatedUser,'the user updated successfuly',200);
    }catch(\Exception $e){
        return $this->handleException($e,'error with updating user');

    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id):JsonResponse
    {
         try {
            $this->userService->deleteUser($id);
            return $this->successResponse([], 'the user deleted successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'error with deleting the user');
        }
    }
    public function addRoleToUser(Request $request){
        try{
            $validated=$request->validated();
            $query=$this->userService->addRoleToUser($validated);
            return $this->successResponse([$query],'the user have a role successfuly');
        }catch(\Exception $e){
          return $this->handleException($e,'error with setting the role to user');
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
