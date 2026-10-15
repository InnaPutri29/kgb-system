<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanInstansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanInstansiController extends Controller
{
    public function index()
    {
        // Ambil data pertama, jika kosong buatkan default kosong
        $pengaturan = PengaturanInstansi::firstOrCreate(
            ['id' => 1],
            [
                'nama_instansi' => 'RUMAH SAKIT DAERAH SIDAWANGI',
                'alamat' => "Jalan Pangeran Kejaksan Sumber Telepon (0231) 833070 Fax : (0231) 8330747\nWebsite: www.rsp.jabarprov.go.id e-mail : rsp@jabarprov.go.id\nC I R E B O N - 4 5 6 1 1",
                'nama_direktur' => 'dr. YOGA PRAMADIA',
                'nip_direktur' => '196812042005011008',
                'pangkat_direktur' => 'Pembina Tk.I'
            ]
        );

        return view('admin.pengaturan-instansi.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_instansi' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nama_direktur' => 'required|string|max:255',
            'nip_direktur' => 'required|string|max:50',
            'pangkat_direktur' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $pengaturan = PengaturanInstansi::first();

        $data = $request->only(['nama_instansi', 'alamat', 'nama_direktur', 'nip_direktur', 'pangkat_direktur']);

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($pengaturan->logo) {
                Storage::disk('public')->delete($pengaturan->logo);
            }
            
            // Simpan logo baru
            $path = $request->file('logo')->store('logo_instansi', 'public');
            $data['logo'] = $path;
        }

        $pengaturan->update($data);

        return back()->with('success', 'Pengaturan Instansi berhasil diperbarui.');
    }
}
