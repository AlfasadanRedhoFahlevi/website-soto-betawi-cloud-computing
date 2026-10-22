<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'string', 'max:255'],
        ]);

        $categories = DB::table('menus')
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $query = DB::table('menus');
        $search = trim($filters['search'] ?? '');
        $selectedCategory = $filters['kategori'] ?? '';

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('nama_menu', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($selectedCategory !== '') {
            $query->where('kategori', $selectedCategory);
        }

        $menus = $query
            ->orderBy('kategori')
            ->orderBy('nama_menu')
            ->get();

        return view('welcome', [
            'menus' => $menus,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $selectedCategory,
        ]);
    }
}
