<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\DepartmentsPersons
 *
 * @property int $person_id
 * @property int $department_id
 * @property-read \App\Models\bitrixDepartment $department
 * @property-read \App\Models\Person $person
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentsPersons newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentsPersons newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentsPersons query()
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentsPersons whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DepartmentsPersons wherePersonId($value)
 * @mixin \Eloquent
 */
class DepartmentsPersons extends Model
{
    use HasFactory;

    protected $table = 'departments_persons';
    protected $guarded = [];
    public $timestamps = false;

    public function person() : BelongsTo
    {
        return $this->belongsTo(Person::class,'person_id', 'id');
    }

    public function department() : BelongsTo
    {
        return $this->belongsTo(bitrixDepartment::class, 'department_id', 'department_id');
    }
}
