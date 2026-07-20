<?php
namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Administrator',
            'email' => 'admin@sman1turen.sch.id',
            'password' => 'password123', // otomatis di-hash via cast 'hashed'
        ]);
    }
}