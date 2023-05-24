<?php

namespace App\Services\Bitrix;

use App\Models\Organization;
use App\Services\B24App;

trait ConfigurationTrait
{
    public function withOrganizationB24App(Organization $org): B24App
    {
        $portal = $org->portal;

        $config = [
            'app_id' => $portal->app_id,
            'app_secret' => $portal->app_secret,
            'domain' => $portal->domain,
            'access_token' => $portal->access_token,
            'refresh_token' => $portal->refresh_token
        ];

        return $this->B24App->setConfig($config)->initialize();
    }

    private function refreshToken() : void
    {
        $credentials = $this->B24App->apiClient->getNewAccessToken();

        $this->B24App->apiClient->getCredentials()->setAccessToken($credentials->getAccessToken());

        $this->organization->portal->update([
            'access_token' => $credentials->getAccessToken()->getAccessToken(),
            'refresh_token' => $credentials->getAccessToken()->getRefreshToken()
        ]);
    }

    public function callMethod(string $method, array $params = []) : array
    {
        return $this->B24App->run($method, $params);
    }
}
