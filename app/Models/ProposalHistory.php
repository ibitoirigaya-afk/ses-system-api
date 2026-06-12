<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalHistory extends Model
{
    protected $fillable = [
        'project_id',
        'engineer_id',
        'proposed_date',
        'interview_date',
        'interview_result',
        'status',
        'memo',
        'deleted_at',
    ];

    protected $casts = [
        'proposed_date' => 'date',
        'interview_date' => 'date',
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