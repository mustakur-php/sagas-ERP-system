<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // users
            ['name' => 'عرض المستخدمين', 'slug' => 'view_users', 'group' => 'users'],
            ['name' => 'إضافة مستخدم', 'slug' => 'create_users', 'group' => 'users'],
            ['name' => 'تعديل مستخدم', 'slug' => 'edit_users', 'group' => 'users'],
            ['name' => 'حذف مستخدم', 'slug' => 'delete_users', 'group' => 'users'],

            // roles
            ['name' => 'عرض الأدوار', 'slug' => 'view_roles', 'group' => 'roles'],
            ['name' => 'إضافة دور', 'slug' => 'create_roles', 'group' => 'roles'],
            ['name' => 'تعديل دور', 'slug' => 'edit_roles', 'group' => 'roles'],
            ['name' => 'حذف دور', 'slug' => 'delete_roles', 'group' => 'roles'],

            // maintenance requests
            ['name' => 'عرض البلاغات', 'slug' => 'view_maintenance_requests', 'group' => 'maintenance_requests'],
            ['name' => 'إضافة بلاغ', 'slug' => 'create_maintenance_requests', 'group' => 'maintenance_requests'],
            ['name' => 'تعديل بلاغ', 'slug' => 'edit_maintenance_requests', 'group' => 'maintenance_requests'],
            ['name' => 'حذف بلاغ', 'slug' => 'delete_maintenance_requests', 'group' => 'maintenance_requests'],

            // stations
            ['name' => 'عرض المحطات', 'slug' => 'view_stations', 'group' => 'stations'],
            ['name' => 'إضافة محطة', 'slug' => 'create_stations', 'group' => 'stations'],
            ['name' => 'تعديل محطة', 'slug' => 'edit_stations', 'group' => 'stations'],
            ['name' => 'حذف محطة', 'slug' => 'delete_stations', 'group' => 'stations'],

            // companies
            ['name' => 'عرض الشركات', 'slug' => 'view_companies', 'group' => 'companies'],
            ['name' => 'إضافة شركة', 'slug' => 'create_companies', 'group' => 'companies'],
            ['name' => 'تعديل شركة', 'slug' => 'edit_companies', 'group' => 'companies'],
            ['name' => 'حذف شركة', 'slug' => 'delete_companies', 'group' => 'companies'],

            // fuel orders
            ['name' => 'عرض جميع طلبات الوقود', 'slug' => 'view_all_fuel_orders', 'group' => 'fuel_orders'],
            ['name' => 'إنشاء طلب وقود', 'slug' => 'create_fuel_orders', 'group' => 'fuel_orders'],
            ['name' => 'اعتماد طلب وقود', 'slug' => 'approve_fuel_orders', 'group' => 'fuel_orders'],
            ['name' => 'رفض طلب وقود', 'slug' => 'reject_fuel_orders', 'group' => 'fuel_orders'],
            ['name' => 'استلام طلب وقود', 'slug' => 'receive_fuel_orders', 'group' => 'fuel_orders'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}