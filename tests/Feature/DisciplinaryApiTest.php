<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DisciplinaryApiTest extends TestCase
{
    /** @test */
    public function disciplinary_cases_returns_list_for_authenticated_employee()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/disciplinary/cases');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data',
            ]);

        $this->assertIsArray($response->json('data'));
    }

    /** @test */
    public function disciplinary_case_detail_requires_access()
    {
        $user = User::where('user_name', 'smaloba3')->first();
        if (!$user) {
            $this->markTestSkipped('Test user smaloba3 not found in database.');
        }

        Sanctum::actingAs($user);

        $listResponse = $this->getJson('/api/disciplinary/cases');
        $listResponse->assertStatus(200);

        $cases = $listResponse->json('data');
        if (empty($cases)) {
            $this->markTestSkipped('No disciplinary cases found for smaloba3.');
        }

        $caseId = $cases[0]['id'];

        $detailResponse = $this->getJson('/api/disciplinary/cases/' . $caseId);
        $detailResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'case_number',
                    'status_name',
                    'viewer_role',
                    'viewer_role_label',
                    'is_subject',
                    'is_assigned_officer',
                    'actions',
                ],
            ]);

        $this->assertArrayNotHasKey('reporter', $detailResponse->json('data'));
    }
}
