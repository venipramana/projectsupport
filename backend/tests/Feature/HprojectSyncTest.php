<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Hproject;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HprojectSyncTest extends TestCase
{
    use DatabaseTransactions;

    public function test_hproject_creation_syncs_project_status_and_date(): void
    {
        $project = Project::create([
            'project_name' => 'Test Project Automated',
            'rproject' => 1,
            'catalog_version' => '1.0.0',
        ]);

        $this->assertEquals(1, $project->rproject);

        Hproject::create([
            'idproject' => $project->id,
            'rproject' => 3,
            'tanggal' => '2026-09-21',
            'catatan' => 'Testing progress creation',
            'progress' => 50,
        ]);

        $project->refresh();
        $this->assertEquals(3, $project->rproject);
        $this->assertStringStartsWith('2026-09-21', (string) $project->tgl_update);
    }

    public function test_hproject_deletion_reverts_to_previous_latest_status(): void
    {
        $project = Project::create([
            'project_name' => 'Revert Test Project Automated',
            'rproject' => 1,
            'catalog_version' => '1.0.0',
        ]);

        Hproject::create([
            'idproject' => $project->id,
            'rproject' => 1,
            'tanggal' => '2026-09-18',
            'catatan' => 'First entry',
            'progress' => 10,
        ]);

        $entry2 = Hproject::create([
            'idproject' => $project->id,
            'rproject' => 3,
            'tanggal' => '2026-09-21',
            'catatan' => 'Second entry',
            'progress' => 20,
        ]);

        $project->refresh();
        $this->assertEquals(3, $project->rproject);

        // Delete latest entry
        $entry2->delete();

        $project->refresh();
        $this->assertEquals(1, $project->rproject);
        $this->assertStringStartsWith('2026-09-18', (string) $project->tgl_update);
    }

    public function test_hproject_sync_preserves_latest_when_older_entry_edited(): void
    {
        $project = Project::create([
            'project_name' => 'Older Edit Test Project',
            'rproject' => 1,
            'catalog_version' => '1.0.0',
        ]);

        $olderEntry = Hproject::create([
            'idproject' => $project->id,
            'rproject' => 1,
            'tanggal' => '2026-09-10',
            'catatan' => 'Older entry',
            'progress' => 0,
        ]);

        Hproject::create([
            'idproject' => $project->id,
            'rproject' => 3,
            'tanggal' => '2026-09-21',
            'catatan' => 'Latest entry',
            'progress' => 50,
        ]);

        $project->refresh();
        $this->assertEquals(3, $project->rproject);

        // Edit the older entry (e.g. changing its status to 2)
        $olderEntry->update(['rproject' => 2, 'catatan' => 'Edited older entry']);

        // Project status MUST remain 3 because the latest entry is still 2026-09-21 with status 3
        $project->refresh();
        $this->assertEquals(3, $project->rproject);
    }
}

