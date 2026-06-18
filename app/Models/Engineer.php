<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Engineer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'bp_company_id',
        'name',
        'company_name',
        'age',
        'gender',
        'nearest_station',
        'desired_unit_price',
        'experience_years',
        'available_date',
        'desired_location',
        'desired_conditions',
        'career_summary',
        'status',
    ];

    protected $casts = [
        'available_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    public function bpCompany()
    {
        return $this->belongsTo(BpCompany::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function proposalHistories()
    {
        return $this->hasMany(ProposalHistory::class);
    }

    public function workRecords()
    {
        return $this->hasMany(WorkRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}