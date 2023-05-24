<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\units
 *
 * @property int $id
 * @property string $title
 * @property int $organization_id Организация
 * @property int|null $header_id Идентификатор персоны, являющейся руководителем подразделения
 * @method static \Illuminate\Database\Eloquent\Builder|units newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|units newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|units query()
 * @method static \Illuminate\Database\Eloquent\Builder|units whereHeaderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|units whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|units whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|units whereTitle($value)
 * @mixin \Eloquent
 */
class units extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
}
