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
        // 1. Create Generics Table
        Schema::create('generics', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('indication')->nullable();
            $table->text('side_effects')->nullable();
            $table->timestamps();
        });

        // 2. Update Products Table with Pharma Fields
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('generic_id')->nullable()->after('brand_id')->constrained('generics')->onDelete('set null');
            $table->string('dosage_form')->nullable()->after('generic_id'); // e.g., Tablet, Syrup, Injection
            $table->string('strength')->nullable()->after('dosage_form'); // e.g., 500mg, 10ml, 1%
            $table->string('registration_number')->nullable()->after('strength'); // DAR (Drug Administration Registration)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('generic_id');
            $table->dropColumn(['dosage_form', 'strength', 'registration_number']);
        });
        Schema::dropIfExists('generics');
    }
};
