<?php

namespace Tests\Feature;

use App\Models\Engineer;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EngineerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_engineers(): void
    {
        $user = User::factory()->create();

        $skill = Skill::create([
            'name' => 'React',
            'category' => 'フロントエンド',
        ]);

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

        $response = $this->getJson('/api/engineers');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => '田中 太郎',
                'company_name' => 'サンプル株式会社',
                'nearest_station' => '新宿',
            ])
            ->assertJsonFragment([
                'name' => 'React',
                'category' => 'フロントエンド',
            ]);
    }

    public function test_can_create_engineer(): void
    {
        $user = User::factory()->create();

        $skill = Skill::create([
            'name' => 'Laravel',
            'category' => 'バックエンド',
        ]);

        $response = $this->postJson('/api/engineers', [
            'user_id' => $user->id,
            'name' => '佐藤 花子',
            'company_name' => 'テスト株式会社',
            'age' => 30,
            'gender' => '女性',
            'nearest_station' => '渋谷',
            'desired_unit_price' => 700000,
            'experience_years' => 5,
            'available_date' => '2026-08-01',
            'desired_location' => 'リモート',
            'desired_conditions' => '週3リモート希望',
            'career_summary' => 'LaravelとReactを使った業務システム開発を経験。',
            'status' => '稼働可能',
            'skill_ids' => [$skill->id],
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => '佐藤 花子',
                'company_name' => 'テスト株式会社',
                'desired_unit_price' => 700000,
            ])
            ->assertJsonFragment([
                'name' => 'Laravel',
            ]);

        $this->assertDatabaseHas('engineers', [
            'name' => '佐藤 花子',
            'company_name' => 'テスト株式会社',
        ]);

        $this->assertDatabaseHas('engineer_skill', [
            'skill_id' => $skill->id,
        ]);
    }

    public function test_cannot_create_engineer_without_skill(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/engineers', [
            'user_id' => $user->id,
            'name' => 'スキルなし要員',
            'company_name' => 'テスト株式会社',
            'age' => 25,
            'gender' => '男性',
            'nearest_station' => '池袋',
            'desired_unit_price' => 600000,
            'experience_years' => 3,
            'available_date' => '2026-07-01',
            'desired_location' => '東京都',
            'desired_conditions' => '常駐可',
            'career_summary' => '開発経験あり。',
            'status' => '稼働可能',
            'skill_ids' => [],
        ]);

        $response->assertStatus(422);
    }

    public function test_can_update_engineer(): void
    {
        $user = User::factory()->create();

        $skill = Skill::create([
            'name' => 'React',
            'category' => 'フロントエンド',
        ]);

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
            'career_summary' => 'React経験あり。',
            'status' => '稼働可能',
        ]);

        $engineer->skills()->sync([$skill->id]);

        $response = $this->putJson("/api/engineers/{$engineer->id}", [
            'user_id' => $user->id,
            'name' => '田中 太郎 修正版',
            'company_name' => 'サンプル株式会社',
            'age' => 29,
            'gender' => '男性',
            'nearest_station' => '品川',
            'desired_unit_price' => 700000,
            'experience_years' => 5,
            'available_date' => '2026-08-01',
            'desired_location' => 'リモート',
            'desired_conditions' => 'フルリモート希望',
            'career_summary' => 'ReactとTypeScript経験あり。',
            'status' => '提案中',
            'skill_ids' => [$skill->id],
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => '田中 太郎 修正版',
                'nearest_station' => '品川',
                'status' => '提案中',
            ]);

        $this->assertDatabaseHas('engineers', [
            'id' => $engineer->id,
            'name' => '田中 太郎 修正版',
            'nearest_station' => '品川',
        ]);
    }

    public function test_can_delete_engineer(): void
    {
        $user = User::factory()->create();

        $engineer = Engineer::create([
            'user_id' => $user->id,
            'name' => '削除対象要員',
            'company_name' => 'サンプル株式会社',
            'age' => 28,
            'gender' => '男性',
            'nearest_station' => '新宿',
            'desired_unit_price' => 650000,
            'experience_years' => 4,
            'available_date' => '2026-07-01',
            'desired_location' => '東京都',
            'desired_conditions' => 'リモート併用希望',
            'career_summary' => '削除テスト用。',
            'status' => '稼働可能',
        ]);

        $response = $this->deleteJson("/api/engineers/{$engineer->id}");

        $response->assertOk();

        $this->assertDatabaseHas('engineers', [
            'id' => $engineer->id,
        ]);

        $this->assertNotNull($engineer->fresh()->deleted_at);
    }

    public function test_can_restore_engineer(): void
    {
        $user = User::factory()->create();

        $engineer = Engineer::create([
            'user_id' => $user->id,
            'name' => '復元対象要員',
            'company_name' => 'サンプル株式会社',
            'age' => 28,
            'gender' => '男性',
            'nearest_station' => '新宿',
            'desired_unit_price' => 650000,
            'experience_years' => 4,
            'available_date' => '2026-07-01',
            'desired_location' => '東京都',
            'desired_conditions' => 'リモート併用希望',
            'career_summary' => '復元テスト用。',
            'status' => '停止中',
            'deleted_at' => now(),
        ]);

        $response = $this->patchJson("/api/engineers/{$engineer->id}/restore");

        $response->assertOk();

        $this->assertNull($engineer->fresh()->deleted_at);
    }
}