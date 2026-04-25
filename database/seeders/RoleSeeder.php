<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'super_admin',
                'slug' => 'super_admin',
            ],
            [
                'name' => 'company_admin',
                'slug' => 'company_admin',
            ],
            [
                'name' => 'station_supervisor',
                'slug' => 'station_supervisor',
            ],
            [
                'name' => 'maintenance_manager',
                'slug' => 'maintenance_manager',
            ],
            [
                'name' => 'technician',
                'slug' => 'technician',
            ],
            [
                'name' => 'finance',
                'slug' => 'finance',
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                ['name' => $roleData['name']]
            );

            if ($roleData['slug'] === 'super_admin') {
                $role->permissions()->sync(Permission::pluck('id')->toArray());
            }

            if ($roleData['slug'] === 'company_admin') {
                $role->permissions()->sync(
                    Permission::whereNotIn('slug', [
                        'delete_companies',
                        'assign_permissions',
                    ])->pluck('id')->toArray()
                );
            }

            if ($roleData['slug'] === 'station_supervisor') {
                $role->permissions()->sync(
                    Permission::whereIn('slug', [
                        'view_stations',
                        'view_station_tanks',
                        'view_station_nozzles',
                        'view_daily_closings',
                        'create_daily_closings',
                        'edit_daily_closings',
                        'submit_daily_closings',
                        'view_fuel_orders',
                        'create_fuel_orders',
                        'edit_fuel_orders',
                        'submit_fuel_orders',
                        'view_maintenance_requests',
                        'create_maintenance_requests',
                        'edit_maintenance_requests',
                    ])->pluck('id')->toArray()
                );
            }

            if ($roleData['slug'] === 'maintenance_manager') {
                $role->permissions()->sync(
                    Permission::whereIn('slug', [
                        'view_maintenance_requests',
                        'edit_maintenance_requests',
                        'update_maintenance_request_status',
                        'forward_maintenance_requests',
                        'view_maintenance_job_orders',
                        'create_maintenance_job_orders',
                        'assign_maintenance_job_orders',
                        'update_maintenance_job_order_status',
                        'complete_maintenance_job_orders',
                    ])->pluck('id')->toArray()
                );
            }

            if ($roleData['slug'] === 'technician') {
                $role->permissions()->sync(
                    Permission::whereIn('slug', [
                        'view_maintenance_job_orders',
                        'update_maintenance_job_order_status',
                        'complete_maintenance_job_orders',
                    ])->pluck('id')->toArray()
                );
            }

            if ($roleData['slug'] === 'finance') {
                $role->permissions()->sync(
                    Permission::whereIn('slug', [
                        'view_fuel_orders',
                        'review_fuel_orders',
                        'approve_fuel_orders',
                        'view_debts',
                        'create_debts',
                        'edit_debts',
                        'view_vendor_payments',
                        'create_vendor_payments',
                        'edit_vendor_payments',
                    ])->pluck('id')->toArray()
                );
            }
        }
    }
}