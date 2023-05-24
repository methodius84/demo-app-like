<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GitlabProject extends Model
{
    use HasFactory;
    protected $table = 'gitlab_projects';
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];
    public $timestamps = false;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(GitlabUser::class, 'gitlab_users_projects', 'project_id', 'user_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(GitlabGroup::class);
    }
}
