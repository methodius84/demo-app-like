<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GitlabGroup extends Model
{
    use HasFactory;

    protected $table = 'gitlab_groups';
    protected $guarded = [];
    protected $casts = [
        'created_at' => 'datetime',
    ];
    public $timestamps = false;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(GitlabUser::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(GitlabProject::class);
    }
}
