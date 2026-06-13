<?php

namespace App\Services;

class FuzzyTsukamotoService
{
    private const RULES = [
        ['AMAN', 'URGENT', 'AMAN', 'SANGAT_LAYAK'],
        ['AMAN', 'URGENT', 'HATI_HATI', 'SANGAT_LAYAK'],
        ['AMAN', 'URGENT', 'BAHAYA', 'LAYAK'],
        ['AMAN', 'NEED', 'AMAN', 'SANGAT_LAYAK'],
        ['AMAN', 'NEED', 'HATI_HATI', 'LAYAK'],
        ['AMAN', 'NEED', 'BAHAYA', 'LAYAK'],
        ['AMAN', 'WANT', 'AMAN', 'LAYAK'],
        ['AMAN', 'WANT', 'HATI_HATI', 'KURANG_LAYAK'],
        ['AMAN', 'WANT', 'BAHAYA', 'KURANG_LAYAK'],
        ['CUKUP', 'URGENT', 'AMAN', 'SANGAT_LAYAK'],
        ['CUKUP', 'URGENT', 'HATI_HATI', 'LAYAK'],
        ['CUKUP', 'URGENT', 'BAHAYA', 'LAYAK'],
        ['CUKUP', 'NEED', 'AMAN', 'LAYAK'],
        ['CUKUP', 'NEED', 'HATI_HATI', 'LAYAK'],
        ['CUKUP', 'NEED', 'BAHAYA', 'KURANG_LAYAK'],
        ['CUKUP', 'WANT', 'AMAN', 'KURANG_LAYAK'],
        ['CUKUP', 'WANT', 'HATI_HATI', 'KURANG_LAYAK'],
        ['CUKUP', 'WANT', 'BAHAYA', 'TIDAK_LAYAK'],
        ['TIPIS', 'URGENT', 'AMAN', 'LAYAK'],
        ['TIPIS', 'URGENT', 'HATI_HATI', 'LAYAK'],
        ['TIPIS', 'URGENT', 'BAHAYA', 'KURANG_LAYAK'],
        ['TIPIS', 'NEED', 'AMAN', 'KURANG_LAYAK'],
        ['TIPIS', 'NEED', 'HATI_HATI', 'KURANG_LAYAK'],
        ['TIPIS', 'NEED', 'BAHAYA', 'TIDAK_LAYAK'],
        ['TIPIS', 'WANT', 'AMAN', 'TIDAK_LAYAK'],
        ['TIPIS', 'WANT', 'HATI_HATI', 'TIDAK_LAYAK'],
        ['TIPIS', 'WANT', 'BAHAYA', 'TIDAK_LAYAK'],
    ];

    public function analyze(
        float $remainingPercentage,
        int $needLevel,
        int $daysUntilAllowance,
        float $currentMoney = 0,
        float $itemPrice = 0
    ): array {
        $memberships = [
            'sisa_uang' => $this->remainingMoney($remainingPercentage),
            'kebutuhan' => $this->need($needLevel),
            'waktu' => $this->time($daysUntilAllowance),
        ];
        $activeRules = $this->activeRules($memberships);
        $score = $this->finalScore($activeRules);
        [$category, $decision] = $this->classification($score);
        $moneyAfterPurchase = $currentMoney - $itemPrice;
        $dailyBudget = $daysUntilAllowance > 0 ? $moneyAfterPurchase / $daysUntilAllowance : $moneyAfterPurchase;

        return [
            'inputs' => [
                'remaining_percentage' => round($remainingPercentage, 2),
                'need_level' => $needLevel,
                'days_until_allowance' => $daysUntilAllowance,
            ],
            'memberships' => $memberships,
            'active_rules' => $activeRules,
            'score' => $score,
            'category' => $category,
            'decision' => $decision,
            'money_after_purchase' => round($moneyAfterPurchase, 2),
            'daily_budget_after_purchase' => round($dailyBudget, 2),
            'recommendation' => $this->recommendation($category, $moneyAfterPurchase, $dailyBudget, $daysUntilAllowance),
        ];
    }

    public function ruleResource(): array
    {
        return [
            'method' => 'Fuzzy Tsukamoto',
            'variables' => [
                'remaining_percentage' => ['TIPIS', 'CUKUP', 'AMAN'],
                'need_level' => ['WANT', 'NEED', 'URGENT'],
                'days_until_allowance' => ['BAHAYA', 'HATI_HATI', 'AMAN'],
            ],
            'outputs' => ['TIDAK_LAYAK', 'KURANG_LAYAK', 'LAYAK', 'SANGAT_LAYAK'],
            'rules_count' => count(self::RULES),
            'rules' => self::RULES,
        ];
    }

