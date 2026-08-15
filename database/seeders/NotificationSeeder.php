<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::limit(9)->get();

        foreach ($users as $user) {
            Notification::factory(10)->create([
                'recipient_id' => $user->id,
            ]);
        }
    }
}
