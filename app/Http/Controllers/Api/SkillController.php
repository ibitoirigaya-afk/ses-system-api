<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::orderBy('id')->get();

        return response()->json($skills);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
        ]);

        $exists = Skill::where('name', $validated['name'])
            ->where('category', $validated['category'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => '同じ名前・カテゴリのスキルはすでに登録されています。',
            ], 422);
        }

        $skill = Skill::create($validated);

        return response()->json($skill, 201);
    }

    public function show(Skill $skill)
    {
        return response()->json($skill);
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
        ]);

        $exists = Skill::where('name', $validated['name'])
            ->where('category', $validated['category'])
            ->where('id', '!=', $skill->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => '同じ名前・カテゴリのスキルはすでに登録されています。',
            ], 422);
        }

        $skill->update($validated);

        return response()->json($skill);
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return response()->json([
            'message' => 'スキルを削除しました。',
        ]);
    }
}