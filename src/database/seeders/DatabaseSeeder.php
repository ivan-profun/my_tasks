<?php

namespace Database\Seeders;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@test.test'],
            [
                'name' => 'test',
                'password' => bcrypt('12345678'),
            ]
        );

        $user->tasks()->createMany([
            [
                'title' => 'Съездить на море',
                'description' => 'поехать в сочи и пройтись по известному маршруту',
                'status' => TaskStatus::Pending,
                'reminder_at' => '2027-08-02 00:00:00',
            ],
            [
                'title' => 'Поехать за границу',
                'description' => 'рассмотреть актуальные направления и посетить одну из стран доступных для туристического посещения',
                'status' => TaskStatus::Pending,
                'reminder_at' => '2028-06-10 00:00:00',
            ],
        ]);
    }
}