<?php

namespace App\Services\Bitrix;

use App\Models\Organization;
use App\Services\B24App;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class MessageService
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

    public function send(Request $request)
    {
        $method = 'im.message.add';

        $url = config('services.bitrix.send-private-new-employee');

//        $this->B24App = $this->B24App->initializeFromWebhook($url);

        $params = $request->get('fields');

        $client = new Client([
            'base_uri' => $url.'/',
        ]);

        $response = $client->post($method, [
           'query' => $params,
        ]);

        $response = json_decode($response->getBody()->getContents(), true);

        if (isset($response['error'])){
            \Log::channel('telegram')->error('Error sending message', [
                'code' => $response['error'],
                'message' => $response['error_description'],
            ]);
            return 'error '.$response['error_description'];
        }
        else{
            \Log::channel('bitrix')->info('Message for user '.$params['DIALOG_ID'].' sent successfully');
            return 'success';
        }
    }
}
