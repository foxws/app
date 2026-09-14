<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->longText('body');
            $table->unsignedInteger('order')->default(0);
            $table->string('section')->nullable();
            $table->string('source_path');
            $table->string('blob_sha');
            $table->boolean('searchable')->default(true);
            $table->json('seo')->nullable();
            $table->timestamps();

            $table->unique(['version_id', 'slug']);
            $table->unique(['version_id', 'source_path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
