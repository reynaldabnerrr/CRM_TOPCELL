<?php

namespace App\Http\Controllers;

use App\Models\CustomerAftercare;
use App\Models\Sale;
use Illuminate\Http\Request;

class CustomerAfterCareController extends Controller
{
    public function index()
    {
        $type = request('type', 'Aftercare h+1');
        $status = request('status', 'pending');

        $query = CustomerAftercare::query();

        if ($type) {
            $query->where('type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $records = $query->orderBy('scheduled_date')->paginate(20);

        $types = ['Aftercare h+1', 'Followup h+7', 'Followup h+1bulan'];
        $statuses = ['pending', 'completed', 'skipped'];

        return view('aftercare.index', compact('records', 'types', 'statuses', 'type', 'status'));
    }

    public function markComplete(CustomerAftercare $aftercare)
    {
        $aftercare->update([
            'status' => 'completed',
            'done_date' => now()->toDateString(),
        ]);

        return back()->with('success', 'Aftercare berhasil ditandai selesai!');
    }

    public function markSkipped(CustomerAftercare $aftercare)
    {
        $aftercare->update([
            'status' => 'skipped',
        ]);

        return back()->with('success', 'Aftercare ditandai skip!');
    }

    public function edit(CustomerAftercare $aftercare)
    {
        return view('aftercare.edit', compact('aftercare'));
    }

    public function update(Request $request, CustomerAftercare $aftercare)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,completed,skipped',
        ]);

        $data = $request->only('notes', 'status');
        if ($request->status === 'completed') {
            $data['done_date'] = now()->toDateString();
        }

        $aftercare->update($data);

        return redirect()
            ->route('aftercare.index')
            ->with('success', 'Aftercare berhasil diupdate!');
    }
}
