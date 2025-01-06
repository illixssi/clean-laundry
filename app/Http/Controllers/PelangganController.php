<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class PelangganController extends Controller
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
        $allowedRoles = ['admin', 'kepala_operasional', 'kasir'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }
        // Mendapatkan query pencarian dari input
        $search = $request->input('search');

        // Query untuk mendapatkan data pelanggan dengan pencarian jika ada input
        $customers = Customer::whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%")
                        ->orWhere('phone_number', 'like', "%$search%")
                        ->orWhere('address', 'like', "%$search%");
                });
            })
            ->orderBy('name', 'asc')
            ->paginate(10); // 10 item per halaman

        return view('pelanggan.index', compact('customers', 'search', 'roleName'));
    }

    public function create()
    {
        // Menampilkan form tambah pelanggan
        return view('pelanggan.create');
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
            'name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
        ], [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'name.string' => 'Nama pelanggan harus berupa teks.',
            'name.max' => 'Nama pelanggan tidak boleh lebih dari 100 karakter.',
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.string' => 'Nomor telepon harus berupa teks.',
            'phone_number.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            'address.required' => 'Alamat pelanggan wajib diisi.',
            'address.string' => 'Alamat pelanggan harus berupa teks.',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode('<br>', $validator->messages()->all());
            return back()->with('modal-error', $errorMessage)->withInput($request->input());
        }

        try {
            Customer::create([
                'name' => $request->input('name'),
                'phone_number' => $request->input('phone_number'),
                'address' => $request->input('address'),
                'created_at' => Carbon::now(),
                'created_by' => $tokenData['user_id'],
            ]);

            return back()->with(['modal-success' => 'Data pelanggan berhasil ditambahkan', 'redirectRoute' => route('pelanggan')]);
        } catch (\Exception $e) {
            return back()->with('modal-error', 'Terjadi kesalahan pada server.');
        }
    }

    public function edit($id)
    {
        // Ambil data pelanggan berdasarkan ID
        $customer = Customer::findOrFail($id);

        // Tampilkan form edit pelanggan dengan data yang sudah ada
        return view('pelanggan.edit', compact('customer'));
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
        $allowedRoles = ['admin', 'kepala_operasional', 'kasir'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
        ], [
            'name.required' => 'Nama pelanggan wajib diisi.',
            'name.string' => 'Nama pelanggan harus berupa teks.',
            'name.max' => 'Nama pelanggan tidak boleh lebih dari 100 karakter.',
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.string' => 'Nomor telepon harus berupa teks.',
            'phone_number.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            'address.required' => 'Alamat pelanggan wajib diisi.',
            'address.string' => 'Alamat pelanggan harus berupa teks.',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode('<br>', $validator->messages()->all());
            return back()->with('modal-error', $errorMessage)->withInput($request->input());
        }

        try {
            Customer::where('id', $id)->update([
                'name' => $request->input('name'),
                'phone_number' => $request->input('phone_number'),
                'address' => $request->input('address'),
                'updated_at' => Carbon::now(),
                'updated_by' => $tokenData['user_id'],
            ]);

            return back()->with(['modal-success' => 'Data pelanggan berhasil diperbarui', 'redirectRoute' => route('pelanggan')]);
        } catch (\Exception $e) {
            return back()->with('modal-error', 'Terjadi kesalahan pada server.');
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

        $customer = Customer::find($id);
        if ($customer) {
            $customer->update([
                'deleted_at' => Carbon::now(),
                'deleted_by' => $tokenData['user_id']
            ]);
            return redirect()->route('pelanggan')->with('success', 'Data Pelanggan Berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan');
    }
}
