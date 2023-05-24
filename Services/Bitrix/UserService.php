<?php

namespace App\Services\Bitrix;

use App\Models\bitrixAccount;
use App\Models\bitrixDepartment;
use App\Models\DepartmentsPersons;
use App\Models\GoogleAccount;
use App\Models\namesBitrixAccount;
use App\Models\namesBitrixDepartment;
use App\Models\Organization;
use App\Models\Person;
use Exception;
use App\Services\B24App;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Validator;

class UserService implements ServiceInterface
{
    use ConfigurationTrait;
    private B24App $B24App;

    private ?Organization $organization;

    public function __construct(){
        $this->B24App = resolve(B24App::class);
    }

    public function setOrganization(Organization $organization) : self
    {
        $this->organization = $organization;
        $this->B24App = $this->withOrganizationB24App($this->organization);

        return $this;
    }

    public function getOrganization() : Organization
    {
        return $this->organization;
    }

    public function get(Request $request)
    {
        // TODO: Implement get() method.
        $user = Person::where('id', $request->get('id'))->first();

        $this->refreshToken();

        $method = 'user.get';

        $params = [
            'ID' => $user->bitrixAccount->platform_id,
        ];


        return $this->B24App->run($method, $params);
    }

    public function create(Request $request): bitrixAccount|namesBitrixAccount|Exception
    {
        // TODO: Implement create() method.

        $user = Person::where('id', $request->get('id'))->first();

        try{
            if($user->email === null){
                throw new Exception("Не создана корп. почта для $user->last_name");
            }

            // проверка на существования юзера с таким мейлом

            switch($request->get('bitrix')){
                case 1:
                    if(!null === bitrixAccount::where('email', $user->email)->first()){
                        throw new Exception("Пользователь $user->last_name существует в Битрикс");
                    }
                    break;
                case 3:
                    if(!null === namesBitrixAccount::where('email', $user->email)->first()){
                        throw new Exception("Пользователь $user->last_name существует в Битрикс");
                    }
                    break;
            };

            // TODO: check if token is still valid
            $this->refreshToken();

            $department = $this->getDepartmentId($user);

            $method = 'user.add';
            $telegramField = config('services.bitrix.telegram');

            $params = [
                'EMAIL' => $user->email,
                'auth' => $this->organization->portal->access_token,
                'NAME' => $user->first_name,
                'LAST_NAME' => $user->last_name,
                'WORK_POSITION' => $user->position,
                'UF_DEPARTMENT' => $department,
                'PERSONAL_PHONE' => $user->phone,
                "$telegramField" => $user->telegram ?? null,
            ];

            $response = $this->B24App->run($method, $params);

            if($this->organization->id === 3){
                $model = new namesBitrixAccount();
            }
            else {
                $model = new bitrixAccount();
            }

            $model = $model::create([
                'platform_id' => $response[0],
                'user_type' => 'employee',
                'department_id' => $department[0],
                'active' => 1,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'second_name' => $user->second_name ?? null,
                'email' => $user->email,
                'mobile' => $user->phone,
                'position' => $user->position,
                'person_id' => $user->id
            ]);


            Log::channel('telegram')->info("User $user->last_name invited to Bitrix24 ".$this->organization->short_title);
            return $model;
        }

        catch (Exception $e){
            Log::channel('telegram')->alert('Error inviting user: '.$e->getMessage());
            return $e;
        }
    }

    public function update(Request $request): string
    {
        // TODO: Implement update() method.
        $person = Person::find($request->get('id'));

        // TODO : refactor this method to avoid duplication

        $method = 'user.update';

        try{
            $this->refreshToken();
            $params = $this->setUpdateParameters($person);

            $this->callMethod($method, $params);

            match ($this->organization->id){
                3 => $person->namesBitrixAccount->update([
                    'email' => $person->email,
                    'position' => $person->position,
                    'department_id' => $params['UF_DEPARTMENT'][0],
                ]),
                default => $person->bitrixAccount->update([
                    'email' => $person->email,
                    'position' => $person->position,
                    'department_id' => $params['UF_DEPARTMENT'][0],
                ]),
            };

            return "Информация обновлена в базе и в Битрикс";
        }
        catch(\Throwable $e){
            return $e->getMessage();
        }

    }

