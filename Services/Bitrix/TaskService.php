<?php

namespace App\Services\Bitrix;

use App\Jobs\Bitrix\CreateTask;
use App\Models\bitrixAccount;
use App\Models\Organization;
use App\Models\Person;
use App\Services\B24App;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskService implements ServiceInterface
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
        $method = 'tasks.task.list';

        $this->refreshToken();

        $params = [
            'select' => $request->get('select'),
            'filter' => $request->get('filter'),
            'order' => $request->get('order'),
            'limit' => 200
        ];

        return $this->B24App->run($method, $params);
    }

    public function create(Request $request)
    {
        // TODO: Implement create() method.

        $method = 'tasks.task.add';

        if(Carbon::now()->diffInMinutes($this->organization->portal->updated_at) >= 50){
            $this->refreshToken();
        }

        $params = [
            'auth' => $this->organization->portal->access_token,
            'fields' => $request->get('fields'),
        ];

        try {
            Log::channel('bitrix')->info($request->get('fields')['RESPONSIBLE_ID'].' trying to create task', ['payload' => $request->all()]);
            $this->B24App->run($method, $params);
        }
        catch (\Throwable $exception){
            Log::channel(['bitrix', 'telegram'])->error('Error creating task for user'.$request->get('fields')['RESPONSIBLE_ID'], [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'payload' => $request->all(),
            ]);
        }
    }

    public function update(Request $request)
    {
        // TODO: Implement update() method.
    }

    public function delete(Request $request)
    {
        // TODO: Implement delete() method.
    }

    public function sync()
    {
        // TODO: Implement sync() method.
    }

    public function massiveTasks(array $data){
        $title = $data['title'];
        $description = $data['description'];

        $creator = $data['created_by'];

        // bitrix project id
        $group = $data['group_id'] ?? null;

        $vacation = $this->getUserAbscences() ?? null;

        $extranet = Person::doesntHave('departments')->get();
        $platform_id = [];
        foreach ($extranet as $extra){
            if(!isset($extra->bitrixAccount)){
                $extranet = $extranet->whereNotIn('id', $extra->id);
                continue;
            }
            $platform_id[] = $extra->bitrixAccount->platform_id;
        }

        foreach ($platform_id as $extranetId){
            $vacation[] = $extranetId;
        }

        $deadline = Carbon::parse($data['deadline'])->setTime(19, 0)->toDateTimeString();

        $users = bitrixAccount::whereActive(1)->get();
        $users->sortBy('platform_id');

        foreach ($users as $user){

            if(in_array($user->platform_id, $vacation)){
                Log::channel('bitrix')->info('User '.$user->last_name.' '.$user->first_name.' skipped. User is on vacation');
                continue;
            }

            $fields = [
                'TITLE' => $title,
                'DESCRIPTION' => $description,
                'CREATED_BY' => $creator,
                'GROUP_ID' => $group,
                'RESPONSIBLE_ID' => $user->platform_id, // person->platform_id
                'DEADLINE' => $deadline,
            ];

            CreateTask::dispatch($fields, $this->organization)->delay(now()->addMinutes(2));
        }
    }

    public function getUserAbscences(){
        $method = 'timeman.timecontrol.reports.get';

        $ch = curl_init();

        $timeStart = Carbon::now('Europe/Moscow')->setTime(0,0,0)->addDays();
        $timeEnd = $timeStart->copy()->addDays(6);

        curl_setopt($ch, CURLOPT_URL, 'https://likecentr.bitrix24.ru/bitrix/components/bitrix/intranet.absence.calendar/ajax.php?MODE=GET&TS_START='.$timeStart->timestamp.'&TS_FINISH='.$timeEnd->timestamp.'&SHORT_EVENTS=N&USERS_ALL=N&DEPARTMENT=&PAGE_NUMBER=0&current_data_id=99465&site_id=s1&iblock_id=3&calendar_iblock_id=0&sessid=b275f0defc7ae5e8eee48a88d217ec5c&rnd=0.4552188842778173');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Authority: likecentr.bitrix24.ru';
        $headers[] = 'Accept: */*';
        $headers[] = 'Accept-Language: ru,en-US;q=0.9,en;q=0.8';
        $headers[] = 'Cookie: USER_LANG=ru; BX_USER_ID=530bf6e7c8a229260c379e230ee1daca; BITRIX_SM_SALE_UID=0; _ym_uid=1676922942648242818; _ym_d=1676922942; tmr_lvid=4e742078cf9d62aa1a88fe24a282eabb; tmr_lvidTS=1676922942312; BITRIX_SM_OTPH=cfcd208495d565ef66e7dff9f98764da.1711177729.4bdcab3746b59d790511967b4f6b68e687be348ca67e133e26303b96a543be56; BITRIX_SM_UIDL=k.kobunov%40likebz.ru; BITRIX_SM_CC=Y; BITRIX_SM_SOUND_LOGIN_PLAYED=Y; ab_id=33fa086371bd6d91d777ba2d5ce9a9d3cb4cda26; _ga=GA1.1.114274433.1676922942; _ga_0X7ZLV9Y7K=GS1.1.1682066124.18.1.1682066125.59.0.0; BITRIX_SM_kernel=-crpt-kernel_0; PHPSESSID=k040ISZoOFmhxBrSzfBeyYXtPB2h5M5N; BITRIX_SM_PK=%2F5507%2F8a0a06aaf9b0af99d261803ff149f741; BITRIX_SM_UIDH=DsvWdLgmWXkVoxxnPMlUXlK25Rnfp0bj; qmb=5507.socservices.1; BITRIX_SM_kernel_0=lVyhwqxKUYC4eSRFtqGY_eSQts89pBBfy2glgC4iD2N3Q6nA3YCvnXJP-8GGXf1QQnR_bTeo9ifDQerMJpghKFsk6Ltf-iN3lx5R1vZW6lb3DNwiSBFnsgCKdJOZGZS1nWyyd06AB_75ZXVTYIyA5wZLjlVm4Q8RYbz9LsemY-AaLhGGR-Alxvcty5AlIFZv_LgzqiVAzJMcOeWWE7Rwnv65WPL2s2cBVykY8Q32HAICgUtcKMdswyMoOcePrqasF-S28DtcyQxOrEO-fHCm_S9ERoYgExvjmvUTluJMk23BUFA8bUEXLt2qFE-XZcUrir4BZkJarEcqWsQ1Ary2X6Bdp0_6b08LhxbAilTZHEkPNFr2sHWwWWrI3Y1mOYpuheAxWotGgPseRDGDwRG3tCaNYE_TrSsoHeUI2mYA9P9LsJsz5M0XLeVJiC7HkVSJK2srxsSO7dw7TJrr-skx_jI9rWleo6WuMUflNUDZ1IGB62BEr25f8IR-_r-BdPSSrwxTif_mZZEX7P6CtzMpFnYA9u0OZBy-wY06t26FR_0GIifao4rglueiYDpfQeG96k-pS9g6gjPk_DMeN94h5IbRNTS04BqnrOed06zC8yALhC6CBd5gWvTEu7JWQMczrPiTkRd_z1MBKvP2iMIOceDeJpHqKn2pg58CtgQhn8SbhZ9x1SC3_HxjGCpMxby-_gKNIRMI2HFUQbRAjpDF5e9dCZHQOyTazfmtCYHK_7piTf7zKbBli6xu2ffvFSLe7OnM5LRYtFerOCzrFuDQX0dsl2lzqvgUViShJv27FVQZ8ni0DQy7PcwkwaWKxY6nYRPdoxj2FrFoH11gTBRaHyKqD-4HrpEwr3WThhD2gL4u0ZegCymSzEuKBa-fZ1t-MjuX0X1Le29HQo_q2GxrVh7J8K8w33rMi335foEhg0ZaxY4YVGskY_EnzADJoMjd859HPUOyOj4au1xjrJxQVShmmpkBB4C_Xk02nWq-CrsfOpgNnMmDuobt7WGuHSJ8usvsMgCJ6Fmp_hIxwt5I2OjFpDNwUajL3aREMkgSGLtZK0EmJsvhnwbOPsBWtKaoO4H1YAowHFWX7xMrx9Liun5hO3dHaXRTZBqSzRWaqLm-0_7pDRdRW0dkJcwG58YlQD6GPiT3LJN3eVaJ-3SpFepXND9wqdkNopuU20VWzsd324L1UEXG8iUqnZG965xZ4zmZsoe1eYl9TDwNnNni6J9vGxM78GLkYGw_zU-X4y3t2b8248qchfZ4glLcwtuAh-ycUWr3RkATRd5E6_8CfedHCWQW8RMQ_9CxnczVsd7a0CGeqWIoa3wM4LagZV_p6UDEBsAuRoZE2CyhQr1z8PkYEn-etmnZnhUxpmSrFXL9pR6GmH1Mc_LipRfG2SFOeDLPMdYebkQ0mpKGp--bG1O72WbgmGL8qME1x4VdC6BLCyc3ZMw3D_ztdrhVWx0E7FOGglIbDhq8qOsohNkmRLpSwS-0jAEd9sg22AoeSUgTud6Ttcx792Me2vEQda4EtLu30DqXvMVf9OLEcLpG9hoYZ-fsAm4EwM3EnLy7ObmfmduLscfTRqqNl8M-rl1f2f6tMah0JPyeuz9pNZq1WEzJOMsLJHEcpE18oxqqDc1rg4SFxEkCCWThy0Hd730XK3q5XAPKScMcrhDDe8xhFe7-Ps3wTVZHWHl-SHHZ3w44yBm1u7aNGWsvQ-0_kXPdIbNoANH8Jr7-GOkNYDhHprav5LNfOpxeUYdzQp91KZdFDFi24YDttXDBw55bZYgPtQDVpFyb8U2RFtB5xCp9Q4g6Fi0nIyT6mUZs2bBh13_13K3H9K7q8WJBhTCtkNRGzei7Li1sWQQh1V4CcKXFR4vM11oGzzjEfzj6FQRpGlcbbqu74nlCNyu9rn8-VKdTdG6OfR0rqdBq6wG4wbW89QqOoXPkKkqSzK0bQF9R4dVp6gg6nEbqksHH32T-8bsWozQV-GraKUHZFvp78Nvou5i3B5Hr';
        $headers[] = 'Referer: https://likecentr.bitrix24.ru/timeman/';
        $headers[] = 'Sec-Ch-Ua: \"Chromium\";v=\"110\", \"Not A(Brand\";v=\"24\", \"YaBrowser\";v=\"23\"';
        $headers[] = 'Sec-Ch-Ua-Mobile: ?0';
        $headers[] = 'Sec-Ch-Ua-Platform: \"macOS\"';
        $headers[] = 'Sec-Fetch-Dest: script';
        $headers[] = 'Sec-Fetch-Mode: no-cors';
        $headers[] = 'Sec-Fetch-Site: same-origin';
        $headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 YaBrowser/23.3.0.2317 Yowser/2.5 Safari/537.36';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $result = preg_replace('/jsBXAC.SetData\(/','',$result);

        $result = preg_replace('/, \d+, \d+, \d+\)/','',$result);
        $result = str_replace("'", '"', $result);
        $data = json_decode($result, false);
        $abscenceUsers = [];
        foreach ($data as $user){
            $abscenceUsers[] = (int)$user->ID;
        }
        return $abscenceUsers;
    }
}
