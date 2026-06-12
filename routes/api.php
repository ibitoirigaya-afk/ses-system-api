<?php

use App\Http\Controllers\Api\EngineerController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProposalHistoryController;

Route::apiResource('skills', SkillController::class);
Route::apiResource('projects', ProjectController::class);
Route::apiResource('engineers', EngineerController::class);
Route::apiResource('proposal-histories', ProposalHistoryController::class);

Route::patch('projects/{project}/restore', [ProjectController::class, 'restore']);
Route::patch('engineers/{engineer}/restore', [EngineerController::class, 'restore']);
Route::patch('proposal-histories/{proposalHistory}/restore', [ProposalHistoryController::class, 'restore']);