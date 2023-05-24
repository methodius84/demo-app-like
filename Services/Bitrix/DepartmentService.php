<?php

namespace App\Services\Bitrix;

use App\Http\Controllers\OrganizationController;
use App\Models\bitrixAccount;
use App\Models\bitrixDepartment;
use App\Models\namesBitrixAccount;
use App\Models\namesBitrixDepartment;
use App\Models\Organization;
use App\Models\Person;
use App\Services\B24App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartmentService implements ServiceInterface
{

    use ConfigurationTrait;

    private B24App $B24App;

    private Organization $organization;

    public function __construct(){
        $this->B24App = resolve(B24App::class);
    }

    public function setOrganization(Organization $organization) : self
    {
        $this->organization = $organization;
        $this->B24App = $this->withOrganizationB24App($organization);

        return $this;
    }

    public function getOrganization() : Organization
    {
        return $this->organization;
    }

    public function get(Request $request)
    {
        // TODO: Implement get() method.

        $department = bitrixDepartment::find($request->get('id'));

        $method = 'department.get';

        $params = [
            'auth' => $this->organization->portal->access_token,
            'ID' => $department->department_id,
        ];

        return $this->B24App->run($method, $params);
    }

    public function create(Request $request)
    {
        // TODO: Implement create() method.

        $method = 'department.add';

        $params = [
            'NAME' => $request->get('name'),
            'auth' => $this->organization->portal->access_token,
            'PARENT' => $request->get('parent_dep'),
            'UF_HEAD' => $request->get('head'), // скорее всего поменяю
        ];

        $response = $this->B24App->run($method, $params);;

        if($response){
            bitrixDepartment::create(
                [
                    'department_id' => $response['result'],
                    'dep_name' => $request->get('name'),
                    'parent_dep' => $request->get('parent_dep'),
                    'head' => $request->get('head'),
                ]
            );
        }

        // TODO поменять ответ
        return response()->json([
            'name' => $request->get('name')
        ]);
    }

    public function update(Request $request)
    {
        // TODO: Implement update() method.

        $method = 'department.update';

        try {
            $department = bitrixDepartment::findOrFail($request->get('id'));
            $head = Person::find($request->get('head'));

            $this->refreshToken();

            $params = [
                'ID' => $department->department_id,
                'auth' => $this->organization->portal->access_token,
                'NAME' => $request->get('name') ?? $department->dep_name,
                'HEAD' => $head->bitrixAccount->platform_id ?? $department->head,
                'PARENT' => $request->get('parent_dep') ?? $department->parent_dep,
            ];

            return $this->B24App->run($method, $params);
        }
        catch (ModelNotFoundException $exception){
            return false;
        }
    }

    public function delete(Request $request)
    {
        // TODO: Implement delete() method.

        $department = bitrixDepartment::findOrFail($request->get('id'));

        $method = 'department.delete';

        $params = [
            'ID' => $department->department_id,
            'auth' => $this->organization->portal->access_token,
        ];

        return $this->B24App->run($method, $params);
    }

    public function sync()
    {
        // TODO: Implement sync() method.

        $this->refreshToken();


        $method = 'department.get';
        $params = [
            'auth' => $this->organization->portal->access_token,
        ];
        $dep_list = $this->B24App->getItems($method, $params);

        if (!empty($dep_list)) {
            switch ($this->organization->id) {
                case 1:
                    foreach ($dep_list as $dep) {
                        $bdep = new bitrixDepartment();
                        $bdep::updateOrCreate(
                            [
                                'department_id' => $dep['ID']
                            ],
                            [
                                'dep_name' => $dep['NAME'] ?? "Без имени",
                                'parent_dep' => $dep['PARENT'] ?? null,
//                                'head' => (($dep['UF_HEAD'] ?? null) != 0) ? $dep['UF_HEAD'] : null,
                                'head' => bitrixAccount::wherePlatformId($dep['UF_HEAD'] ?? null)->first()->person_id ?? null,
                                'organization_id' => 1
                            ]
                        );
                    }
                    $this->findDeprecatedDepartments($dep_list, $this->organization);
                    break;
                case 3:
                    foreach ($dep_list as $dep) {
                        $bdep = new namesBitrixDepartment();
                        $bdep::updateOrCreate(
                            [
                                'department_id' => $dep['ID']
                            ],
                            [
                                'like_id' => bitrixDepartment::whereDepName($dep['NAME'])->first()->department_id ?? null,
                                'dep_name' => $dep['NAME'] ?? "Без имени",
                                'parent_dep' => $dep['PARENT'] ?? null,
                                'head' => namesBitrixAccount::wherePlatformId($dep['UF_HEAD'] ?? null)->first()->person_id ?? null,
                            ]
                        );
                    }
                    $this->findDeprecatedDepartments($dep_list, $this->organization);
                    break;
            }

            if ($this->organization->id === 1) {
                $var = new OrganizationController();
                $var->fillUnits();
            }
            Log::channel('telegram')->info('Departments were synced');
            if(PHP_SAPI === 'cli'){
                return 'success';
            }
            return redirect('/');
        }
    }

    public function list(){
        $departments = bitrixDepartment::all();
        return response()->json($departments);
    }

    private function findDeprecatedDepartments(array $dep_list, Organization $organization) : void
    {
        if($organization->id === 3){
            $dbDepartmentIds = namesBitrixDepartment::all()->pluck('department_id')->toArray();
        }
        else {
            $dbDepartmentIds = bitrixDepartment::all()->pluck('department_id')->toArray();
        }
        $actualDepIds = array_column($dep_list, 'ID');
        $deprecatedDepartments = array_diff($dbDepartmentIds, $actualDepIds);
        if($deprecatedDepartments){
            bitrixDepartment::whereIn('department_id', $deprecatedDepartments)->delete();
        }
    }
}
