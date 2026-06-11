<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Engineer;
use Illuminate\Http\Request;

class EngineerController extends Controller
{
    public function index()
    {
        $engineers = Engineer::with('skills')
            ->orderBy('id')
            ->get();

        return response()->json($engineers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:18'],
            'gender' => ['required', 'string', 'max:255'],
            'nearest_station' => ['required', 'string', 'max:255'],
            'desired_unit_price' => ['required', 'integer', 'min:1'],
            'experience_years' => ['required', 'integer', 'min:0'],
            'available_date' => ['required', 'date'],
            'desired_location' => ['required', 'string', 'max:255'],
            'desired_conditions' => ['required', 'string'],
            'career_summary' => ['required', 'string'],
            'status' => ['required', 'string', 'max:255'],
            'skill_ids' => ['required', 'array', 'min:1'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ]);

        $engineer = Engineer::create([
            'user_id' => $validated['user_id'],
            'name' => $validated['name'],
            'company_name' => $validated['company_name'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'nearest_station' => $validated['nearest_station'],
            'desired_unit_price' => $validated['desired_unit_price'],
            'experience_years' => $validated['experience_years'],
            'available_date' => $validated['available_date'],
            'desired_location' => $validated['desired_location'],
            'desired_conditions' => $validated['desired_conditions'],
            'career_summary' => $validated['career_summary'],
            'status' => $validated['status'],
        ]);

        $engineer->skills()->sync($validated['skill_ids']);

        return response()->json(
            $engineer->load('skills'),
            201
        );
    }

    public function show(Engineer $engineer)
    {
        return response()->json($engineer->load('skills'));
    }

    public function update(Request $request, Engineer $engineer)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:18'],
            'gender' => ['required', 'string', 'max:255'],
            'nearest_station' => ['required', 'string', 'max:255'],
            'desired_unit_price' => ['required', 'integer', 'min:1'],
            'experience_years' => ['required', 'integer', 'min:0'],
            'available_date' => ['required', 'date'],
            'desired_location' => ['required', 'string', 'max:255'],
            'desired_conditions' => ['required', 'string'],
            'career_summary' => ['required', 'string'],
            'status' => ['required', 'string', 'max:255'],
            'skill_ids' => ['required', 'array', 'min:1'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ]);

        $engineer->update([
            'user_id' => $validated['user_id'],
            'name' => $validated['name'],
            'company_name' => $validated['company_name'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'nearest_station' => $validated['nearest_station'],
            'desired_unit_price' => $validated['desired_unit_price'],
            'experience_years' => $validated['experience_years'],
            'available_date' => $validated['available_date'],
            'desired_location' => $validated['desired_location'],
            'desired_conditions' => $validated['desired_conditions'],
            'career_summary' => $validated['career_summary'],
            'status' => $validated['status'],
        ]);

        $engineer->skills()->sync($validated['skill_ids']);

        return response()->json($engineer->load('skills'));
    }

    public function destroy(Engineer $engineer)
    {
        $engineer->update([
            'deleted_at' => now(),
        ]);

        return response()->json([
            'message' => '要員を削除しました。',
        ]);
    }

    public function restore(Engineer $engineer)
    {
        $engineer->update([
            'deleted_at' => null,
        ]);

        return response()->json($engineer->load('skills'));
    }
}