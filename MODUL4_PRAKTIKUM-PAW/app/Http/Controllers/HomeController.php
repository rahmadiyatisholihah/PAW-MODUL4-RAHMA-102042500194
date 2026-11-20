<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman katalog buku dengan fitur pencarian, filter, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        // TODO 3: Lengkapi logika index():
        // 1. Buat query pencarian berdasarkan judul, penulis, atau penerbit jika ada input 'search'
        // 2. Tambahkan filter penerbit dan filter ketersediaan stok jika ada input 'penerbit' / 'status'
        // 3. Tambahkan pengurutan data berdasarkan input 'sort' (judul, tahun, stok, default: latest)
        // 4. Lakukan paginasi 8 data dengan withQueryString()
        // 5. Ambil list penerbit unik untuk opsi dropdown filter
        // 6. Return view 'home' dengan data buku dan list penerbit

        $buku = Buku::paginate(8);
        $listPenerbit = collect();
    $search = $request->input('search');

    $buku = Buku::query()
    ->when($search, function ($query, $search) {
        $query->where(function ($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('penulis', 'like', "%{$search}%")
              ->orWhere('penerbit', 'like', "%{$search}%");
        });
    })

    ->when($request->input('penerbit'), function ($query, $penerbit) {
        $query->where('penerbit', $penerbit);})
    ->when($request->input('status') == 'tersedia', function ($query) {
        $query->where('stok', '>', 0);})
    ->when($request->input('status') == 'habis', function ($query) {
        $query->where('stok', 0);})

    ->when($request->input('sort') == 'judul', function ($query) {
        $query->orderBy('judul', 'asc');})
    ->when($request->input('sort') == 'tahun', function ($query) {
        $query->orderBy('tahun_terbit', 'desc');})
    ->when($request->input('sort') == 'stok', function ($query) {
        $query->orderBy('stok', 'desc');})
    ->when(!$request->input('sort') || $request->input('sort') == 'terbaru', function ($query) {
        $query->latest();})

    ->paginate(8)
    ->withQueryString();

    $listPenerbit = Buku::select('penerbit')
    ->distinct()
    ->whereNotNull('penerbit')
    ->orderBy('penerbit')
    ->pluck('penerbit');

        return view('home', compact('buku', 'listPenerbit'));
    }

    /**
     * Menampilkan halaman detail informasi sebuah buku untuk publik/mahasiswa.
     */
    public function show(Buku $buku)
    {
        // TODO 3: Kembalikan view 'home_show' membawa data $buku
        return view('home_show', compact('buku'));
    }
}
