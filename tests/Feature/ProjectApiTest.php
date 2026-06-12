<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_projects(): void
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

        $response = $this->getJson('/api/projects');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'title' => 'React案件',
                'location' => '東京都',
                'unit_price' => 700000,
            ])
            ->assertJsonFragment([
                'name' => 'React',
                'category' => 'フロントエンド',
            ]);
    }

    public function test_can_create_project(): void
    {
        $user = User::factory()->create();

        $skill = Skill::create([
            'name' => 'Laravel',
            'category' => 'バックエンド',
        ]);

        $response = $this->postJson('/api/projects', [
            'user_id' => $user->id,
            'title' => 'Laravel案件',
            'description' => 'Laravel API開発案件です。',
            'location' => 'リモート',
            'unit_price' => 800000,
            'status' => '募集中',
            'skill_ids' => [$skill->id],
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'title' => 'Laravel案件',
                'location' => 'リモート',
                'unit_price' => 800000,
            ])
            ->assertJsonFragment([
                'name' => 'Laravel',
            ]);

        $this->assertDatabaseHas('projects', [
            'title' => 'Laravel案件',
            'unit_price' => 800000,
        ]);

        $this->assertDatabaseHas('project_skill', [
            'skill_id' => $skill->id,
        ]);
    }

    public function test_cannot_create_project_without_skill(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/projects', [
            'user_id' => $user->id,
            'title' => 'スキルなし案件',
            'description' => '必要スキルがない案件です。',
            'location' => '東京都',
            'unit_price' => 600000,
            'status' => '募集中',
            'skill_ids' => [],
        ]);

        $response->assertStatus(422);
    }

    public function test_can_update_project(): void
    {
        $user = User::factory()->create();

        $skill = Skill::create([
            'name' => 'React',
            'category' => 'フロントエンド',
        ]);

        $project = Project::create([
            'user_id' => $user->id,
            'title' => 'React案件',
            'description' => 'React案件です。',
            'location' => '東京都',
            'unit_price' => 700000,
            'status' => '募集中',
        ]);

        $project->skills()->sync([$skill->id]);

        $response = $this->putJson("/api/projects/{$project->id}", [
            'user_id' => $user->id,
            'title' => 'React案件 修正版',
            'description' => 'ReactとTypeScript案件です。',
            'location' => 'リモート',
            'unit_price' => 750000,
            'status' => '提案中',
            'skill_ids' => [$skill->id],
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'title' => 'React案件 修正版',
                'location' => 'リモート',
                'unit_price' => 750000,
                'status' => '提案中',
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'React案件 修正版',
            'status' => '提案中',
        ]);
    }

    public function test_can_delete_project(): void
    {
        $user = User::factory()->create();

        $project = Project::create([
            'user_id' => $user->id,
            'title' => '削除対象案件',
            'description' => '削除テスト用案件です。',
            'location' => '東京都',
            'unit_price' => 700000,
            'status' => '募集中',
        ]);

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertOk();

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
        ]);

        $this->assertNotNull($project->fresh()->deleted_at);
    }

    public function test_can_restore_project(): void
    {
        $user = User::factory()->create();

        $project = Project::create([
            'user_id' => $user->id,
            'title' => '復元対象案件',
            'description' => '復元テスト用案件です。',
            'location' => '東京都',
            'unit_price' => 700000,
            'status' => '募集中',
            'deleted_at' => now(),
        ]);

        $response = $this->patchJson("/api/projects/{$project->id}/restore");

        $response->assertOk();

        $this->assertNull($project->fresh()->deleted_at);
    }
}