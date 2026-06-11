<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('skills')
            ->orderBy('id')
            ->get();

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'max:255'],
            'skill_ids' => ['required', 'array', 'min:1'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ]);

        $project = Project::create([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'unit_price' => $validated['unit_price'],
            'status' => $validated['status'],
        ]);

        $project->skills()->sync($validated['skill_ids']);

        return response()->json(
            $project->load('skills'),
            201
        );
    }

    public function show(Project $project)
    {
        return response()->json($project->load('skills'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'max:255'],
            'skill_ids' => ['required', 'array', 'min:1'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ]);

        $project->update([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'unit_price' => $validated['unit_price'],
            'status' => $validated['status'],
        ]);

        $project->skills()->sync($validated['skill_ids']);

        return response()->json($project->load('skills'));
    }

    public function destroy(Project $project)
    {
        $project->update([
            'deleted_at' => now(),
        ]);

        return response()->json([
            'message' => '案件を削除しました。',
        ]);
    }

    public function restore(Project $project)
{
    $project->update([
        'deleted_at' => null,
    ]);

    return response()->json($project->load('skills'));
}
}