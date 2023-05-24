<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\bitrixAccount
 *
 * @property int $id
 * @property int $platform_id Идентификатор в битриксе
 * @property string $user_type UF_SKYPE. Может быть = employee или extranet
 * @property int $department_id
 * @property int $active Активен ли пользователь на платформе
 * @property string|null $first_name Имя
 * @property string|null $last_name Фамилия
 * @property string|null $second_name Отчество
 * @property string $gender Пол
 * @property string|null $email
 * @property string|null $phone PERSONAL_PHONE
 * @property string|null $mobile PERSONAL_MOBILE
 * @property string|null $personal_site Пол
 * @property string|null $birthday День рождения
 * @property string|null $profile_photo Фото профиля
 * @property string|null $city Город
 * @property string|null $position Должность
 * @property string|null $vk_page UF_WEB_SITES — это доп.сайты
 * @property string|null $skype_login UF_SKYPE
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $person_id
 * @property-read \App\Models\Person|null $person
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount wherePersonalSite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount wherePlatformId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereSecondName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereSkypeLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixAccount whereVkPage($value)
 * @mixin \Eloquent
 */
class bitrixAccount extends Model
{
    use HasFactory;
    protected $primaryKey = 'platform_id';
    protected $guarded = [];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class,'person_id', 'id');
    }
}
