<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'unit_price',
        'status',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function proposalHistories()
{
    return $this->hasMany(ProposalHistory::class);
}

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}