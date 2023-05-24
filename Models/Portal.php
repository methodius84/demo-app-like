<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Portal
 *
 * @property int $id portal_id
 * @property string $app_id bitrix app_id
 * @property mixed $app_secret bitrix app_secret
 * @property string $domain domain info
 * @property mixed $access_token
 * @property mixed $refresh_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Organization[] $organizations
 * @property-read int|null $organizations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Portal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Portal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Portal query()
 * @method static \Illuminate\Database\Eloquent\Builder|Portal whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portal whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portal whereAppSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portal whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portal whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Portal whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Portal extends Model
{
    use HasFactory;

    protected $table = 'portal';

    protected $fillable = [
        'app_id',
        'app_secret',
        'domain',
        'access_token',
        'refresh_token'
    ];

    protected $casts = [
        'app_secret' => 'encrypted',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    public function organizations() : HasMany{
        return $this->hasMany(Organization::class);
    }
}
