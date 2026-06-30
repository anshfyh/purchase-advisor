<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\Controller;
use App\Services\FuzzyTsukamotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FuzzyResourceController extends Controller
{
public function index(FuzzyTsukamotoService $fuzzy): JsonResponse
{
    return response()->json([
        'status' => 'success',
        'message' => 'Purchase Advisor REST API',
        'version' => '1.0',
        'developer' => 'Ani Shofiyyah Zazqia',

        'resource' => $fuzzy->ruleResource(),

        'endpoints' => [
            [
                'method' => 'GET',
                'url' => url('/api/fuzzy'),
                'description' => 'Menampilkan informasi metode Fuzzy Tsukamoto beserta rule yang digunakan.',
            ],
            [
                'method' => 'POST',
                'url' => url('/api/fuzzy/calculate'),
                'description' => 'Menghitung rekomendasi pembelian berdasarkan data yang dikirim.',
            ],
        ],
    ]);
}

    public function calculate(Request $request, FuzzyTsukamotoService $fuzzy): JsonResponse
    {
        $data = $request->validate(AnalysisController::rules());
        $percentage = ($data['current_money'] / $data['monthly_allowance']) * 100;

        return response()->json([
            'data' => [
                'item_name' => $data['item_name'],
                'result' => $fuzzy->analyze(
                    $percentage,
                    $data['need_level'],
                    $data['days_until_allowance'],
                    $data['current_money'],
                    $data['item_price']
                ),
            ],
        ]);
    }
}
