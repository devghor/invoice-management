<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

final class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        if (User::query()->where('email', 'sa@app.com')->doesntExist()) {
            UserFactory::new()->createOne([
                'email' => 'sa@app.com',
            ]);
        }
    }
}
