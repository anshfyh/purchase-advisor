<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\FuzzyTsukamotoService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(FuzzyTsukamotoService $fuzzy): void
    {
        User::updateOrCreate(['email' => 'admin@advisor.test'], [
            'name' => 'Administrator',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $user = User::updateOrCreate(['email' => 'student@advisor.test'], [
            'name' => 'Mahasiswa Demo',
            'password' => 'password',
            'role' => 'user',
        ]);

        if ($user->analyses()->doesntExist()) {
            $result = $fuzzy->analyze(80, 9, 23, 1200000, 250000);
            $user->analyses()->create([
                'item_name' => 'Charger Laptop Original',
                'monthly_allowance' => 1500000,
                'current_money' => 1200000,
                'item_price' => 250000,
                'need_level' => 9,
                'days_until_allowance' => 23,
                'remaining_percentage' => 80,
                'score' => $result['score'],
                'category' => $result['category'],
                'decision' => $result['decision'],
                'recommendation' => $result['recommendation'],
                'result_payload' => $result,
            ]);
        }
    }
}
