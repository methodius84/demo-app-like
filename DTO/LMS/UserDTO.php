<?php

namespace App\DTO\LMS;

use App\Models\LMSUser;

class UserDTO
{
    private ?string $id;
    private string $email;

    private int $phone;

    private string $firstName;
    private string $lastName;
    private ?string $patronymic;
    private ?string $city;
    private ?string $telegram;

    private array $roles;

    private ?int $personId;

    /**
     * @param LMSUser $user
     */
    public function __construct(LMSUser $user)
    {
        $this->id = $user->platform_id;
        $this->email = $user->person->email;
        $this->phone = $user->person->phone;
        $this->firstName = $user->person->first_name;
        $this->lastName = $user->person->last_name;
        $this->patronymic = $user->person->second_name ?? null;
        $this->telegram = $user->person->telegram;
        $this->roles = $user->roles;
        $this->personId = $user->person_id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getPhone(): int
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getPatronymic(): string | null
    {
        return $this->patronymic;
    }

    /**
     * @return string|null
     */
    public function getTelegram(): string | null
    {
        return $this->telegram;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return int
     */
    public function getPersonId(): int
    {
        return $this->personId;
    }
}
