<?php

namespace App\Http\Controllers\Bitrix;

use App\Http\Controllers\Controller;
use App\Models\bitrixDepartment;
use App\Models\Organization;
use App\Services\Bitrix\ServiceInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{

    protected ServiceInterface $service;

    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;
    }

    public function sync($id){
        try {
            $organization = Organization::findOrFail($id);

            $this->service->setOrganization($organization)->sync();
        }
        catch (ModelNotFoundException $exception){
            return response()->json([
                'error' => $exception->getMessage(),
            ], 422);
        }
        catch (BindingResolutionException $exception){
            \Log::channel('telegram')->alert('Binding exception in '.__CLASS__,
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]
            );
        }
    }

    public function create(Request $request){
        try {
            $organization = Organization::findOrFail($request->get('bitrix'));

            $this->service->create($request);
        }
        catch (ModelNotFoundException $exception){
            return response()->json([
                'error' => $exception->getMessage(),
            ], 422);
        }
        catch (BindingResolutionException $exception){
            \Log::channel('telegram')->alert('Binding exception in '.__CLASS__,
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]
            );
        }
    }

    public function update(Request $request){
        try {
            $organization = Organization::find(1);
            $response = $this->service->setOrganization($organization)->update($request);
            Log::channel('telegram')->debug('response updating department', ['response' => $response]);
            if($response){
                return response()->json([
                    'message' => 'success',
                    'department' => bitrixDepartment::find($request->get('id'))
                ]);
            }
            else{
                return response()->json([
                    'message' => 'something went wrong',
                ], 400);
            }
        }
        catch (ModelNotFoundException $exception){
            return response()->json([
                'error' => $exception->getMessage(),
            ], 422);
        }
        catch (BindingResolutionException $exception){
            \Log::channel('telegram')->alert('Binding exception in '.__CLASS__,
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]
            );
        }
    }

    public function delete(Request $request){
        try {
            $organization = Organization::findOrFail($request->get('bitrix'));

            $this->service->delete($request);
        }
        catch (ModelNotFoundException $exception){
            return response()->json([
                'error' => $exception->getMessage(),
            ], 422);
        }
        catch (BindingResolutionException $exception){
            \Log::channel('telegram')->alert('Binding exception in '.__CLASS__,
                [
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ]
            );
        }
    }

    public function list(){
        $organization = Organization::find(1);

        return $this->service->list();
    }

    public function getDepartmentById($id){
        return response()->json([
            'department' => bitrixDepartment::find($id) ?? null]);
    }
}
