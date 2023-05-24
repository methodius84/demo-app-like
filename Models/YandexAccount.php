<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\YandexAccount
 *
 * @property int $id
 * @property int $platform_id Идентификатор пользователя в коннекте
 * @property string $is_active Активен ли пользователь
 * @property string $created Дата создания пользователя на платформе
 * @property string $first_name Имя пользователя коннект
 * @property string $last_name Фамилия пользователя коннект
 * @property string|null $second_name Отчество пользователя коннект
 * @property string|null $gender Пол пользователя в коннекте
 * @property string|null $birthdate Дата рождения
 * @property string $department_id Идентификатор департамента, которому принадлежит сотрудник
 * @property string|null $position Занимаемая должность
 * @property string $email Основной адрес электронной почты пользователя на коннекте
 * @property mixed|null $contacts Дополнительные контакты пользователя
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $person_id
 * @property-read \App\Models\Person|null $person
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereContacts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount wherePlatformId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereSecondName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereUpdatedAt($value)
 * @property int $is_admin
 * @property int $is_robot
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|YandexAccount whereIsRobot($value)
 * @mixin \Eloquent
 */
class YandexAccount extends Model
{
    use HasFactory;
    public $table = 'connect_accounts';
    protected $primaryKey = 'platform_id';
    protected $guarded = [];

    public function person() : BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id', 'id');
    }
}
