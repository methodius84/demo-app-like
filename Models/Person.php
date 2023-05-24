<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Fields\Searchable;

/**
 * App\Models\person
 *
 * @property int $id
 * @property string|null $first_name Name
 * @property string|null $second_name Middle name
 * @property string|null $last_name Surname
 * @property string|null $email corp. mail
 * @property string|null $personal_email own email
 * @property string|null $phone phone
 * @property string|null $telegram
 * @property int|null $org_id id of organisation
 * @property int|null $unit_id id of unit
 * @property string|null $position
 * @property string $created_at
 * @property string|null $type тип трудоустройства
 * @property int $active is employee working
 * @property-read SpacepassUser|null $SpacePassUser
 * @property-read AmoUser|null $amoUser
 * @property-read bitrixAccount|null $bitrixAccount
 * @property-read YandexAccount|null $yandex
 * @property-read Collection|bitrixDepartment[] $departments
 * @property-read int|null $departments_count
 * @property-read Collection|GoogleAccount[] $googleAccounts
 * @property-read int|null $google_accounts_count
 * @property-read namesBitrixAccount|null $namesBitrixAccount
 * @method static \Illuminate\Database\Eloquent\Builder|Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Person query()
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereOrgId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person wherePersonalEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereSecondName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereTelegram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereUnitId($value)
 * @property-read Collection|\App\Models\sipoutInternal[] $sipout
 * @property-read int|null $sipout_count
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SpacepassUser|null $spacepass
 * @method static \Illuminate\Database\Eloquent\Builder|Person whereUpdatedAt($value)
 * @property-read Collection<int, \Laravel\Nova\Actions\ActionEvent> $actions
 * @property-read int|null $actions_count
 * @property-read \App\Models\GitlabUser|null $gitlab
 * @property-read \App\Models\LMSUser|null $lmsUser
 * @property-read \App\Models\Email|null $mailbox
 * @mixin \Eloquent
 */
class Person extends Model
{
    use HasFactory, Actionable, Searchable;
    protected $table = 'person';
    protected $guarded = [];
    public $timestamps = true;

    public function bitrixAccount() : HasOne
    {
        return $this->hasOne(bitrixAccount::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'org_id', 'id');
    }

    public function mailbox() : HasOne
    {
        return $this->hasOne(Email::class);
    }

    public function amoUser() : HasOne
    {
        return $this->hasOne(AmoUser::class);
    }

    public function namesBitrixAccount() : HasOne
    {
        return $this->hasOne(namesBitrixAccount::class);
    }

    public function yandex() : HasOne
    {
        return $this->hasOne(YandexAccount::class, 'person_id', 'id');
    }

    public function departments() : BelongsToMany
    {
        return $this->belongsToMany(bitrixDepartment::class, 'departments_persons', 'person_id', 'department_id');
    }

    public function googleAccounts() : HasMany
    {
        return $this->hasMany(GoogleAccount::class);
    }

    public function spacepass(): HasOne
    {
        return $this->hasOne(SpacepassUser::class);
    }

    public function sipout(): HasMany
    {
        return $this->hasMany(sipoutInternal::class);
    }

    public function gitlab(): HasOne
    {
        return $this->hasOne(GitlabUser::class);
    }

    public function lmsUser(): HasOne
    {
        return $this->hasOne(LMSUser::class);
    }
}
