<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Service;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use Illuminate\Support\Facades\Redis;
use PDF;
use Illuminate\Support\Carbon;

class TransaksiController extends Controller
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

        $search = $request->input('search');

        // Query untuk mendapatkan data transaksi dengan pagination dan pencarian
        $transactions = Transaction::with('customer')
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->when($search, function ($query, $search) {
                $query->where('order_number', 'like', "%$search%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 item per halaman

        return view('transaksi.index', compact('transactions', 'search', 'roleName'));
    }

    public function create()
    {
        $services = Service::all(); // Ambil semua data peran
        $customers = Customer::all(); // Mengambil semua pelanggan untuk dropdown
        return view('transaksi.create', compact('services', 'customers'));
    }

    public function store(Request $request)
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
        $allowedRoles = ['admin', 'kepala_operasional', 'kasir'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|numeric|exists:customers,id',
            'notes' => 'nullable|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'clothes_quantity' => 'required|numeric|min:1',
            'details' => 'required|array|min:1',
            'details.*.service_id' => 'required|numeric|exists:services,id',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.price' => 'required|numeric|min:0',
        ], [
            'customer_id.required' => 'Pelanggan wajib dipilih.',
            'total_price.required' => 'Total harga wajib diisi.',
            'clothes_quantity.required' => 'Jumlah pakaian wajib diisi.',
            'details.required' => 'Detail transaksi wajib diisi.',
            'details.*.service_id.required' => 'Layanan wajib dipilih.',
            'details.*.quantity.required' => 'Kuantitas wajib diisi.',
            'details.*.price.required' => 'Harga wajib diisi.',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode('<br>', $validator->messages()->all());
            return back()->with('modal-error', $errorMessage)->withInput($request->input());
        }

        DB::beginTransaction();

        try {
            // Insert transaction
            $transaction = Transaction::create([
                'order_number' => 'test',
                'customer_id' => $request->input('customer_id'),
                'notes' => $request->input('notes'),
                'total_price' => $request->input('total_price'),
                'clothes_quantity' => $request->input('clothes_quantity'),
                'created_at' => now(),
                'created_by' => $tokenData['user_id'],
                'status' => 'Dalam Proses',
            ]);

            // Insert transaction details
            foreach ($request->input('details') as $detail) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'service_id' => $detail['service_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ]);
            }

            DB::commit();

            return back()->with([
                'modal-tr-print' => 'Data transaksi berhasil ditambahkan',
                'transaction_id' => $transaction->id, // Tambahkan ID transaksi ke response
                'redirectRoute' => route('transaksi')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('modal-error', 'Terjadi kesalahan pada server: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $transaction = Transaction::with(['customer', 'details.service'])->findOrFail($id);
        $services = Service::all(); // Mengambil semua layanan untuk dropdown
        $customers = Customer::all(); // Mengambil semua pelanggan untuk dropdown
        $isEdit = true;
        return view('transaksi.edit', compact('transaction', 'services', 'customers', 'isEdit'));
    }

    public function update(Request $request, $id)
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
        $allowedRoles = ['admin', 'kepala_operasional'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:255',
            'total_price' => 'required|numeric|min:0',
            'clothes_quantity' => 'required|numeric|min:1',
            'details' => 'required|array|min:1',
            'details.*.service_id' => 'required|numeric|exists:services,id',
            'details.*.quantity' => 'required|numeric|min:1',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode('<br>', $validator->messages()->all());
            return back()->with('modal-error', $errorMessage)->withInput($request->input());
        }

        DB::beginTransaction();

        try {
            // Update transaksi utama tanpa mengubah `customer_id`
            $transaction = Transaction::findOrFail($id);
            $transaction->update([
                'notes' => $request->input('notes'),
                'total_price' => $request->input('total_price'),
                'clothes_quantity' => $request->input('clothes_quantity'),
                'updated_at' => now(),
                'updated_by' => $tokenData['user_id'],
            ]);

            // Ambil detail ID dari request untuk menentukan mana yang perlu dihapus
            $requestDetailIds = collect($request->input('details'))->pluck('service_id')->toArray();

            // Hapus detail yang tidak ada di request
            $transaction->details()->whereNotIn('service_id', $requestDetailIds)->delete();

            // Update atau insert detail transaksi
            foreach ($request->input('details') as $detail) {
                $transactionDetail = $transaction->details()->where('service_id', $detail['service_id'])->first();

                if ($transactionDetail) {
                    // Jika detail sudah ada, lakukan update
                    $transactionDetail->update([
                        'quantity' => $detail['quantity'],
                        'price' => $detail['price'],
                    ]);
                } else {
                    // Jika detail tidak ada, lakukan insert baru
                    $transaction->details()->create([
                        'service_id' => $detail['service_id'],
                        'quantity' => $detail['quantity'],
                        'price' => $detail['price'],
                    ]);
                }
            }

            DB::commit();

            return back()->with(['modal-success' => 'Data transaksi berhasil diperbarui', 'redirectRoute' => route('transaksi')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('modal-error', 'Terjadi kesalahan pada server: ' . $e->getMessage());
        }
    }

    public function delete($id, Request $request)
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
        $allowedRoles = ['admin', 'kepala_operasional'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        try {
            $transaction = Transaction::findOrFail($id);

            // Update kolom deleted_at dan deleted_by pada transaksi utama
            $transaction->update([
                'deleted_at' => now(),
                'deleted_by' => $tokenData['user_id'],
            ]);

            return redirect()->route('transaksi')->with('success', 'Data transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan pada server.');
        }
    }

    public function viewDetail(Request $request, $id)
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

        // Mendapatkan transaksi berdasarkan ID beserta relasinya dengan customer dan details
        $transaction = Transaction::with(['customer', 'details.service'])->findOrFail($id);

        // Mengirim data transaksi ke view
        return view('transaksi.view', compact('transaction', 'roleName'));
    }

    public function updateStatus(Request $request, $id)
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
        $allowedRoles = ['admin', 'kepala_operasional', 'kasir', 'staf_laundry'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }
        // Validasi input status dari request
        $request->validate([
            'status' => 'required|string'
        ]);

        $transaction = Transaction::findOrFail($id);
        $transaction->status = $request->input('status');
        $transaction->updated_at = now();
        $transaction->updated_by = $tokenData['user_id'];

        $transaction->save();

        return redirect()->route('transaksi.view', $transaction->id)->with('success', 'Status berhasil diperbarui.');
    }

    public function generatePDF(Request $request)
    {
        $dateRange = $request->input('date_range', Carbon::today()->format('d/m/Y') . ' - ' . Carbon::today()->format('d/m/Y'));
        $transactions = Transaction::with('customer')
            ->whereBetween('created_at', [/*tanggal mulai dan akhir berdasarkan $dateRange*/])
            ->get();

        $pdf = PDF::loadView('pdf', compact('transactions', 'dateRange'));
        return $pdf->download('laporan_transaksi.pdf');
    }
}
