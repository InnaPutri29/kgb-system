<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterPejabat;
use Illuminate\Http\Request;

class MasterPejabatController extends Controller
{
    public function index()
    {
        $pejabat = MasterPejabat::latest()->get();
        return view('admin.master-pejabat.index', compact('pejabat'));
    }

    public function show(MasterPejabat $masterPejabat)
    {
        $masterPejabat->load('riwayatKgb.pegawai');
        return view('admin.master-pejabat.show', compact('masterPejabat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan'    => 'required|string|max:255',
            'nama_pejabat'    => 'nullable|string|max:255',
        ]);

        MasterPejabat::create($request->only(['nama_jabatan', 'nama_pejabat']));

        return back()->with('success', 'Data pejabat berhasil ditambahkan.');
    }

    public function update(Request $request, MasterPejabat $masterPejabat)
    {
        $request->validate([
            'nama_jabatan'    => 'required|string|max:255',
            'nama_pejabat'    => 'nullable|string|max:255',
        ]);

        $masterPejabat->update($request->only(['nama_jabatan', 'nama_pejabat']));

        return back()->with('success', 'Data pejabat berhasil diperbarui.');
    }

    public function destroy(MasterPejabat $masterPejabat)
    {
        $masterPejabat->delete();
        return back()->with('success', 'Data pejabat berhasil dihapus.');
    }
}
