<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==========================================
        // 1. CREATE PERMISSIONS
        // ==========================================
        $permissions = [
            // Student Permissions
            'view_students',
            'create_students',
            'edit_students',
            'delete_students',

            // Staff Permissions
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff',
            'manage_staff',

            // Class Permissions
            'view_classes',
            'create_classes',
            'edit_classes',
            'delete_classes',
            'manage_classes',

            // Subject Permissions
            'view_subjects',
            'create_subjects',
            'edit_subjects',
            'delete_subjects',
            'manage_subjects',

            // Academic Permissions
            'view_academic_years',
            'create_academic_years',
            'edit_academic_years',
            'delete_academic_years',
            'manage_academic',

            // Exam/Grade Permissions
            'view_exams',
            'create_exams',
            'edit_exams',
            'delete_exams',
            'enter_grades',
            'view_grades',
            'edit_grades',
            'approve_grades',

            // Fee Permissions
            'view_fees',
            'create_fees',
            'edit_fees',
            'delete_fees',
            'process_payments',
            'view_reports',

            // Admin Permissions
            'manage_admins',
            'manage_settings',
            'manage_updates',

            // Module Permissions
            'manage_modules',
            'view_modules',

            // Hostel Permissions
            'manage_hostel',

            // Timetable Permissions
            'manage_timetable',

            // Transport Permissions
            'manage_transport',

            // Library Permissions
            'manage_library',

            // Inventory Permissions
            'manage_inventory',

            // Parent Permissions
            'manage_parents',
            'view_parents',

            // Report Permissions
            'view_reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ==========================================
        // 2. CREATE ROLES & ASSIGN PERMISSIONS
        // ==========================================

        // Super Admin - Has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Admin - School Administrator
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions([
            'view_students', 'create_students', 'edit_students', 'delete_students',
            'view_staff', 'create_staff', 'edit_staff', 'delete_staff', 'manage_staff',
            'view_classes', 'create_classes', 'edit_classes', 'delete_classes', 'manage_classes',
            'view_subjects', 'create_subjects', 'edit_subjects', 'delete_subjects', 'manage_subjects',
            'view_academic_years', 'create_academic_years', 'edit_academic_years', 'delete_academic_years', 'manage_academic',
            'view_exams', 'create_exams', 'edit_exams', 'delete_exams',
            'enter_grades', 'view_grades', 'edit_grades',
            'view_fees', 'create_fees', 'edit_fees', 'delete_fees',
            'process_payments', 'view_reports',
            'manage_modules', 'view_modules',
            'manage_hostel',
            'manage_timetable',
            'manage_transport',
            'manage_library',
            'manage_inventory',
            'manage_parents', 'view_parents',
            'manage_settings',
        ]);

        // Teacher
        $teacher = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $teacher->syncPermissions([
            'view_students',
            'view_staff',
            'view_classes',
            'view_subjects',
            'view_exams',
            'enter_grades',
            'view_grades',
        ]);

        // Accountant
        $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'view_fees',
            'create_fees',
            'edit_fees',
            'delete_fees',
            'process_payments',
            'view_reports',
        ]);

        // Parent
        $parent = Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        $parent->syncPermissions([
            'view_students',
            'view_grades',
            'view_fees',
        ]);

        // Student
        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $student->syncPermissions([
            'view_grades',
        ]);
    }
}
