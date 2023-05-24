<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Email
 *
 * @property int $id
 * @property string $username email username
 * @property int $active
 * @property string $domain
 * @property string|null $name
 * @property string $local_part email nickname before @
 * @property int|null $quota email messages capacity. 0=infinity usage
 * @property int $quota_used used capacity of max quota
 * @property mixed $attributes mailbox settings
 * @property int $last_imap_login
 * @property int $last_smtp_login
 * @property int $last_pop3_login
 * @property int $is_relayed is mailbox relayed to another
 * @property int|null $person_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Email newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Email query()
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereIsRelayed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereLastImapLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereLastPop3Login($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereLastSmtpLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereLocalPart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereQuotaUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Email whereUsername($value)
 * @property-read \App\Models\Person|null $person
 * @mixin \Eloquent
 */
class Email extends Model
{
    use HasFactory;

    protected $table = 'emails';

    protected $guarded = ['id'];

    public $timestamps = true;


    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
