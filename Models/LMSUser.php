<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\LMSUser
 *
 * @property int $id
 * @property string $email
 * @property int|null $phone
 * @property string $locale
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $patronymic middle_name
 * @property string|null $city
 * @property string|null $telegram
 * @property mixed|null $roles
 * @property int|null $person_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person|null $person
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser wherePatronymic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser whereRoles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser whereTelegram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser whereUpdatedAt($value)
 * @property string $platform_id
 * @method static \Illuminate\Database\Eloquent\Builder|LMSUser wherePlatformId($value)
 * @mixin \Eloquent
 */
class LMSUser extends Model
{
    use HasFactory;

    public $table = 'lms_users';

    protected $guarded = [];

    public $timestamps = true;

    protected $casts = [
        'roles' => 'array',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
