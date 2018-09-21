<?php

use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create(
            [
                'email' => "waselews1@gmail.com",
                'name' => "waselews"
            ]
        );
        factory(User::class)->create(
            [
                'email' => "maximus@gmail.com",
                'name' => "maximus"
            ]
        );
        factory(User::class)->create(
            [
                'email' => "underdog@gmail.com",
                'name' => "underdog"
            ]
        );
        // $this->call(UsersTableSeeder::class);
    }
}

