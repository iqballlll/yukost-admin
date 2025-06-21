<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'approved');
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = Tenant::query();

        // dd($tab);

        // Filter berdasarkan tab
        if ($tab === 'registered') {
            $query->where('status', 'pending');
        } else {
            $query->where('status', 'approved');
        }

        // Filter pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        // Ambil data paginasi
        $tenants = $query->paginate($perPage)->withQueryString();

        $data = [
            'title' => 'Penyewa',
            'tenants' => $tenants,
            'tab' => $tab,
            'perPage' => $perPage,
            'search' => $search
        ];

        return view('pages.tenant.index', $data);
    }


    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'note' => 'required_if:status,rejected|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }



        $tenant = Tenant::findOrFail($id);
        $tenant->status = $request->status;
        $tenant->note = $request->status === 'ditolak' ? $request->alasan : null;
        $tenant->save();

        return redirect()->back()->with('success', 'Status tenant berhasil diperbarui.');
    }
}
