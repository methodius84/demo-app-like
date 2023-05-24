<?php

namespace App\Services;

use Bitrix24\SDK\Core\ApiClient;
use Bitrix24\SDK\Core\Commands\Command;
use Bitrix24\SDK\Core\Credentials\AccessToken;
use Bitrix24\SDK\Core\Credentials\ApplicationProfile;
use Bitrix24\SDK\Core\Credentials\Credentials;
use Bitrix24\SDK\Core\Credentials\Scope;
use Bitrix24\SDK\Core\Credentials\WebhookUrl;
use Bitrix24\SDK\Core\Response\Response;
use Symfony\Component\HttpClient\CurlHttpClient;
use Illuminate\Log\Logger;
use RuntimeException;

class B24App
{
    private array $config;
    public ?ApiClient $apiClient = null;
    private Logger $logger;
    private CurlHttpClient $httpClient;

    public function __construct(CurlHttpClient $client, Logger $logger)
    {
        $this->logger = $logger;
        $this->httpClient = $client;
        $this->config = [];
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function getConfig() : array
    {
        return $this->config;
    }

    public function initialize(): self
    {
        $appProfile = new ApplicationProfile(
            $this->config['app_id'], $this->config['app_secret'], new Scope(['user', 'task', 'department'])
        );

        $token = new AccessToken($this->config['access_token'], $this->config['refresh_token'], 3600);

        $credentials = Credentials::createFromOAuth($token, $appProfile, $this->getDomainUrl($this->config['domain']));

        $this->httpClient = $this->httpClient->withOptions(['max_duration' => 60]);

        $this->apiClient = new ApiClient($credentials, $this->httpClient, $this->logger);

        return $this;
    }

    public function initializeFromWebhook(string $url): self
    {
        try{
            $webhook = new WebhookUrl($url);
            $credentials = Credentials::createFromWebhook($webhook);

            $this->httpClient = $this->httpClient->withOptions(['max_duration' => 60]);

            $this->apiClient = new ApiClient($credentials, $this->httpClient, $this->logger);
        }
        catch (\InvalidArgumentException $exception){
            $this->logger->error('Invalid argument during B24App initialization', [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'url' => $url,
            ]);
        }

        return $this;
    }

    public function run(string $method, $params):array
    {
        if(!$this->apiClient){
            throw new RuntimeException('Api client is not initialized. Please run auth method before running this one');
        }

        $response = $this->apiClient->getResponse($method, $params);
        $response = new Response($response, new Command($method, $params), $this->logger);
        $data = $response->getResponseData();

        return $data->getResult();
    }

    // TODO : Переделать в batch - запросы

    public function getItems(string $method, array $params):array
    {
        if(!$this->apiClient){
            throw new RuntimeException('Api client is not initialized. Please run auth method before running this one');
        }

        $response = $this->apiClient->getResponse($method, $params);
        $response = new Response($response, new Command($method, $params), $this->logger);
        $total = $response->getResponseData()->getPagination()->getTotal();
        $data = [];

        for($i=0; $i<=$total; $i+=50){
            $parameters = array_merge_recursive($params, ['start' => $i]);
            $response = $this->apiClient->getResponse($method, $parameters);
            $response = new Response($response, new Command($method, $parameters), $this->logger);
            $data = array_merge_recursive($data, $response->getResponseData()->getResult());
        }
        return $data;
    }

    private function getDomainUrl(string $domain): string
    {
        return "https://" . $domain;
    }

}