    private function remainingMoney(float $value): array
    {
        $tipis = $value <= 20 ? 1 : ($value < 40 ? (40 - $value) / 20 : 0);
        $cukup = ($value <= 30 || $value >= 70) ? 0 : ($value < 50 ? ($value - 30) / 20 : ($value == 50 ? 1 : (70 - $value) / 20));
        $aman = $value <= 60 ? 0 : ($value < 80 ? ($value - 60) / 20 : 1);

        return $this->roundMemberships(['TIPIS' => $tipis, 'CUKUP' => $cukup, 'AMAN' => $aman]);
    }

    private function need(float $value): array
    {
        $want = $value <= 2 ? 1 : ($value < 4 ? (4 - $value) / 2 : 0);
        $need = ($value <= 3 || $value >= 7) ? 0 : ($value < 5 ? ($value - 3) / 2 : ($value == 5 ? 1 : (7 - $value) / 2));
        $urgent = $value <= 6 ? 0 : ($value < 8 ? ($value - 6) / 2 : 1);

        return $this->roundMemberships(['WANT' => $want, 'NEED' => $need, 'URGENT' => $urgent]);
    }

    private function time(float $value): array
    {
        $bahaya = $value <= 5 ? 1 : ($value < 10 ? (10 - $value) / 5 : 0);
        $hati = ($value <= 8 || $value >= 20) ? 0 : ($value < 14 ? ($value - 8) / 6 : ($value == 14 ? 1 : (20 - $value) / 6));
        $aman = $value <= 18 ? 0 : ($value < 24 ? ($value - 18) / 6 : 1);

        return $this->roundMemberships(['BAHAYA' => $bahaya, 'HATI_HATI' => $hati, 'AMAN' => $aman]);
    }

    private function activeRules(array $memberships): array
    {
        $active = [];

        foreach (self::RULES as $index => $rule) {
            [$money, $need, $time, $output] = $rule;
            $alpha = min(
                $memberships['sisa_uang'][$money],
                $memberships['kebutuhan'][$need],
                $memberships['waktu'][$time]
            );

            if ($alpha > 0) {
                $active[] = [
                    'number' => $index + 1,
                    'condition' => "{$money} + {$need} + {$time}",
                    'alpha' => $alpha,
                    'output' => $output,
                    'z' => $this->zValue($alpha, $output),
                ];
            }
        }

        return $active;
    }

    private function zValue(float $alpha, string $category): float
    {
        return match ($category) {
            'TIDAK_LAYAK' => 30 - ($alpha * 30),
            'KURANG_LAYAK' => 20 + ($alpha * 30),
            'LAYAK' => 40 + ($alpha * 30),
            default => 60 + ($alpha * 40),
        };
    }

    private function finalScore(array $rules): float
    {
        if ($rules === []) {
            return 0;
        }

        $totalAlpha = array_sum(array_column($rules, 'alpha'));
        $totalWeighted = array_reduce($rules, fn (float $carry, array $rule) => $carry + ($rule['alpha'] * $rule['z']), 0.0);

        return round($totalWeighted / $totalAlpha, 2);
    }

    private function classification(float $score): array
    {
        return match (true) {
            $score > 75 => ['SANGAT LAYAK', 'Beli sekarang jika tidak ada kewajiban mendesak lain.'],
            $score > 50 => ['LAYAK', 'Boleh membeli, dengan tetap menjaga pengeluaran harian.'],
            $score > 25 => ['KURANG LAYAK', 'Pertimbangkan menunda pembelian atau mencari alternatif lebih murah.'],
            default => ['TIDAK LAYAK', 'Jangan membeli saat ini; prioritaskan kebutuhan utama.'],
        };
    }

    private function recommendation(string $category, float $remaining, float $daily, int $days): string
    {
        $balance = number_format($remaining, 0, ',', '.');
        $perDay = number_format($daily, 0, ',', '.');

        return match ($category) {
            'SANGAT LAYAK' => "Kondisi cukup mendukung pembelian. Setelah transaksi, sisa uang sekitar Rp {$balance}, atau Rp {$perDay} per hari untuk {$days} hari berikutnya.",
            'LAYAK' => "Pembelian masih masuk akal, tetapi batasi belanja tambahan. Estimasi dana harian setelah membeli adalah Rp {$perDay}.",
            'KURANG LAYAK' => "Pembelian berisiko mengurangi ruang finansial. Sisa dana diperkirakan Rp {$balance}; sebaiknya tunda atau cari harga lebih rendah.",
            default => "Dana dan waktu menuju kiriman berikutnya belum mendukung. Pembelian ini menyisakan sekitar Rp {$balance}; utamakan kebutuhan rutin dahulu.",
        };
    }

    private function roundMemberships(array $memberships): array
    {
        return array_map(fn (float $value) => round($value, 4), $memberships);
    }
}
