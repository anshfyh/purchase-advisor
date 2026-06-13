<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'monthly_allowance',
        'current_money',
        'item_price',
        'need_level',
        'days_until_allowance',
        'remaining_percentage',
        'score',
        'category',
        'decision',
        'recommendation',
        'ai_recommendation',
        'result_payload',
    ];

    protected function casts(): array
    {
        return [
            'monthly_allowance' => 'decimal:2',
            'current_money' => 'decimal:2',
            'item_price' => 'decimal:2',
            'remaining_percentage' => 'decimal:2',
            'score' => 'decimal:2',
            'result_payload' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}