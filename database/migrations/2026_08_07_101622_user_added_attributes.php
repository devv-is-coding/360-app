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
            $table->renameColumn('name', 'username');
            $table->renameColumn('created_at', 'created_on');
            $table->renameColumn('updated_at', 'updated_on');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->change();

            $table->string('first_name')->after('email');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->after('middle_name');
            $table->string('contact_number', 20)->nullable()->after('last_name');
            $table->string('profile_picture')->nullable()->after('contact_number');
            $table->unsignedTinyInteger('role')->default(0)->after('profile_picture');
            $table->boolean('is_active')->default(true)->after('role');
            $table->softDeletes('deleted_on')->after('updated_on');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'contact_number',
                'profile_picture',
                'role',
                'is_active',
                'deleted_on',
            ]);

            $table->dropUnique(['username']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('username', 'name');
            $table->renameColumn('created_on', 'created_at');
            $table->renameColumn('updated_on', 'updated_at');
        });
    }
};
