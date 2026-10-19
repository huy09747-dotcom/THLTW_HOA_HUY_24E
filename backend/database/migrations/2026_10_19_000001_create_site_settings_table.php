<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('site_name')->default('HH Shop');
            $table->string('tagline')->default('Công nghệ mỗi ngày');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('address')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('footer_text')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('site_settings'); }
};
