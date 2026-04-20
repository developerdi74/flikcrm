<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создаем администратора по умолчанию
        User::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Администратор Системы',
            'position' => 'IT Администратор',
            'description' => 'Главный администратор корпоративного чата',
            'status' => 'active',
            'work_schedule' => '9:00-18:00',
            'is_admin' => true,
        ]);

        // Создаем несколько тестовых сотрудников
        User::create([
            'email' => 'ivanov@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Иванов Иван Иванович',
            'position' => 'Менеджер проектов',
            'description' => 'Руководит проектами разработки',
            'status' => 'active',
            'work_schedule' => '9:00-18:00',
            'is_admin' => false,
        ]);

        User::create([
            'email' => 'petrov@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Петров Петр Петрович',
            'position' => 'Разработчик',
            'description' => 'Backend разработчик',
            'status' => 'active',
            'work_schedule' => '10:00-19:00',
            'is_admin' => false,
        ]);

        User::create([
            'email' => 'sidorova@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Сидорова Анна Сергеевна',
            'position' => 'Дизайнер',
            'description' => 'UI/UX дизайнер',
            'status' => 'vacation',
            'work_schedule' => '9:00-18:00',
            'is_admin' => false,
        ]);
    }
}
