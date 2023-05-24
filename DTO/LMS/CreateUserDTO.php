<?php

namespace App\DTO\LMS;

class CreateUserDTO implements RabbitMessageDTOInterface
{
    private string $email;

    private int $phone;

    private string $firstName;
    private string $lastName;
    private ?string $patronymic;
    private ?string $telegram;

    private array $roles;

    /**
     * @param UserDTO $user
     */
    public function __construct(UserDTO $user)
    {
        $this->email = $user->getEmail();
        $this->phone = $user->getPhone();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->patronymic = $user->getPatronymic();
        $this->telegram = $user->getTelegram();
        $this->roles = $user->getRoles();
    }

    public function toArray(): array
    {
        return [
            'data' => [
                'data' => [
                   'email' => $this->email,
                   'phone' => $this->phone,
                   'firstName' => $this->firstName,
                   'lastName' => $this->lastName,
                   'patronymic' => $this->patronymic,
                   'telegram' => $this->telegram,
                   'roles' => $this->roles,
                ],
            ],
            'type' => 'create_user',
        ];
    }
}
