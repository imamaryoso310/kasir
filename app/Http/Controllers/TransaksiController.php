<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;

class TransaksiController extends Controller
{
    // Tampilkan halaman transaksi
    public function index()
    {
        $transaksis = Transaksi::latest()->get();
        return view('transaksi', compact('transaksis'));
    }

    // Tambah data transaksi
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'total_harga' => 'required|numeric',
            'bayar' => 'required|numeric',
            'kembalian' => 'required|numeric',
            'kasir' => 'required|string|max:100',
            'metode' => 'required|string',
        ]);

        Transaksi::create($request->all());

        return response()->json(['message' => 'Transaksi berhasil ditambahkan']);
    }

    // Update data transaksi
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'total_harga' => 'required|numeric',
            'bayar' => 'required|numeric',
            'kembalian' => 'required|numeric',
            'kasir' => 'required|string|max:100',
            'metode' => 'required|string',
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update($request->all());

        return response()->json(['message' => 'Transaksi berhasil diperbarui']);
    }

    // Hapus data transaksi
    public function destroy($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return response()->json(['message' => 'Transaksi berhasil dihapus']);
    }
}
