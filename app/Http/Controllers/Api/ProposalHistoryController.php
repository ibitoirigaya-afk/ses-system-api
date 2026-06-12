<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProposalHistory;
use Illuminate\Http\Request;

class ProposalHistoryController extends Controller
{
    public function index()
    {
        $proposalHistories = ProposalHistory::with(['project.skills', 'engineer.skills'])
            ->orderBy('id')
            ->get();

        return response()->json($proposalHistories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'engineer_id' => ['required', 'integer', 'exists:engineers,id'],
            'proposed_date' => ['required', 'date'],
            'interview_date' => ['nullable', 'date'],
            'interview_result' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'memo' => ['nullable', 'string'],
        ]);

        $proposalHistory = ProposalHistory::create($validated);

        return response()->json(
            $proposalHistory->load(['project.skills', 'engineer.skills']),
            201
        );
    }

    public function show(ProposalHistory $proposalHistory)
    {
        return response()->json(
            $proposalHistory->load(['project.skills', 'engineer.skills'])
        );
    }

    public function update(Request $request, ProposalHistory $proposalHistory)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'engineer_id' => ['required', 'integer', 'exists:engineers,id'],
            'proposed_date' => ['required', 'date'],
            'interview_date' => ['nullable', 'date'],
            'interview_result' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:255'],
            'memo' => ['nullable', 'string'],
        ]);

        $proposalHistory->update($validated);

        return response()->json(
            $proposalHistory->load(['project.skills', 'engineer.skills'])
        );
    }

    public function destroy(ProposalHistory $proposalHistory)
    {
        $proposalHistory->update([
            'deleted_at' => now(),
        ]);

        return response()->json([
            'message' => '提案履歴を削除しました。',
        ]);
    }

    public function restore(ProposalHistory $proposalHistory)
    {
        $proposalHistory->update([
            'deleted_at' => null,
        ]);

        return response()->json(
            $proposalHistory->load(['project.skills', 'engineer.skills'])
        );
    }
}