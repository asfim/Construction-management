<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Project;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Demo Company
        $company = Company::firstOrCreate(
            ['code' => 'HTP-CON'],
            [
                'name' => 'H Tech Provision Construction Ltd.',
                'legal_name' => 'H Tech Provision Construction Ltd.',
                'trade_license' => 'TRAD/DSCC/012984/2024',
                'tin' => '482910482910',
                'bin' => '002819482-0101',
                'email' => 'info@htech.com.bd',
                'phone' => '+8801711000000',
                'address' => 'House 42, Road 11, Block D, Banani',
                'city' => 'Dhaka',
                'division' => 'Dhaka',
                'country' => 'Bangladesh',
                'currency' => 'BDT',
                'currency_symbol' => '৳',
                'fiscal_year_start' => '07-01',
                'is_active' => true,
            ]
        );

        // 2. Create Branches
        $dhakaBranch = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'BR-DHK'],
            [
                'name' => 'Dhaka Head Office',
                'phone' => '+88029876543',
                'email' => 'dhaka@htech.com.bd',
                'address' => 'House 42, Road 11, Block D, Banani, Dhaka-1213',
                'city' => 'Dhaka',
                'division' => 'Dhaka',
                'is_head_office' => true,
                'is_active' => true,
            ]
        );

        $ctgBranch = Branch::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'BR-CTG'],
            [
                'name' => 'Chattogram Regional Branch',
                'phone' => '+88031765432',
                'email' => 'ctg@htech.com.bd',
                'address' => 'World Trade Center, 5th Floor, Agrabad C/A, Chattogram',
                'city' => 'Chattogram',
                'division' => 'Chattogram',
                'is_head_office' => false,
                'is_active' => true,
            ]
        );

        // 3. Create Demo Users across Key Roles
        $usersData = [
            [
                'name' => 'System Super Admin',
                'email' => 'superadmin@htech.com.bd',
                'phone' => '+8801700000001',
                'designation' => 'Chief Technology Officer',
                'role' => 'Super Admin',
            ],
            [
                'name' => 'Engr. Hasan Mahmud (Managing Director)',
                'email' => 'owner@htech.com.bd',
                'phone' => '+8801700000002',
                'designation' => 'Managing Director & Owner',
                'role' => 'Company Owner',
            ],
            [
                'name' => 'Engr. Tariqul Islam (Project Manager)',
                'email' => 'pm@htech.com.bd',
                'phone' => '+8801700000003',
                'designation' => 'Senior Project Manager',
                'role' => 'Project Manager',
            ],
            [
                'name' => 'Md. Rafiqul Karim (Site Engineer)',
                'email' => 'site.engineer@htech.com.bd',
                'phone' => '+8801700000004',
                'designation' => 'Resident Site Engineer',
                'role' => 'Site Engineer',
            ],
            [
                'name' => 'Nasir Uddin (Quantity Surveyor - QS)',
                'email' => 'qs@htech.com.bd',
                'phone' => '+8801700000005',
                'designation' => 'Chief Quantity Surveyor',
                'role' => 'Quantity Surveyor (QS)',
            ],
            [
                'name' => 'Sabbir Ahmed (Procurement Manager)',
                'email' => 'procurement@htech.com.bd',
                'phone' => '+8801700000006',
                'designation' => 'Head of Procurement',
                'role' => 'Procurement Manager',
            ],
            [
                'name' => 'Kamal Hossain (Storekeeper)',
                'email' => 'storekeeper@htech.com.bd',
                'phone' => '+8801700000007',
                'designation' => 'Central Storekeeper',
                'role' => 'Storekeeper',
            ],
            [
                'name' => 'Farhana Akter ACA (Accounts Manager)',
                'email' => 'accounts@htech.com.bd',
                'phone' => '+8801700000008',
                'designation' => 'Chief Financial Officer',
                'role' => 'Accounts Manager',
            ],
            [
                'name' => 'Zillur Rahman (Internal Auditor)',
                'email' => 'auditor@htech.com.bd',
                'phone' => '+8801700000009',
                'designation' => 'Internal Audit Lead',
                'role' => 'Auditor',
            ],
        ];

        $createdUsers = [];
        foreach ($usersData as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'phone' => $userData['phone'],
                    'designation' => $userData['designation'],
                    'current_company_id' => $company->id,
                    'preferred_locale' => 'en',
                    'is_active' => true,
                ]
            );

            // Assign Spatie Role
            $user->syncRoles([$userData['role']]);

            // Attach to company pivot
            $company->users()->syncWithoutDetaching([
                $user->id => [
                    'branch_id' => $dhakaBranch->id,
                    'designation' => $userData['designation'],
                    'is_default' => true,
                ],
            ]);

            $createdUsers[$userData['role']] = $user;
        }

        // 4. Create Realistic Bangladesh Projects
        $pmUser = $createdUsers['Project Manager'] ?? null;
        $siteEngineer = $createdUsers['Site Engineer'] ?? null;
        $qsUser = $createdUsers['Quantity Surveyor (QS)'] ?? null;

        $project1 = Project::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'PRJ-GLS-001'],
            [
                'branch_id' => $dhakaBranch->id,
                'name' => 'Gulshan Commercial Tower 14-Story',
                'description' => 'Construction of 14-storied commercial tower with 3 basements including civil, MEP, and finishing work.',
                'client_name' => 'Apex Horizon Properties Ltd.',
                'client_contact_person' => 'Mr. Enamul Haque, Chief Engineer',
                'client_phone' => '+8801819234567',
                'client_email' => 'projects@apexhorizon.com',
                'site_address' => 'Plot 88, Road 17, Gulshan-2, Dhaka',
                'city' => 'Dhaka',
                'division' => 'Dhaka',
                'contract_value' => 125000000.00,
                'budget_amount' => 105000000.00,
                'retention_percentage' => 5.00,
                'advance_recovery_percentage' => 10.00,
                'start_date' => '2025-01-15',
                'end_date' => '2027-06-30',
                'status' => 'active',
                'progress_percent' => 35.50,
                'project_manager_id' => $pmUser?->id,
                'created_by' => $pmUser?->id,
            ]
        );

        $project2 = Project::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'PRJ-PAD-002'],
            [
                'branch_id' => $dhakaBranch->id,
                'name' => 'Padma Bridge Connecting Highway & Culverts Package-3',
                'description' => '12.5 km 4-lane rigid pavement highway construction, 6 box culverts, and slope protection.',
                'client_name' => 'Roads and Highways Department (RHD), Bangladesh',
                'client_contact_person' => 'Executive Engineer, Munshiganj Division',
                'client_phone' => '+8801712345678',
                'client_email' => 'ee_munshiganj@rhd.gov.bd',
                'site_address' => 'Mawa to Shariatpur Link Highway, Munshiganj',
                'city' => 'Munshiganj',
                'division' => 'Dhaka',
                'contract_value' => 280000000.00,
                'budget_amount' => 240000000.00,
                'retention_percentage' => 10.00,
                'advance_recovery_percentage' => 15.00,
                'start_date' => '2024-07-01',
                'end_date' => '2026-12-31',
                'status' => 'active',
                'progress_percent' => 62.00,
                'project_manager_id' => $pmUser?->id,
                'created_by' => $pmUser?->id,
            ]
        );

        $project3 = Project::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'PRJ-UTR-003'],
            [
                'branch_id' => $dhakaBranch->id,
                'name' => 'Uttara Sector 18 Residential Complex',
                'description' => 'Construction of 10-storied residential building with modern foundation piles and shear walls.',
                'client_name' => 'Green City Developers Ltd.',
                'client_contact_person' => 'Engr. Shahinur Rahman',
                'client_phone' => '+8801912345678',
                'client_email' => 'shahin@greencitybd.com',
                'site_address' => 'Plot 12, Road 4, Sector 18, Uttara, Dhaka',
                'city' => 'Dhaka',
                'division' => 'Dhaka',
                'contract_value' => 85000000.00,
                'budget_amount' => 72000000.00,
                'retention_percentage' => 5.00,
                'advance_recovery_percentage' => 10.00,
                'start_date' => '2026-11-01',
                'end_date' => '2028-04-30',
                'status' => 'planning',
                'progress_percent' => 0.00,
                'project_manager_id' => $pmUser?->id,
                'created_by' => $pmUser?->id,
            ]
        );

        // Assign Project Members
        if ($pmUser && $siteEngineer && $qsUser) {
            $project1->members()->syncWithoutDetaching([
                $pmUser->id => ['project_role' => 'project_manager', 'assigned_date' => '2025-01-15', 'is_active' => true],
                $siteEngineer->id => ['project_role' => 'site_engineer', 'assigned_date' => '2025-01-15', 'is_active' => true],
                $qsUser->id => ['project_role' => 'qs', 'assigned_date' => '2025-01-15', 'is_active' => true],
            ]);

            $project2->members()->syncWithoutDetaching([
                $pmUser->id => ['project_role' => 'project_manager', 'assigned_date' => '2024-07-01', 'is_active' => true],
                $siteEngineer->id => ['project_role' => 'site_engineer', 'assigned_date' => '2024-07-01', 'is_active' => true],
            ]);
        }

        // Set Default Active Project for PM and Site Engineer
        $pmUser?->updateQuietly(['current_project_id' => $project1->id]);
        $siteEngineer?->updateQuietly(['current_project_id' => $project1->id]);

        // 5. System Settings presets
        SystemSetting::setSetting('default_currency', 'BDT', 'general', $company->id, 'string');
        SystemSetting::setSetting('currency_symbol', '৳', 'general', $company->id, 'string');
        SystemSetting::setSetting('date_format', 'd-m-Y', 'localization', $company->id, 'string');
        SystemSetting::setSetting('timezone', 'Asia/Dhaka', 'localization', $company->id, 'string');
        SystemSetting::setSetting('default_vat_rate', '7.5', 'tax', $company->id, 'decimal');
        SystemSetting::setSetting('default_tds_rate', '5.0', 'tax', $company->id, 'decimal');
        SystemSetting::setSetting('default_retention_rate', '5.0', 'billing', $company->id, 'decimal');
    }
}
