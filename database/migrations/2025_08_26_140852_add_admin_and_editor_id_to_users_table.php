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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('assigned_to_admin_id')
                ->nullable()
                ->constrained('users') // References same table
                ->onDelete('set null')
                ->comment('Which admin this editor is assigned to');

            
            $table->foreignId('assigned_to_editor_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Which editor this user is assigned to');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_admin_id']);
            $table->dropForeign(['assigned_to_editor_id']);
            $table->dropColumn(['assigned_to_admin_id', 'assigned_to_editor_id']);
        });
    }
};
