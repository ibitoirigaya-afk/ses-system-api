<?php

namespace Tests\Feature;

use App\Models\Engineer;
use App\Models\Project;
use App\Models\ProposalHistory;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalHistoryApiTest extends TestCase
{
    use RefreshDatabase;

    private function createProjectAndEngineer(): array
    {
        $user = User::factory()->create();

        $skill = Skill::create([
            'name' => 'React',
            'category' => 'フロントエンド',
        ]);

        $project = Project::create([
            'user_id' => $user->id,
            'title' => 'React案件',
            'description' => 'ReactとTypeScriptを使用した案件です。',
            'location' => '東京都',
            'unit_price' => 700000,
            'status' => '募集中',
        ]);

        $project->skills()->sync([$skill->id]);

        $engineer = Engineer::create([
            'user_id' => $user->id,
            'name' => '田中 太郎',
            'company_name' => 'サンプル株式会社',
            'age' => 28,
            'gender' => '男性',
            'nearest_station' => '新宿',
            'desired_unit_price' => 650000,
            'experience_years' => 4,
            'available_date' => '2026-07-01',
            'desired_location' => '東京都',
            'desired_conditions' => 'リモート併用希望',
            'career_summary' => 'ReactとTypeScriptを中心に開発経験あり。',
            'status' => '稼働可能',
        ]);

        $engineer->skills()->sync([$skill->id]);

        return [$project, $engineer];
    }

    public function test_can_get_proposal_histories(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        ProposalHistory::create([
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'proposed_date' => '2026-06-12',
            'interview_date' => '2026-06-20',
            'interview_result' => '調整中',
            'status' => '面談調整中',
            'memo' => '一覧取得テスト',
        ]);

        $response = $this->getJson('/api/proposal-histories');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'status' => '面談調整中',
                'memo' => '一覧取得テスト',
            ])
            ->assertJsonFragment([
                'title' => 'React案件',
            ])
            ->assertJsonFragment([
                'name' => '田中 太郎',
            ]);
    }

    public function test_can_create_proposal_history(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        $response = $this->postJson('/api/proposal-histories', [
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'proposed_date' => '2026-06-12',
            'interview_date' => '2026-06-20',
            'interview_result' => '調整中',
            'status' => '面談調整中',
            'memo' => '登録テスト',
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'status' => '面談調整中',
                'memo' => '登録テスト',
            ]);

        $this->assertDatabaseHas('proposal_histories', [
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'status' => '面談調整中',
        ]);
    }

    public function test_cannot_create_proposal_history_without_project(): void
    {
        [, $engineer] = $this->createProjectAndEngineer();

        $response = $this->postJson('/api/proposal-histories', [
            'project_id' => 999,
            'engineer_id' => $engineer->id,
            'proposed_date' => '2026-06-12',
            'status' => '提案中',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_update_proposal_history(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        $proposalHistory = ProposalHistory::create([
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'proposed_date' => '2026-06-12',
            'interview_date' => '2026-06-20',
            'interview_result' => '調整中',
            'status' => '面談調整中',
            'memo' => '更新前',
        ]);

        $response = $this->putJson("/api/proposal-histories/{$proposalHistory->id}", [
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'proposed_date' => '2026-06-13',
            'interview_date' => '2026-06-25',
            'interview_result' => '通過',
            'status' => '面談済み',
            'memo' => '更新後',
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'status' => '面談済み',
                'interview_result' => '通過',
                'memo' => '更新後',
            ]);

        $this->assertDatabaseHas('proposal_histories', [
            'id' => $proposalHistory->id,
            'status' => '面談済み',
            'memo' => '更新後',
        ]);
    }

    public function test_can_delete_proposal_history(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        $proposalHistory = ProposalHistory::create([
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'proposed_date' => '2026-06-12',
            'status' => '提案中',
            'memo' => '削除対象',
        ]);

        $response = $this->deleteJson("/api/proposal-histories/{$proposalHistory->id}");

        $response->assertOk();

        $this->assertNotNull($proposalHistory->fresh()->deleted_at);
    }

    public function test_can_restore_proposal_history(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        $proposalHistory = ProposalHistory::create([
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'proposed_date' => '2026-06-12',
            'status' => '提案中',
            'memo' => '復元対象',
            'deleted_at' => now(),
        ]);

        $response = $this->patchJson("/api/proposal-histories/{$proposalHistory->id}/restore");

        $response->assertOk();

        $this->assertNull($proposalHistory->fresh()->deleted_at);
    }
}