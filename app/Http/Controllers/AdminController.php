<?php

namespace App\Http\Controllers;

use App\Models\PurchaseAnalysis;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
{
    return view('admin.dashboard', [
        'usersCount'    => User::where('role', 'user')->count(),
        'analysesCount' => PurchaseAnalysis::count(),
        'categoryStats' => PurchaseAnalysis::selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category'),
        'analyses' => PurchaseAnalysis::with('user')->latest()->limit(5)->get(),
    ]);
}

    public function users(): View
    {
        return view('admin.users', [
            'users' => User::withCount('analyses')->latest()->paginate(12),
        ]);
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['user', 'admin'])],
        ]);

        if ($user->is($request->user()) && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Role akun sendiri tidak dapat diturunkan.']);
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('status', 'Role pengguna berhasil diperbarui.');
    }

    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Akun yang sedang dipakai tidak dapat dihapus.']);
        }

        $user->delete();

        return back()->with('status', 'Pengguna berhasil dihapus.');
    }

    public function analyses(Request $request): View
    {
        $analyses = PurchaseAnalysis::with('user')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($query) use ($search) {
                    $query->where('item_name', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($query) => $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.analyses', [
            'analyses' => $analyses,
            'categories' => PurchaseAnalysis::query()
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
        ]);
    }

    public function showAnalysis(PurchaseAnalysis $analysis): View
    {
        return view('admin.analysis-detail', [
            'analysis' => $analysis->load('user'),
        ]);
    }

    public function statistics(): View
    {
        return view('admin.statistics', [
            'topItems' => PurchaseAnalysis::selectRaw('item_name, COUNT(*) as total')
                ->groupBy('item_name')
                ->orderByDesc('total')
                ->limit(8)
                ->get(),
            'categoryStats' => PurchaseAnalysis::selectRaw('category, COUNT(*) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->get(),
            'averageScore' => round((float) PurchaseAnalysis::avg('score'), 2),
            'totalAnalyses' => PurchaseAnalysis::count(),
        ]);
    }
}
