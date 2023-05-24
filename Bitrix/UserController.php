<?php

namespace App\Http\Controllers\Bitrix;

use App\Http\Controllers\Controller;
use App\Models\bitrixAccount;
use App\Models\bitrixDepartment;
use App\Models\DepartmentsPersons;
use App\Models\GoogleAccount;
use App\Models\namesBitrixAccount;
use App\Models\Organization;
use App\Models\Person;
use App\Services\Bitrix\ServiceInterface;
use App\Services\Bitrix\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected ServiceInterface $service;

    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;
    }

    public function getUser(Request $request){
        $organization = Organization::findOrFail($request->get('bitrix'));
        return $this->service->setOrganization($organization)->get($request);
    }

    public function add(Request $request): bitrixAccount | namesBitrixAccount | \Exception
    {

        try {
            $organization = Organization::findOrFail($request->get('bitrix'));
        }
        catch(ModelNotFoundException $exception){
            return $exception;
        }
        return $this->service->setOrganization($organization)->create($request);
    }

    /**
     * @param Request $request
     */
    public function updateUser(Request $request){
        $person = Person::find($request->get('id'));

        Log::channel('bitrix')->debug('update user', $person->toArray());

        if($person->bitrixAccount){
            $organization = Organization::find(1);
            $this->service->setOrganization($organization)->update($request);
        }

        if($person->namesBitrixAccount){
            $organization = Organization::find(3);
            $this->service->setOrganization($organization)->update($request);
        }
    }

    public function activateUser(Request $request){
        $organization = Organization::find($request->get('bitrix'));

        return $this->service->setOrganization($organization)->activate($request);
    }

    public function fireUser(Request $request)
    {
        $organization = Organization::find($request->get('bitrix'));

        return $this->service->setOrganization($organization)->delete($request);
    }

    public function syncUsers($id){
        try {
            $organization = Organization::findOrFail($id);

            $this->service->setOrganization($organization)->sync();
            Log::channel('telegram')->info($organization->short_title.' users were synced');
        }
        catch (ModelNotFoundException $exception){
            Log::channel('telegram')->critical('Users were not synced. Error: '.$exception->getMessage());
        }
    }
}
