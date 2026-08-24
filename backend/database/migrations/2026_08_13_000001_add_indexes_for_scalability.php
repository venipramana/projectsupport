<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project', function (Blueprint $table) {
            $table->index('tanggal', 'idx_project_tanggal');
            $table->index('tanggal_awal', 'idx_project_tanggal_awal');
            $table->index('tgl_update', 'idx_project_tgl_update');
            $table->index('rproject', 'idx_project_rproject');
            $table->index('direktorat', 'idx_project_direktorat');
            $table->index('id_catalog', 'idx_project_id_catalog');
            $table->index('leadby', 'idx_project_leadby');
        });

        Schema::table('nonproject', function (Blueprint $table) {
            $table->index('tanggal', 'idx_nonproject_tanggal');
            $table->index('pic', 'idx_nonproject_pic');
        });

        Schema::table('rcatalog', function (Blueprint $table) {
            $table->index('id_direktorat', 'idx_rcatalog_id_direktorat');
        });

        Schema::table('hproject', function (Blueprint $table) {
            $table->index('idproject', 'idx_hproject_idproject');
            $table->index('tanggal', 'idx_hproject_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project', function (Blueprint $table) {
            $table->dropIndex('idx_project_tanggal');
            $table->dropIndex('idx_project_tanggal_awal');
            $table->dropIndex('idx_project_tgl_update');
            $table->dropIndex('idx_project_rproject');
            $table->dropIndex('idx_project_direktorat');
            $table->dropIndex('idx_project_id_catalog');
            $table->dropIndex('idx_project_leadby');
        });

        Schema::table('nonproject', function (Blueprint $table) {
            $table->dropIndex('idx_nonproject_tanggal');
            $table->dropIndex('idx_nonproject_pic');
        });

        Schema::table('rcatalog', function (Blueprint $table) {
            $table->dropIndex('idx_rcatalog_id_direktorat');
        });

        Schema::table('hproject', function (Blueprint $table) {
            $table->dropIndex('idx_hproject_idproject');
            $table->dropIndex('idx_hproject_tanggal');
        });
    }
};
