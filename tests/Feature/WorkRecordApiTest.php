<?php

namespace Tests\Feature;

use App\Models\Engineer;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use App\Models\WorkRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkRecordApiTest extends TestCase
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

    public function test_can_get_work_records(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        WorkRecord::create([
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'target_month' => '2026-06',
            'working_hours' => 160,
            'billing_amount' => 700000,
            'payment_amount' => 500000,
            'gross_profit' => 200000,
            'memo' => '一覧取得テスト',
        ]);

        $response = $this->getJson('/api/work-records');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'target_month' => '2026-06',
                'working_hours' => 160,
                'billing_amount' => 700000,
                'payment_amount' => 500000,
                'gross_profit' => 200000,
                'memo' => '一覧取得テスト',
            ])
            ->assertJsonFragment([
                'title' => 'React案件',
            ])
            ->assertJsonFragment([
                'name' => '田中 太郎',
            ]);
    }

    public function test_can_create_work_record(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        $response = $this->postJson('/api/work-records', [
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'target_month' => '2026-07',
            'working_hours' => 150,
            'billing_amount' => 700000,
            'payment_amount' => 520000,
            'gross_profit' => 180000,
            'memo' => '登録テスト',
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'target_month' => '2026-07',
                'working_hours' => 150,
                'billing_amount' => 700000,
                'payment_amount' => 520000,
                'gross_profit' => 180000,
                'memo' => '登録テスト',
            ]);

        $this->assertDatabaseHas('work_records', [
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'target_month' => '2026-07',
            'gross_profit' => 180000,
        ]);
    }

    public function test_cannot_create_work_record_when_payment_exceeds_billing(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        $response = $this->postJson('/api/work-records', [
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'target_month' => '2026-07',
            'working_hours' => 150,
            'billing_amount' => 500000,
            'payment_amount' => 700000,
            'gross_profit' => -200000,
            'memo' => '支払額超過テスト',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_update_work_record(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        $workRecord = WorkRecord::create([
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'target_month' => '2026-06',
            'working_hours' => 160,
            'billing_amount' => 700000,
            'payment_amount' => 500000,
            'gross_profit' => 200000,
            'memo' => '更新前',
        ]);

        $response = $this->putJson("/api/work-records/{$workRecord->id}", [
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'target_month' => '2026-06',
            'working_hours' => 155,
            'billing_amount' => 720000,
            'payment_amount' => 510000,
            'gross_profit' => 210000,
            'memo' => '更新後',
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'working_hours' => 155,
                'billing_amount' => 720000,
                'payment_amount' => 510000,
                'gross_profit' => 210000,
                'memo' => '更新後',
            ]);

        $this->assertDatabaseHas('work_records', [
            'id' => $workRecord->id,
            'working_hours' => 155,
            'gross_profit' => 210000,
            'memo' => '更新後',
        ]);
    }

    public function test_can_delete_work_record(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        $workRecord = WorkRecord::create([
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'target_month' => '2026-06',
            'working_hours' => 160,
            'billing_amount' => 700000,
            'payment_amount' => 500000,
            'gross_profit' => 200000,
            'memo' => '削除対象',
        ]);

        $response = $this->deleteJson("/api/work-records/{$workRecord->id}");

        $response->assertOk();

        $this->assertNotNull($workRecord->fresh()->deleted_at);
    }

    public function test_can_restore_work_record(): void
    {
        [$project, $engineer] = $this->createProjectAndEngineer();

        $workRecord = WorkRecord::create([
            'project_id' => $project->id,
            'engineer_id' => $engineer->id,
            'target_month' => '2026-06',
            'working_hours' => 160,
            'billing_amount' => 700000,
            'payment_amount' => 500000,
            'gross_profit' => 200000,
            'memo' => '復元対象',
            'deleted_at' => now(),
        ]);

        $response = $this->patchJson("/api/work-records/{$workRecord->id}/restore");

        $response->assertOk();

        $this->assertNull($workRecord->fresh()->deleted_at);
    }
}