<?php

namespace App\Http\Controllers;

use App\Models\PurchaseAnalysis;
use App\Services\FuzzyTsukamotoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnalysisController extends Controller
{
    public function store(Request $request, FuzzyTsukamotoService $fuzzy): JsonResponse
    {
        $data = $request->validate($this->rules());

        $percentage = ($data['current_money'] / $data['monthly_allowance']) * 100;

        $result = $fuzzy->analyze(
            $percentage,
            $data['need_level'],
            $data['days_until_allowance'],
            $data['current_money'],
            $data['item_price']
        );

        $analysis = $request->user()->analyses()->create([
            ...$data,
            'remaining_percentage' => round($percentage, 2),
            'score'                => $result['score'],
            'category'             => $result['category'],
            'decision'             => $result['decision'],
            'recommendation'       => $result['recommendation'],
            'result_payload'       => $result,
        ]);

        return response()->json([
            'message'     => 'Pertimbanganmu berhasil disimpan.',
            'analysis_id' => $analysis->id,
            'item_name'   => $analysis->item_name,
            'result'      => $result,
        ], 201);
    }
    public function create()
{
    return view('analysis.create');
}
    public function show(PurchaseAnalysis $analysis)
    {
        abort_if($analysis->user_id !== auth()->id(), 403);

        return view('analysis.show', [
            'analysis' => $analysis,
        ]);
    }

    public function aiRecommendation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'analysis_id'          => 'required|exists:purchase_analyses,id',
            'item_name'            => 'required|string',
            'item_price'           => 'required|numeric',
            'score'                => 'required|numeric',
            'category'             => 'required|string',
            'need_level'           => 'required|numeric',
            'days_until_allowance' => 'required|numeric',
            'remaining_percentage' => 'required|numeric',
        ]);

        $apiKey = config('services.gemini.key');

        if (!$apiKey || str_starts_with($apiKey, 'AIzaSyxxxxx')) {
            return response()->json([
                'recommendation' => 'API key Gemini belum dikonfigurasi di file .env.',
                'source'         => 'fallback',
            ]);
        }

        $prompt = "Berikan rekomendasi keuangan untuk mahasiswa dalam 2-3 kalimat. "
            . "Langsung ke poin, tanpa kalimat pembuka seperti 'oke', 'tentu', 'baik', atau 'mari kita lihat'. "
            . "Gunakan bahasa Indonesia santai.\n\n"
            . "Barang  : {$data['item_name']}\n"
            . "Harga   : Rp " . number_format($data['item_price'], 0, ',', '.') . "\n"
            . "Skor    : {$data['score']}/100 (" . str_replace('_', ' ', $data['category']) . ")\n"
            . "Butuh   : {$data['need_level']}/10\n"
            . "Sisa    : {$data['remaining_percentage']}% uang saku, {$data['days_until_allowance']} hari lagi kiriman\n";

        try {
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])
    ->timeout(30)
    ->retry(2, 1000, function ($exception, $request) {
        // Retry hanya jika error 503 (overload)
        return $exception instanceof \Illuminate\Http\Client\RequestException
            && $exception->response?->status() === 503;
    })
    ->post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey,
        [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'maxOutputTokens' => 1024,
                'temperature'     => 0.5,
            ],
        ]
    );

    if ($response->failed()) {
        throw new \Exception('Status: ' . $response->status() . ' - ' . $response->body());
    }

            $body = $response->json();

            $text = $body['candidates'][0]['content']['parts'][0]['text']
                ?? 'Rekomendasi tidak tersedia.';

            $text = preg_replace('/\*{1,2}([^*]+)\*{1,2}/', '$1', $text);
            $text = preg_replace('/#{1,3}\s*/', '', $text);
            $text = trim($text);

            PurchaseAnalysis::find($data['analysis_id'])
                ?->update([
                    'ai_recommendation' => $text,
                ]);

            return response()->json([
                'recommendation' => $text,
                'source'         => 'ai',
            ]);
} catch (\Exception $e) {
    $message = $e->getMessage();

    // Pesan ramah untuk user, sembunyikan detail teknis
    if (str_contains($message, '503') || str_contains($message, 'UNAVAILABLE') || str_contains($message, 'high demand')) {
        $friendlyMessage = 'AI sedang sibuk melayani banyak permintaan. Coba klik beberapa saat lagi ya.';
    } elseif (str_contains($message, '429')) {
        $friendlyMessage = 'Batas penggunaan AI tercapai. Coba lagi nanti.';
    } else {
        $friendlyMessage = 'Rekomendasi AI sedang tidak dapat diakses. Silakan coba lagi.';
    }

    return response()->json([
        'recommendation' => $friendlyMessage,
        'source'         => 'error',
    ]);
}
    }

    public static function rules(): array
    {
        return [
            'item_name'            => ['required', 'string', 'max:120'],
            'monthly_allowance'    => ['required', 'numeric', 'min:1'],
            'current_money'        => ['required', 'numeric', 'min:0'],
            'item_price'           => ['required', 'numeric', 'min:0'],
            'need_level'           => ['required', 'integer', 'between:1,10'],
            'days_until_allowance' => ['required', 'integer', 'between:0,30'],
        ];
    }

public function update(
    Request $request,
    PurchaseAnalysis $analysis,
    FuzzyTsukamotoService $fuzzy
): JsonResponse
{
    abort_if($analysis->user_id !== auth()->id(), 403);

    $data = $request->validate([
        'item_name'            => ['required', 'string', 'max:120'],
        'monthly_allowance'    => ['required', 'numeric', 'min:1'],
        'current_money'        => ['required', 'numeric', 'min:0'],
        'item_price'           => ['required', 'numeric', 'min:0'],
        'need_level'           => ['required', 'integer', 'between:1,10'],
        'days_until_allowance' => ['required', 'integer', 'between:0,30'],
    ]);

    $percentage = ($data['current_money'] / $data['monthly_allowance']) * 100;

    $result = $fuzzy->analyze(
        $percentage,
        $data['need_level'],
        $data['days_until_allowance'],
        $data['current_money'],
        $data['item_price']
    );

    $analysis->update([
        ...$data,
        'remaining_percentage' => round($percentage, 2),
        'score'                => $result['score'],
        'category'             => $result['category'],
        'decision'             => $result['decision'],
        'recommendation'       => $result['recommendation'],
        'result_payload'       => $result,
        'ai_recommendation'    => null,
    ]);

    return response()->json([
        'message' => 'Pertimbangan berhasil diperbarui.',
        'analysis' => $analysis->fresh(),
    ]);
}

public function generateAi(PurchaseAnalysis $analysis): JsonResponse
{
    abort_if($analysis->user_id !== auth()->id(), 403);

    $request = new Request([
        'analysis_id'          => $analysis->id,
        'item_name'            => $analysis->item_name,
        'item_price'           => $analysis->item_price,
        'score'                => $analysis->score,
        'category'             => $analysis->category,
        'need_level'           => $analysis->need_level,
        'days_until_allowance' => $analysis->days_until_allowance,
        'remaining_percentage' => $analysis->remaining_percentage,
    ]);

    return $this->aiRecommendation($request);
}



    public function destroy(PurchaseAnalysis $analysis)
{
    abort_if($analysis->user_id !== auth()->id(), 403);

    $analysis->delete();

    return redirect()
        ->route('history')
        ->with('status', 'Riwayat berhasil dihapus.');
}
}