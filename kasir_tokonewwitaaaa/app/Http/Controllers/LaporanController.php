<?php

namespace App\Http\Controllers;
use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.form');
    }

    public function harian(Request $request)
    {
        $penjualan = Penjualan::join('users', 'users.id', '=', 'penjualans.user_id')
            ->leftJoin('pelanggans', 'pelanggans.id', '=', 'penjualans.pelanggan_id')
            ->whereDate('penjualans.tanggal', $request->tanggal)
            ->select('penjualans.*', 'pelanggans.nama as nama_pelanggan', 'users.nama as nama_kasir')
            ->orderBy('penjualans.id')
            ->get();

        return view('laporan.harian', [
            'penjualan' => $penjualan
        ]);
    }

    public function bulanan(Request $request)
{
    $penjualan = Penjualan::select(
            DB::raw('COUNT(id) as jumlah_transaksi'),
            DB::raw('SUM(total) as jumlah_total'),
            DB::raw('DATE_FORMAT(tanggal, "%d/%m/%Y") as tgl')
        )
        ->whereMonth('tanggal', $request->bulan)
        ->whereYear('tanggal', $request->tahun)
        ->where('status', '!=', 'batal')
        ->groupBy('tgl')
        ->get();

    $nama_bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $bulan = isset($nama_bulan[$request->bulan]) ? $nama_bulan[$request->bulan] : null;

    return view('laporan.bulanan', ['penjualan' => $penjualan, 'bulan' => $bulan]);
    }
}
