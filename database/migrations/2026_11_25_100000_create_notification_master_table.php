<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Notification Master: admin yahan se sab members ke liye notification (announcement) add karta hai.
 * is_default = 1 wala notification member panel me sabse upar pinned rehta hai (ek time par sirf ek).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_master', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->longText('description');                 // CKEditor ka HTML
            $table->boolean('is_default')->default(false);   // pinned on top
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        // Permission bana ke Admin role ko de do (baaki masters ki tarah admin guard)
        $now = now();
        $permissionId = DB::table('permissions')
            ->where(['name' => 'Notification Master', 'guard_name' => 'admin'])
            ->value('id');

        if (!$permissionId) {
            $permissionId = DB::table('permissions')->insertGetId([
                'name' => 'Notification Master', 'guard_name' => 'admin',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $adminRoleId = DB::table('roles')->where(['name' => 'Admin', 'guard_name' => 'admin'])->value('id');
        if ($adminRoleId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permissionId, 'role_id' => $adminRoleId,
            ]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_master');

        $permissionId = DB::table('permissions')
            ->where(['name' => 'Notification Master', 'guard_name' => 'admin'])
            ->value('id');

        if ($permissionId) {
            DB::table('role_has_permissions')->where('permission_id', $permissionId)->delete();
            DB::table('model_has_permissions')->where('permission_id', $permissionId)->delete();
            DB::table('permissions')->where('id', $permissionId)->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
