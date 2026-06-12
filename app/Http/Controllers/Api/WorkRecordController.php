<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkRecord;
use Illuminate\Http\Request;

class WorkRecordController extends Controller
{
    public function index()
    {
        $workRecords = WorkRecord::with(['project.skills', 'engineer.skills'])
            ->orderBy('id')
            ->get();

        return response()->json($workRecords);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'engineer_id' => ['required', 'integer', 'exists:engineers,id'],
            'target_month' => ['required', 'string', 'max:255'],
            'working_hours' => ['required', 'integer', 'min:1'],
            'billing_amount' => ['required', 'integer', 'min:1'],
            'payment_amount' => ['required', 'integer', 'min:0'],
            'gross_profit' => ['required', 'integer'],
            'memo' => ['nullable', 'string'],
        ]);

        if ($validated['payment_amount'] > $validated['billing_amount']) {
            return response()->json([
                'message' => '支払額は請求額以下にしてください。',
            ], 422);
        }

        $workRecord = WorkRecord::create($validated);

        return response()->json(
            $workRecord->load(['project.skills', 'engineer.skills']),
            201
        );
    }

    public function show(WorkRecord $workRecord)
    {
        return response()->json(
            $workRecord->load(['project.skills', 'engineer.skills'])
        );
    }

    public function update(Request $request, WorkRecord $workRecord)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'engineer_id' => ['required', 'integer', 'exists:engineers,id'],
            'target_month' => ['required', 'string', 'max:255'],
            'working_hours' => ['required', 'integer', 'min:1'],
            'billing_amount' => ['required', 'integer', 'min:1'],
            'payment_amount' => ['required', 'integer', 'min:0'],
            'gross_profit' => ['required', 'integer'],
            'memo' => ['nullable', 'string'],
        ]);

        if ($validated['payment_amount'] > $validated['billing_amount']) {
            return response()->json([
                'message' => '支払額は請求額以下にしてください。',
            ], 422);
        }

        $workRecord->update($validated);

        return response()->json(
            $workRecord->load(['project.skills', 'engineer.skills'])
        );
    }

    public function destroy(WorkRecord $workRecord)
    {
        $workRecord->update([
            'deleted_at' => now(),
        ]);

        return response()->json([
            'message' => '稼働実績を削除しました。',
        ]);
    }

    public function restore(WorkRecord $workRecord)
    {
        $workRecord->update([
            'deleted_at' => null,
        ]);

        return response()->json(
            $workRecord->load(['project.skills', 'engineer.skills'])
        );
    }
}