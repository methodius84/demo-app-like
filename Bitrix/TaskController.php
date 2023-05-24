<?php

namespace App\Http\Controllers\Bitrix;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bitrix\MassiveTaskRequest;
use App\Models\bitrixAccount;
use App\Models\bitrixDepartment;
use App\Models\Organization;
use App\Services\Bitrix\ServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected ServiceInterface $service;

    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;
    }

    public function getTasks(Request $request){
        try{
            $organization = Organization::findOrFail('bitrix');

            return $this->service->setOrganization($organization)->get($request);
        }
        catch (\Throwable $exception){
            return response()->json([], 400);
        }
    }

    public function createMassiveTask(MassiveTaskRequest $request){
        // TODO : reduce polymorphic call
        $organization = Organization::findOrFail(1);
        $this->service->setOrganization($organization)->massiveTasks($request->validated());

        return redirect('/');
    }

    public function createTaskView(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $users = bitrixAccount::whereActive(1)->get();
        return view('admin.bitrix_layouts.create-task')->with(['users' => $users]);
    }

    public function test(){
        $organization = Organization::find(1);

        return $this->service->setOrganization($organization)->getUserAbscences();
    }
}
