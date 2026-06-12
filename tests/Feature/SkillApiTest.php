<?php

namespace Tests\Feature;

use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_skills(): void
    {
        Skill::create([
            'name' => 'React',
            'category' => 'フロントエンド',
        ]);

        $response = $this->getJson('/api/skills');

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'React',
                'category' => 'フロントエンド',
            ]);
    }

    public function test_can_create_skill(): void
    {
        $response = $this->postJson('/api/skills', [
            'name' => 'Laravel',
            'category' => 'バックエンド',
        ]);

        $response
            ->assertCreated()
            ->assertJsonFragment([
                'name' => 'Laravel',
                'category' => 'バックエンド',
            ]);

        $this->assertDatabaseHas('skills', [
            'name' => 'Laravel',
            'category' => 'バックエンド',
        ]);
    }

    public function test_cannot_create_duplicate_skill(): void
    {
        Skill::create([
            'name' => 'React',
            'category' => 'フロントエンド',
        ]);

        $response = $this->postJson('/api/skills', [
            'name' => 'React',
            'category' => 'フロントエンド',
        ]);

        $response->assertStatus(422);
    }

    public function test_can_update_skill(): void
    {
        $skill = Skill::create([
            'name' => 'React',
            'category' => 'フロントエンド',
        ]);

        $response = $this->putJson("/api/skills/{$skill->id}", [
            'name' => 'React.js',
            'category' => 'フロントエンド',
        ]);

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'React.js',
                'category' => 'フロントエンド',
            ]);

        $this->assertDatabaseHas('skills', [
            'id' => $skill->id,
            'name' => 'React.js',
        ]);
    }

    public function test_can_delete_skill(): void
    {
        $skill = Skill::create([
            'name' => 'React',
            'category' => 'フロントエンド',
        ]);

        $response = $this->deleteJson("/api/skills/{$skill->id}");

        $response->assertOk();

        $this->assertDatabaseMissing('skills', [
            'id' => $skill->id,
        ]);
    }
}