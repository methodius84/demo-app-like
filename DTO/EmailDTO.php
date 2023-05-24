<?php

namespace App\DTO;

class EmailDTO
{

    private string $username;
    private ?bool $active = true;
    private string $domain;
    private string $name;
    private ?int $quota = 1024;
    private string $localPart;
    private ?int $personId;


    public function __construct(array $params){
        $this->username = $params['username'];
        $this->domain = $params['domain'];
        $this->active = $params['active'] ?? true;
        $this->name = $params['name'];
        $this->localPart = $params['local_part'];
        $this->personId = $params['person_id'];
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }


    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getLocalPart(): string
    {
        return $this->localPart;
    }

    /**
     * @return int|null
     */
    public function getQuota(): ?int
    {
        return $this->quota;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getPersonId(): ?int
    {
        return $this->personId;
    }
}
