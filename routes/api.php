<?php

use App\Http\Controllers\Api\EngineerController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProposalHistoryController;
use App\Http\Controllers\Api\WorkRecordController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BpCompanyController;

Route::apiResource('skills', SkillController::class);
Route::apiResource('projects', ProjectController::class);
Route::apiResource('engineers', EngineerController::class);
Route::apiResource('proposal-histories', ProposalHistoryController::class);
Route::apiResource('bp-companies', BpCompanyController::class);
Route::apiResource('work-records', WorkRecordController::class);

Route::patch('projects/{project}/restore', [ProjectController::class, 'restore']);
Route::patch('engineers/{engineer}/restore', [EngineerController::class, 'restore']);
Route::patch('proposal-histories/{proposalHistory}/restore', [ProposalHistoryController::class, 'restore']);
Route::patch('work-records/{workRecord}/restore', [WorkRecordController::class, 'restore']);
Route::patch('/bp-companies/{id}/restore', [BpCompanyController::class, 'restore']);

Route::post('/login', [AuthController::class, 'login']);
Route::get('/me', [AuthController::class, 'me']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);