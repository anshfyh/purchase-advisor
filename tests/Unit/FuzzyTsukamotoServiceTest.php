<?php

namespace Tests\Unit;

use App\Services\FuzzyTsukamotoService;
use PHPUnit\Framework\TestCase;

class FuzzyTsukamotoServiceTest extends TestCase
{
    public function test_urgent_purchase_with_safe_money_is_very_feasible(): void
    {
        $result = (new FuzzyTsukamotoService())->analyze(80, 9, 23, 1200000, 250000);

        $this->assertSame(93.33, $result['score']);
        $this->assertSame('SANGAT LAYAK', $result['category']);
        $this->assertNotEmpty($result['active_rules']);
    }

    public function test_wanted_purchase_with_low_money_is_not_feasible(): void
    {
        $result = (new FuzzyTsukamotoService())->analyze(18, 3, 5, 270000, 550000);

        $this->assertSame('TIDAK LAYAK', $result['category']);
        $this->assertLessThanOrEqual(25, $result['score']);
    }
}
