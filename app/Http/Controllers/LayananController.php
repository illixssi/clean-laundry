<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class LayananController extends Controller
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
        $allowedRoles = ['owner', 'admin', 'kepala_operasional', 'kasir'];

        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }
        // Mendapatkan query pencarian dari input
        $search = $request->input('search');

        // Query untuk mendapatkan data layanan dengan pencarian jika ada input
        $services = Service::whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('service_name', 'like', "%$search%")
                        ->orWhere('unit', 'like', "%$search%")
                        ->orWhere('price', 'like', "%$search%");
                });
            })
            ->orderBy('service_name', 'asc') // Mengurutkan berdasarkan nama layanan
            ->paginate(10); // 10 item per halaman

        return view('layanan.index', compact('services', 'search', 'roleName'));
    }

    public function create()
    {
        // Menampilkan form tambah layanan
        return view('layanan.create');
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
        $allowedRoles = ['admin', 'kepala_operasional'];
        if (!in_array($roleName, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses.');
        }

        $validator = Validator::make($request->all(), [
            'service_name' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric',
        ], [
            'service_name.required' => 'Nama layanan wajib diisi.',
            'service_name.string' => 'Nama layanan harus berupa teks.',
            'service_name.max' => 'Nama layanan tidak boleh lebih dari 100 karakter.',
            'unit.required' => 'Satuan layanan wajib diisi.',
            'unit.string' => 'Satuan layanan harus berupa teks.',
            'unit.max' => 'Satuan layanan tidak boleh lebih dari 50 karakter.',
            'price.required' => 'Harga layanan wajib diisi.',
            'price.numeric' => 'Harga layanan harus berupa angka.',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode('<br>', $validator->messages()->all());
            return back()->with('modal-error', $errorMessage)->withInput($request->input());
        }

        try {
            service::create([
                'service_name' => $request->input('service_name'),
                'unit' => $request->input('unit'),
                'price' => $request->input('price'),
                'created_at' => Carbon::now(),
                'created_by' => $tokenData['user_id'],
            ]);

            return back()->with(['modal-success' => 'Data layanan berhasil ditambahkan', 'redirectRoute' => route('layanan')]);
        } catch (\Exception $e) {
            return back()->with('modal-error', 'Terjadi kesalahan pada server.');
        }
    }

    public function edit($id)
    {
        $service = service::findOrFail($id);
        return view('layanan.edit', compact('service'));
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
            'service_name' => 'required|string|max:100',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric', // Gunakan numeric untuk angka desimal
        ], [
            'service_name.required' => 'Nama layanan wajib diisi.',
            'service_name.string' => 'Nama layanan harus berupa teks.',
            'service_name.max' => 'Nama layanan tidak boleh lebih dari 100 karakter.',
            'unit.required' => 'Satuan layanan wajib diisi.',
            'unit.string' => 'Satuan layanan harus berupa teks.',
            'unit.max' => 'Satuan layanan tidak boleh lebih dari 50 karakter.',
            'price.required' => 'Harga layanan wajib diisi.',
            'price.numeric' => 'Harga layanan harus berupa angka.',
        ]);

        if ($validator->fails()) {
            $errorMessage = implode('<br>', $validator->messages()->all());
            return back()->with('modal-error', $errorMessage)->withInput($request->input());
        }

        try {
            service::where('id', $id)->update([
                'service_name' => $request->input('service_name'),
                'unit' => $request->input('unit'),
                'price' => $request->input('price'),
                'updated_at' => Carbon::now(),
                'updated_by' => $tokenData['user_id'],
            ]);

            return back()->with(['modal-success' => 'Data layanan berhasil diperbarui', 'redirectRoute' => route('layanan')]);
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

        $service = service::find($id);
        if ($service) {
            $service->update([
                'deleted_at' => Carbon::now(),
                'deleted_by' => $tokenData['user_id']
            ]);
            return redirect()->route('layanan')->with('success', 'Data layanan Berhasil dihapus!');
        }

        return redirect()->back()->with('error', 'Data tidak ditemukan');
    }
}
