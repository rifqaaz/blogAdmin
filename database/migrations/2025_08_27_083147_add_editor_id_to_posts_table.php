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
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('editor_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->after('user_id');

            $table->timestamp('approved_at')->nullable();

            $table->boolean('is_active')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['editor_id']);
            $table->dropColumn(['editor_id', 'approved_at']);
            $table->boolean('is_active')->default(1)->change();
        });
    }
};
