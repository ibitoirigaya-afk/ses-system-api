<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use Illuminate\Support\Facades\Route;

Route::apiResource('skills', SkillController::class);
Route::apiResource('projects', ProjectController::class);

Route::patch('projects/{project}/restore', [ProjectController::class, 'restore']);