    private function setUpdateParameters(Person $person) : array
    {
        return match ($this->organization->id){
            1 => [
                'ID' => $person->bitrixAccount->platform_id,
                'auth' => $this->organization->portal->access_token,
                'EMAIL' => $person->email,
                'WORK_POSITION' => $person->position,
                'UF_DEPARTMENT' => $this->getDepartmentId($person),
                config('services.bitrix.telegram') => $person->telegram,
            ],
            3 => [
                'ID' => $person->namesBitrixAccount->platform_id,
                'auth' => $this->organization->portal->access_token,
                'EMAIL' => $person->email,
                'WORK_POSITION' => $person->position,
                'UF_DEPARTMENT' => $this->getDepartmentId($person)
            ]
        };
    }

    public function delete(Request $request): int|\Illuminate\Http\RedirectResponse
    {
        // TODO: Implement delete() method. fireBitrix method

        $user = Person::find($request->get('id'));

        $this->refreshToken();

        $bitrix = $this->getBitrix($user);

        $method = 'user.update';

        $params =[
            'ID' => $bitrix->platform_id,
            'auth' => $this->organization->portal->access_token,
            'ACTIVE' => "N"
        ];
        $data = $this->B24App->run($method, $params);

        if($data[0]){
            $bitrix->update(['active' => 0]);
            $user->update(['active' => 0]);

            if($request->headers->contains('Accept', 'application/json')){
                return 0;
            }
            return redirect()->back()->with('status', "Пользователь $user->last_name $user->first_name заблокирован в Битрикс24");
        }
        else return redirect()->back()->with('status', "Ошибка при увольнении");
    }

    public function sync() : string
    {
        $users = $this->getUserList($this->organization);

//        Log::channel('telegram')->debug('Users has come from bitrix: '.count($users).' users');

        if(!empty($users)){
            if($this->organization->id === 3){
                $this->updateTableNames($users);
                return 'success';
            }
            $this->departmentPersonMatch($users);
            $this->updateTableLike($users);
            return 'success';
        }
        else return 'unable to get users';
    }

    public function getUserList(Organization $organization) : array
    {
        $this->refreshToken();
        $method = "user.get";

        $params = [
            "auth" => $this->organization->portal->access_token,
            "USER_TYPE" => "employee",
        ];
        return $this->B24App->getItems($method, $params);
    }

    private function updateTableLike($user_list)
    {
        foreach ($user_list as $user) {
            $account = new bitrixAccount();
            $this->fillBitrixTable($account, $user);
            $person = bitrixAccount::wherePlatformId($user['ID'])->first()->person ?? null;
            if(isset($user[config('services.bitrix.telegram')]) && $person){
                $person->update([
                    'telegram' => preg_replace(['/@/', "/t\.me\//", "/https:\/\//"],['','',''], $user[config('services.bitrix.telegram')])
                ]);
            }
            if(isset($user[config('services.bitrix.gmail')]) && $person) {

                $gmailArray = preg_split('/[\s,;]/', $user[config('services.bitrix.gmail')]);
                foreach ($gmailArray as $email) {
                    $validator = Validator::make([
                        'email' => $email,
                        'person' => $person
                    ],
                        [
                            'email' => 'required|email',
                            'person' => 'required'
                        ]);

                    if ($validator->passes()) {
                        GoogleAccount::updateOrCreate([
                            'email' => $email
                        ],
                        [
                            'person_id' => $person->id,
                        ]);
                    }
                }
            }

        }
        Log::channel('bitrix')->info('Выполнена синхронизация пользователей битрикс LikeCentre');
    }

