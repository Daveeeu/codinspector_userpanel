<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Spatie\Permission\Models\Role;
    use Spatie\Permission\Models\Permission;
    use App\Models\Package;

    class PackageSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            // Jogosultságok létrehozása
            $permissions = [
                'basic' => 'Access basic features',
                'limited access' => 'Access limited functionalities',
                'pro' => 'Access pro features',
                'full access' => 'Access all pro functionalities',
                'enterprise' => 'Access enterprise features',
                'premium support' => 'Access premium support',
                'feedback-list' => 'Access feedback list',
                'query-list' => 'Access query list',
            ];

            foreach ($permissions as $key => $description) {
                Permission::firstOrCreate(['name' => $key, 'guard_name' => 'web']);
            }

            // Szerepkörök létrehozása és jogosultságok hozzárendelése
            $roles = [
                'Basic Package' => ['basic', 'limited access', 'feedback-list', 'query-list', 'add-exception', 'manual-query'],
                'Pro Package' => ['pro', 'full access'],
                'Enterprise Package' => ['enterprise', 'premium support']
            ];

            foreach ($roles as $roleName => $rolePermissions) {
                $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
                $role->syncPermissions($rolePermissions);
            }

            // Csomagok létrehozása és szerepkör hozzárendelése
            $packages = [
                [
                    'name' => 'Basic Package',
                    'description' => 'This is the basic subscription package with limited queries.',
                    'query_limit' => 100,
                    'cost_per_query' => 0.10,
                    'cost' => null,
                    'permissions' => json_encode(['basic', 'limited access']),
                    'role' => 'Basic Package'
                ],
                [
                    'name' => 'Pro Package',
                    'description' => 'A professional package with more queries and advanced features.',
                    'query_limit' => 500,
                    'cost_per_query' => null,
                    'cost' => 40.00,
                    'permissions' => json_encode(['pro', 'full access']),
                    'role' => 'Pro Package'
                ],
                [
                    'name' => 'Enterprise Package',
                    'description' => 'Enterprise-level subscription with unlimited queries and premium support.',
                    'query_limit' => 0,
                    'cost_per_query' => null,
                    'cost' => 150.00,
                    'permissions' => json_encode(['enterprise', 'premium support']),
                    'role' => 'Enterprise Package'
                ]
            ];

            foreach ($packages as $packageData) {
                $package = Package::create([
                    'name' => $packageData['name'],
                    'description' => $packageData['description'],
                    'query_limit' => $packageData['query_limit'],
                    'cost_per_query' => $packageData['cost_per_query'],
                    'cost' => $packageData['cost'],
                    'permissions' => $packageData['permissions'],
                ]);

                // Szerepkör hozzárendelése a csomaghoz
                $package->assignRole($packageData['role']);
            }
        }
    }
