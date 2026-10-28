<?php

namespace Tests\Feature;

use App\Models\Hproject;
use App\Models\Nonproject;
use App\Models\Pengguna;
use App\Models\Project;
use App\Models\Rproject;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KegiatanBulananTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Pengguna::firstOrCreate(
            ['idpengguna' => 'test_user_kegiatan'],
            [
                'nama' => 'Test User Kegiatan',
                'password' => bcrypt('password123'),
                'levelpengguna' => 0,
                'status' => 1,
            ]
        );
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/laporan/kegiatan-bulanan');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_kegiatan_bulanan(): void
    {
        $response = $this->actingAs($this->user)->get('/laporan/kegiatan-bulanan');
        $response->assertStatus(200);
        $response->assertSee('Laporan Kegiatan Bulanan');
        $response->assertSee('Kegiatan Bulanan');
        $response->assertSee('Total Kegiatan');
        $response->assertSee('Kegiatan Project');
        $response->assertSee('Kegiatan Non Project');
        $response->assertSee('Save to Excel');
    }

    public function test_kegiatan_bulanan_merges_hproject_and_nonproject_data(): void
    {
        // 1. Create a Project and Rproject status
        $project = Project::firstOrCreate(
            ['project_name' => 'TEST PROJECT MERGE BULANAN'],
            [
                'tanggal' => '2026-10-01',
                'rproject' => 1,
            ]
        );

        $rproject = Rproject::first();

        // 2. Create an Hproject record
        $hproject = Hproject::create([
            'idproject' => $project->id,
            'rproject' => $rproject->id,
            'tanggal' => '2026-10-25',
            'catatan' => 'Catatan Khusus Hproject Test',
            'progress' => 50,
        ]);

        // 3. Create a Nonproject record
        $nonproject = Nonproject::create([
            'tanggal' => '20261025',
            'kegiatan' => 'Diskusi Khusus Nonproject Test',
            'catatan' => 'Catatan Khusus Nonproject Test',
            'lokasi' => 'Gedung Pusat',
            'pic' => 'Test PIC',
        ]);

        // 4. Request with tahun=2026 and bulan=10
        $response = $this->actingAs($this->user)->get('/laporan/kegiatan-bulanan?tahun=2026&bulan=10');
        $response->assertStatus(200);

        // Assert hproject data is rendered
        $response->assertSee('TEST PROJECT MERGE BULANAN');
        $response->assertSee($rproject->deskripsi);
        $response->assertSee('Catatan Khusus Hproject Test');

        // Assert nonproject data is rendered
        $response->assertSee('LAIN-LAIN');
        $response->assertSee('Diskusi Khusus Nonproject Test');
        $response->assertSee('Catatan Khusus Nonproject Test');
    }

    public function test_kegiatan_bulanan_filters_by_search_keyword(): void
    {
        $nonproject = Nonproject::create([
            'tanggal' => '20261026',
            'kegiatan' => 'Kegiatan Unik Search 987654321',
            'catatan' => 'Catatan Unik Search',
            'lokasi' => 'Online',
            'pic' => 'Search PIC',
        ]);

        $response = $this->actingAs($this->user)->get('/laporan/kegiatan-bulanan?tahun=2026&bulan=10&search=987654321');
        $response->assertStatus(200);
        $response->assertSee('Kegiatan Unik Search 987654321');
        $response->assertSee('LAIN-LAIN');
    }

    public function test_submenu_kegiatan_bulanan_is_present_in_layout(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee(route('laporan.kegiatan_bulanan'));
        $response->assertSee('Kegiatan Bulanan');
    }

    public function test_kegiatan_bulanan_is_sorted_by_project_kegiatan_and_nonproject_comes_after_project(): void
    {
        // 1. Create 2 Projects with names that test alphabetical ordering
        $pB = Project::create([
            'project_name' => 'BBB Project Sorting Test',
            'tanggal' => '2026-10-01',
            'rproject' => 1,
        ]);
        $pA = Project::create([
            'project_name' => 'AAA Project Sorting Test',
            'tanggal' => '2026-10-01',
            'rproject' => 1,
        ]);

        Hproject::create([
            'idproject' => $pB->id,
            'rproject' => 1,
            'tanggal' => '2026-10-10',
            'catatan' => 'Catatan B',
        ]);
        Hproject::create([
            'idproject' => $pA->id,
            'rproject' => 1,
            'tanggal' => '2026-10-10',
            'catatan' => 'Catatan A',
        ]);

        // 2. Create Nonproject item
        Nonproject::create([
            'tanggal' => '20261010',
            'kegiatan' => 'Kegiatan Nonproject Sorting Test',
            'catatan' => 'Catatan Nonproject',
        ]);

        $response = $this->actingAs($this->user)->get('/laporan/kegiatan-bulanan?tahun=2026&bulan=10&search=Sorting+Test');
        $response->assertStatus(200);

        $viewData = $response->viewData('data');
        $items = $viewData->items();

        $this->assertCount(3, $items);
        // First should be AAA Project
        $this->assertEquals('AAA Project Sorting Test', $items[0]->project_kegiatan);
        $this->assertEquals('project', $items[0]->sumber);

        // Second should be BBB Project
        $this->assertEquals('BBB Project Sorting Test', $items[1]->project_kegiatan);
        $this->assertEquals('project', $items[1]->sumber);

        // Third should be Nonproject (LAIN-LAIN)
        $this->assertEquals('LAIN-LAIN', $items[2]->project_kegiatan);
        $this->assertEquals('nonproject', $items[2]->sumber);
    }
}

