<?php

use App\Models\Notes;
use App\Models\Tag;
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
        Schema::create('note_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Notes::class, 'note_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Tag::class, 'tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['note_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_tag');
    }
};
