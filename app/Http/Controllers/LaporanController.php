<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->cookie('user_token');
        $tokenData = json_decode(base64_decode($token), true);

        $redisTokenKey = 'user_token:' . $tokenData['user_id'];
        $redisToken = Redis::get($redisTokenKey);

        if (!$redisToken || $redisToken !== $token) {
            return redirect()->route('login')->with('error', 'Session telah berakhir atau token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['user_id']) || empty($tokenData['user_id'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['role_name']) || empty($tokenData['role_name'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        $roleName = $tokenData['role_name'];
        $allowedRoles = ['admin', 'owner'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        // Set default date range: kemarin hingga hari ini
        $yesterday = Carbon::yesterday()->format('d/m/Y');
        $today = Carbon::today()->format('d/m/Y');
        $defaultDateRange = $yesterday . ' - ' . $today;

        $transactions = Transaction::with('customer');

        // Filter berdasarkan rentang tanggal jika ada
        if ($request->has('date_range') && !empty($request->date_range)) {
            [$start, $end] = explode(' - ', $request->date_range);

            // Ubah format tanggal dan sesuaikan waktu
            $startDate = Carbon::createFromFormat('d/m/Y', $start)->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $end)->endOfDay();

            // Terapkan filter rentang tanggal
            $transactions = $transactions->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            // Gunakan default range (kemarin hingga hari ini)
            $startDate = Carbon::yesterday()->startOfDay();
            $endDate = Carbon::today()->endOfDay();

            $transactions = $transactions->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Terapkan paginasi setelah filter
        $transactions = $transactions->orderBy('created_at', 'asc')->paginate(10);

        return view('laporan.index', compact('transactions', 'roleName', 'defaultDateRange'));
    }

    public function generatePDF(Request $request)
    {
        $token = $request->cookie('user_token');
        $tokenData = json_decode(base64_decode($token), true);

        $redisTokenKey = 'user_token:' . $tokenData['user_id'];
        $redisToken = Redis::get($redisTokenKey);

        if (!$redisToken || $redisToken !== $token) {
            return redirect()->route('login')->with('error', 'Session telah berakhir atau token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['user_id']) || empty($tokenData['user_id'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        if (!isset($tokenData['role_name']) || empty($tokenData['role_name'])) {
            return redirect()->route('login')->with('error', 'Token tidak valid. Silakan login kembali.');
        }

        $roleName = $tokenData['role_name'];
        $allowedRoles = ['admin', 'owner'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        // Filter transactions for PDF
        $transactions = Transaction::query();

        if ($request->has('date_range') && !empty($request->date_range)) {
            [$start, $end] = explode(' - ', $request->date_range);
            $startDate = Carbon::createFromFormat('d/m/Y', $start)->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', $end)->endOfDay();

            $transactions = $transactions->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            // Gunakan default range (kemarin hingga hari ini)
            $startDate = Carbon::yesterday()->startOfDay();
            $endDate = Carbon::today()->endOfDay();

            $transactions = $transactions->whereBetween('created_at', [$startDate, $endDate]);
        }

        $transactions = $transactions->get();

        // Cek jika tidak ada data
        if ($transactions->isEmpty()) {
            return redirect()->route('laporan')->with('error', 'Tidak ada data untuk rentang tanggal yang dipilih.');
        }

        // Format nama file PDF
        $fileName = Carbon::now()->format('YmdHi') . '_Laporan_CleanLaundry.pdf';

        $dateRange = $request->input('date_range', Carbon::yesterday()->format('d/m/Y') . ' - ' . Carbon::today()->format('d/m/Y'));

        $pdf = PDF::loadView('laporan.pdf', compact('transactions', 'dateRange'));

        return $pdf->download($fileName);
    }
}
