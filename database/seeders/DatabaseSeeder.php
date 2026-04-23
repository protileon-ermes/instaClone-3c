<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::factory()->create([
            'name' => 'Ermes Dev',
            'username' => 'ermes_dev',
            'email' => 'teste@exemplo.com',
            'password' => bcrypt('12345678'),
        ]);

        // Cria mais 10 usuários aleatórios para testarmos o Follow depois
        User::factory(10)->create();
    }
}
