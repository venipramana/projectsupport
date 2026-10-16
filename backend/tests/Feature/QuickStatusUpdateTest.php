<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Hproject;
use App\Models\Rproject;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuickStatusUpdateTest extends TestCase
{
    use DatabaseTransactions;

    protected function getAdminUser()
    {
        return Pengguna::where('levelpengguna', 0)->first();
    }

    public function test_api_rproject_list_returns_active_statuses(): void
    {
        $user = $this->getAdminUser();
        $response = $this->actingAs($user)->getJson(route('rproject.api_list'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'deskripsi', 'icon', 'icon_url', 'status']
            ]
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_unauthenticated_user_cannot_quick_update_status(): void
    {
        $response = $this->postJson(route('project.quick_status_update'), [
            'idproject' => 1,
            'rproject' => 2
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_can_quick_update_project_status_and_logs_hproject(): void
    {
        $admin = $this->getAdminUser();
        $this->assertNotNull($admin, 'Admin user must exist in database');

        $project = Project::create([
            'project_name' => 'Donut Context Menu Test Project',
            'rproject' => 1,
            'tanggal' => '2026-10-16',
        ]);

        $targetStatus = Rproject::where('id', '!=', 1)->first();
        $this->assertNotNull($targetStatus, 'Target status must exist');

        $response = $this->actingAs($admin)->postJson(route('project.quick_status_update'), [
            'idproject' => $project->id,
            'rproject' => $targetStatus->id
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'idproject' => $project->id,
                'rproject' => $targetStatus->id,
                'status_name' => $targetStatus->deskripsi,
            ]
        ]);

        // Verify project table updated
        $project->refresh();
        $this->assertEquals($targetStatus->id, $project->rproject);
        $this->assertNotNull($project->tgl_update);

        // Verify hproject inserted
        $latestHproject = Hproject::where('idproject', $project->id)->latest('id')->first();
        $this->assertNotNull($latestHproject);
        $this->assertEquals($targetStatus->id, $latestHproject->rproject);
        $this->assertStringContainsString($targetStatus->deskripsi, $latestHproject->catatan);
    }

    public function test_validation_fails_on_invalid_data(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->postJson(route('project.quick_status_update'), [
            'idproject' => 99999999, // non-existent
            'rproject' => 99999999
        ]);

        $response->assertStatus(422);
    }
}