    private function updateTableNames($user_list)
    {
        foreach ($user_list as $user) {
            $account = new namesBitrixAccount();
            $this->fillBitrixTable($account, $user);
        }
        Log::channel('bitrix')->info('Выполнена синхронизация пользователей битрикс NAMES');
    }

    private function fillBitrixTable($account, $user):void
    {
        try {
            $account->updateOrCreate(
                [
                    'platform_id' => $user['ID']
                ],
                [
                    'user_type' => $user['USER_TYPE'],
                    'active' => $user['ACTIVE'],
                    'first_name' => $user['NAME'] ?? null,
                    'last_name' => $user['LAST_NAME'] ?? null,
                    'second_name' => $user['SECOND_NAME'] ?? null,
                    'gender' => $user['PERSONAL_GENDER'] ?? null,
                    'email' => $user['EMAIL'],
                    'phone' => $user['PERSONAL_PHONE'] ?? null,
                    'mobile' => $user['PERSONAL_MOBILE'] ?? null,
                    'personal_site' => $user['PERSONAL_WWW'] ?? null,
                    'birthday' => Carbon::parse($user['PERSONAL_BIRTHDAY'])->format('Y-m-d h:i:s') ?? null,
                    'profile_photo' => $user['PERSONAL_PHOTO'] ?? null,
                    'city' => $user['PERSONAL_CITY'] ?? null,
                    'position' => $user['WORK_POSITION'] ?? null,
                    'vk_page' => $user['UF_WEB_SITES'] ?? null,
                    'skype_login' => $user['UF_SKYPE'] ?? null,
                ]
            );
        }
        catch (\Throwable $exception){
            Log::channel('bitrix')->error('User '.$user['ID'].' wasnt synced. Error: '.$exception->getMessage());
        }
    }

    public function departmentPersonMatch(array $user_list){
        foreach($user_list as $list_item){
            $user = bitrixAccount::wherePlatformId($list_item['ID'])->first();
            if($user && !is_null($user->person_id)){
                DepartmentsPersons::where('person_id', $user->person->id)->delete();
                $departments = $list_item['UF_DEPARTMENT'];
                foreach($departments as $department){
                    if($department !== 0 && bitrixDepartment::find($department)){
                        DepartmentsPersons::insert([
                            'department_id' => $department,
                            'person_id' => $user->person->id
                        ]);
                    }
                }
            }
        }
    }

    public function activate(Request $request){
        $user = Person::find($request->get('id'));

        $bitrix = $this->getBitrix($user);

        $department = $this->getDepartmentId($user);

        $this->refreshToken();

        $method = 'user.update';

        $params = [
            'ID' => $bitrix->platform_id,
            'auth' => $this->organization->portal->access_token,
            'ACTIVE' => "Y",
            'EMAIL' => $user->email,
            'WORK_POSITION' => $user->position,
            'UF_DEPARTMENT' => $department
        ];

        $response = $this->B24App->run($method, $params);
        if($response[0]){
            $bitrix->update(['active' => 1]);
            $user->update(['active' => 1]);
            return redirect()->back()->with('status', "Пользователь $user->last_name $user->first_name активирован в Битрикс24");
        }
        else return redirect()->back()->with('status', "Ошибка при активации");
    }

    private function getDepartmentId(Person $user): array|null
    {
        if ($this->organization->id === 3){
            $likeIds = $user->departments->pluck('department_id')->toArray();
            $namesIds = namesBitrixDepartment::whereIn('like_id', $likeIds)->get()->pluck('department_id')->toArray();
            if(!$namesIds){
                $namesIds = namesBitrixDepartment::whereDepartmentId(1)->pluck('department_id')->toArray();
            }

            return $namesIds;
        }
        else {
            return $user->departments->pluck('department_id')->toArray() ?? [];
        }
    }

    private function getBitrix(Person $person): bitrixAccount|namesBitrixAccount|null
    {
        return match ($this->organization->id) {
            3 => $person->namesBitrixAccount ?? null,
            default => $person->bitrixAccount ?? null,
        };
    }
}
