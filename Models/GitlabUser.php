<?php

namespace App\Models;

use App\Http\Controllers\Gitlab\UserController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\GitlabUser
 *
 * @property int $id gitlab id
 * @property string $username gitlab username
 * @property string|null $name last and first name
 * @property string $email
 * @property string $state
 * @property int $is_bot is user a bot
 * @property string|null $web_url profile link
 * @property \Illuminate\Support\Carbon $created_at gitlab creation timestamp
 * @property \Illuminate\Support\Carbon|null $last_sign_in_at last signed in
 * @property \Illuminate\Support\Carbon|null $last_activity_on last activity date
 * @property \Illuminate\Support\Carbon|null $current_sign_in current session
 * @property int $is_admin is user an admin
 * @property int $external is user external
 * @property int|null $created_by user id
 * @property int|null $person_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GitlabGroup> $groups
 * @property-read int|null $groups_count
 * @property-read \App\Models\Person|null $person
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GitlabProject> $projects
 * @property-read int|null $projects_count
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereCurrentSignIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereExternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereIsBot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereLastActivityOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereLastSignInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GitlabUser whereWebUrl($value)
 * @mixin \Eloquent
 */
class GitlabUser extends Model
{
    use HasFactory;
    protected $table = 'gitlab_users';
    protected $guarded = [];
    public $incrementing = false;

    protected $casts = [
        'created_at' => 'datetime',
        'last_activity_on' => 'date',
        'last_sign_in_at' => 'datetime',
        'current_sign_in' => 'datetime',
    ];

    public $timestamps = false;

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(GitlabGroup::class, 'gitlab_users_groups', 'user_id', 'group_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(GitlabProject::class, 'gitlab_users_projects', 'user_id', 'project_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}
