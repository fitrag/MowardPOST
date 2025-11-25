<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $branch = \App\Models\Branch::create([
            'name' => 'Main Store',
            'address' => '123 Main St',
            'phone' => '123-456-7890',
        ]);

        // Create users
        $owner = User::create([
            'name' => 'Owner User',
            'email' => 'owner@pos.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'branch_id' => $branch->id,
        ]);

        \App\Models\User::create([
            'name' => 'Cashier User',
            'email' => 'cashier@example.com',
            'password' => bcrypt('password'),
            'role' => 'cashier',
            'branch_id' => $branch->id,
        ]);
    }
}
