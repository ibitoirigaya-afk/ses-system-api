<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BpCompany;
use Illuminate\Http\Request;

class BpCompanyController extends Controller
{
    /**
     * BP企業一覧
     */
    public function index()
    {
        return response()->json(
            BpCompany::withCount('engineers')
                ->withTrashed()
                ->latest()
                ->get()
        );
    }

    /**
     * BP企業登録
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'memo' => ['nullable', 'string'],
        ]);

        $bpCompany = BpCompany::create($validated);

        return response()->json($bpCompany->loadCount('engineers'), 201);
    }

    /**
     * BP企業詳細
     */
    public function show(BpCompany $bpCompany)
    {
        return response()->json(
            $bpCompany->load(['engineers.skills'])->loadCount('engineers')
        );
    }

    /**
     * BP企業更新
     */
    public function update(Request $request, BpCompany $bpCompany)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'memo' => ['nullable', 'string'],
        ]);

        $bpCompany->update($validated);

        return response()->json($bpCompany->loadCount('engineers'));
    }

    /**
     * BP企業削除
     */
    public function destroy(BpCompany $bpCompany)
    {
        $bpCompany->delete();

        return response()->json([
            'message' => 'BP企業を削除しました。',
        ]);
    }

    /**
     * BP企業復元
     */
    public function restore(int $id)
    {
        $bpCompany = BpCompany::withTrashed()->findOrFail($id);

        $bpCompany->restore();

        return response()->json($bpCompany->fresh()->loadCount('engineers'));
    }
}