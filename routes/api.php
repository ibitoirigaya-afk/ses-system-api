<?php

use App\Http\Controllers\Api\SkillController;
use Illuminate\Support\Facades\Route;

Route::apiResource('skills', SkillController::class);