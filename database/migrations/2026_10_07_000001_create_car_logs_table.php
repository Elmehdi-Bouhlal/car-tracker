<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('car_ident', 64);
            $table->json('changes');
            $table->json('snapshot');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('car_ident')
                ->references('ident')
                ->on('car')
                ->cascadeOnDelete();

            $table->index(['car_ident', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_logs');
    }
};
