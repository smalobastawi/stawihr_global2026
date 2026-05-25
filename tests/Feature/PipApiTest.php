<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PipApiTest extends TestCase
{
    /** @test */
    public function pip_plans_returns_list_for_authenticated_employee()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/pip/plans');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data',
            ]);

        $this->assertIsArray($response->json('data'));
    }

    /** @test */
    public function pip_plan_detail_returns_progress_and_sections_for_owned_plan()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $listResponse = $this->getJson('/api/pip/plans');
        $listResponse->assertStatus(200);

        $plans = $listResponse->json('data');
        if (empty($plans)) {
            $this->markTestSkipped('No PIP plans found for smaloba3.');
        }

        $planId = $plans[0]['id'];

        $detailResponse = $this->getJson('/api/pip/plans/' . $planId);
        $detailResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'status_label',
                    'outcome_label',
                    'progress' => [
                        'overall_percent',
                        'goals',
                        'reviews',
                        'timeline_percent',
                    ],
                    'goals',
                    'review_schedules',
                ],
            ]);
    }
}
