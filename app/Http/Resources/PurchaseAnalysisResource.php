<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseAnalysisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_name' => $this->item_name,
            'monthly_allowance' => $this->monthly_allowance,
            'current_money' => $this->current_money,
            'item_price' => $this->item_price,
            'need_level' => $this->need_level,
            'days_until_allowance' => $this->days_until_allowance,
            'remaining_percentage' => $this->remaining_percentage,
            'score' => $this->score,
            'category' => $this->category,
            'decision' => $this->decision,
            'recommendation' => $this->recommendation,
            'ai_recommendation' => $this->ai_recommendation,
            'created_at' => $this->created_at,
        ];
    }
}