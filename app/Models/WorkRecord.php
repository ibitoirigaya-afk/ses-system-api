<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkRecord extends Model
{
    protected $fillable = [
        'project_id',
        'engineer_id',
        'target_month',
        'working_hours',
        'billing_amount',
        'payment_amount',
        'gross_profit',
        'memo',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function engineer()
    {
        return $this->belongsTo(Engineer::class);
    }
}