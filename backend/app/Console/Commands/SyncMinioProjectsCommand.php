<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Storage;

class SyncMinioProjectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minio:sync-projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing projects with MinIO bucket and create folder_evidence attribute';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Checking project database schema for folder_evidence column...");
        if (!Schema::hasColumn('project', 'folder_evidence')) {
            Schema::table('project', function (Blueprint $table) {
                $table->string('folder_evidence', 255)->nullable();
            });
            $this->info("Successfully added 'folder_evidence' column to 'project' table.");
        } else {
            $this->info("'folder_evidence' column already exists in 'project' table.");
        }

        $projects = Project::all();
        $this->info("Found {$projects->count()} existing projects. Creating MinIO folders...");

        $disk = Storage::disk('minio');
        $createdCount = 0;

        foreach ($projects as $project) {
            // Standar nama folder: evidence_project_{id}
            $folderName = "evidence_project_" . $project->id;
            $keepFilePath = $folderName . "/.keep";

            try {
                if (!$disk->exists($keepFilePath)) {
                    $disk->put($keepFilePath, "");
                }
            } catch (\Exception $e) {
                $msg = $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
                $this->error("Failed connecting or writing to MinIO storage: " . $msg);
                if ($e->getPrevious()) {
                    $this->error("Underlying AWS error: " . $e->getPrevious()->getMessage());
                }
                return 1;
            }

            // Update project attribute if changed or empty
            if ($project->folder_evidence !== $folderName) {
                $project->folder_evidence = $folderName;
                $project->save();
            }

            $createdCount++;
        }

        $this->info("Successfully synced and updated {$createdCount} project folders in MinIO!");
        return 0;
    }
}
