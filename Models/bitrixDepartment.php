<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\bitrixDepartment
 *
 * @property int $department_id Идентификатор отдела из платформы
 * @property string $dep_name Наименование подразделения
 * @property int|null $parent_dep Старшее подразделение
 * @property int|null $head Руководитель подразделения
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $organization_id
 * @property-read \App\Models\namesBitrixDepartment|null $namesBitrixDepartment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Person[] $persons
 * @property-read int|null $persons_count
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixDepartment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixDepartment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixDepartment query()
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixDepartment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixDepartment whereDepName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixDepartment whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixDepartment whereHead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixDepartment whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixDepartment whereParentDep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|bitrixDepartment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class bitrixDepartment extends Model
{
    use HasFactory;
    protected $primaryKey = 'department_id';
    protected $guarded = [];

    public function persons() : BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'departments_persons', 'department_id', 'person_id');
    }

    public function namesBitrixDepartment(): HasOne
    {
        return $this->hasOne(namesBitrixDepartment::class, 'like_id', 'department_id');
    }
}
