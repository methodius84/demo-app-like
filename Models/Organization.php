<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Organization
 *
 * @property int $id
 * @property int|null $portal_id
 * @property string $full_title Полное наименование организации
 * @property string $short_title Краткое наименование организации
 * @property int $inn add inn as bigint
 * @property int $kpp add kpp as bigint
 * @property int $connect_id привязываю организацию к определенному коннекту
 * @property-read Portal|null $portal
 * @method static \Illuminate\Database\Eloquent\Builder|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereConnectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereFullTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereInn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereKpp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization wherePortalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereShortTitle($value)
 * @mixin \Eloquent
 */
class Organization extends Model
{
    use HasFactory;
    protected $table = 'organizations';
    protected $guarded = [];
    public $timestamps = false;


    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class, 'org_id', 'id');
    }
}
