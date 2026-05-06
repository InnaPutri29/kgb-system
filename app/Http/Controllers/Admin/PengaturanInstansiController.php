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
                'nama_instansi' => 'UOBK Rumah Sakit Paru Provinsi Jawa Barat',
                'alamat' => 'Jl. Raya Sidawangi, Kec. Sumber, Kab. Cirebon',
                'nama_direktur' => 'dr. Budi Santoso, M.Kes',
                'nip_direktur' => '19700101 200003 1 001',
                'pangkat_direktur' => 'Pembina Utama Muda (IV/c)'
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
