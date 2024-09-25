<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $name = env("name", "Admin");
        $email = env("email", "admin@adnan-tech.com");
        $password = env("password", "admin");

        if (!empty($name) && !empty($email) && !empty($password))
        {
            $super_admin = DB::table("users")
                ->where("type", "=", "super_admin")
                ->first();

            if ($super_admin == null)
            {
                DB::table("users")
                    ->insertGetId([
                        "name" => $name,
                        "email" => $email,
                        "password" => password_hash($password, PASSWORD_DEFAULT),
                        "email_verified_at" => now(),
                        "type" => "super_admin",
                        "created_at" => now(),
                        "updated_at" => now()
                    ]);
            }
        }
    }
}
