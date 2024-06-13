<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@akil.co.id',
            'password' => bcrypt('Ve5JbvSCBXBkdni'),
        ]);

        User::factory(1000)->create();
        // Artisan::call('passport:client --personal');
    }
}
