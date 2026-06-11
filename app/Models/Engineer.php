<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Engineer extends Model
{
    protected $fillable = [
        'user_id',
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
        'deleted_at',
    ];

    protected $casts = [
        'available_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    public function skills()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}