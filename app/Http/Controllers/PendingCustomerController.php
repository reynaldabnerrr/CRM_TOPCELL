<?php

namespace App\Http\Controllers;

use App\Models\PendingCustomer;
use Illuminate\Http\Request;

class PendingCustomerController extends Controller
{
    public function index()
    {
        $status = request('status', 'Chat masuk');
        $customers = PendingCustomer::where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $statuses = [
            'Chat masuk',
            'On proses',
            'Follow up h+1',
            'Follow up h+2',
            'Follow up h+3',
            'Closing',
            'Lost contact',
            'Budget kurang',
            'Barang kosong',
            'Tunggu kabar keluarga',
            'Perbandingan harga',
            'Sudah beli di toko lain'
        ];

        return view('pending-customers.index', compact('customers', 'statuses', 'status'));
    }

    public function create()
    {
        $statuses = [
            'Chat masuk',
            'On proses',
            'Follow up h+1',
            'Follow up h+2',
            'Follow up h+3',
            'Closing',
            'Lost contact',
            'Budget kurang',
            'Barang kosong',
            'Tunggu kabar keluarga',
            'Perbandingan harga',
            'Sudah beli di toko lain'
        ];

        return view('pending-customers.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'status' => 'required|in:Chat masuk,On proses,Follow up h+1,Follow up h+2,Follow up h+3,Closing,Lost contact,Budget kurang,Barang kosong,Tunggu kabar keluarga,Perbandingan harga,Sudah beli di toko lain',
            'notes' => 'nullable|string',
        ]);

        PendingCustomer::create($request->all());

        return redirect()
            ->route('pending-customers.index')
            ->with('success', 'Data calon customer berhasil ditambahkan!');
    }

    public function edit(PendingCustomer $pendingCustomer)
    {
        $statuses = [
            'Chat masuk',
            'On proses',
            'Follow up h+1',
            'Follow up h+2',
            'Follow up h+3',
            'Closing',
            'Lost contact',
            'Budget kurang',
            'Barang kosong',
            'Tunggu kabar keluarga',
            'Perbandingan harga',
            'Sudah beli di toko lain'
        ];

        return view('pending-customers.edit', compact('pendingCustomer', 'statuses'));
    }

    public function update(Request $request, PendingCustomer $pendingCustomer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'status' => 'required|in:Chat masuk,On proses,Follow up h+1,Follow up h+2,Follow up h+3,Closing,Lost contact,Budget kurang,Barang kosong,Tunggu kabar keluarga,Perbandingan harga,Sudah beli di toko lain',
            'notes' => 'nullable|string',
        ]);

        $pendingCustomer->update($request->all());

        return redirect()
            ->route('pending-customers.index')
            ->with('success', 'Data calon customer berhasil diupdate!');
    }

    public function destroy(PendingCustomer $pendingCustomer)
    {
        $pendingCustomer->delete();

        return redirect()
            ->route('pending-customers.index')
            ->with('success', 'Data calon customer berhasil dihapus!');
    }
}
