<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\PkpEvaluasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PkpEvaluasiController extends Controller
{
    public function store(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'tahun_penilaian' => ['required', 'integer', 'min:2000', 'max:' . now()->year],
            'predikat'        => ['required', 'in:Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang'],
            'file_bukti_pkp'  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'tahun_penilaian.required' => 'Tahun penilaian wajib diisi.',
            'tahun_penilaian.max'      => 'Tahun penilaian tidak boleh melebihi tahun ini.',
            'predikat.required'        => 'Predikat PKP wajib dipilih.',
            'predikat.in'              => 'Predikat PKP tidak valid.',
        ]);

        // Cek duplikasi: 1 tahun hanya 1 entri per pegawai
        $exists = PkpEvaluasi::where('pegawai_id', $pegawai->id)
            ->where('tahun_penilaian', $request->tahun_penilaian)
            ->exists();

        if ($exists) {
            return back()->with('error', "Data PKP tahun {$request->tahun_penilaian} untuk pegawai ini sudah ada.");
        }

        $filePath = null;
        if ($request->hasFile('file_bukti_pkp')) {
            $filePath = $request->file('file_bukti_pkp')
                ->store("skp/{$pegawai->nip}", 'public');
        }

        PkpEvaluasi::create([
            'pegawai_id'      => $pegawai->id,
            'tahun_penilaian' => $request->tahun_penilaian,
            'predikat'        => $request->predikat,
            'file_bukti_pkp'  => $filePath,
        ]);

        return back()->with('success', "Data PKP tahun {$request->tahun_penilaian} berhasil ditambahkan.");
    }

    public function update(Request $request, Pegawai $pegawai, PkpEvaluasi $pkp)
    {
        $request->validate([
            'tahun_penilaian' => ['required', 'integer', 'min:2000', 'max:' . now()->year],
            'predikat'        => ['required', 'in:Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang'],
            'file_bukti_pkp'  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Cek duplikasi kecuali diri sendiri
        $exists = PkpEvaluasi::where('pegawai_id', $pegawai->id)
            ->where('tahun_penilaian', $request->tahun_penilaian)
            ->where('id', '!=', $pkp->id)
            ->exists();

        if ($exists) {
            return back()->with('error', "Data PKP tahun {$request->tahun_penilaian} untuk pegawai ini sudah ada.");
        }

        $filePath = $pkp->file_bukti_pkp;
        if ($request->hasFile('file_bukti_pkp')) {
            // Hapus file lama
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file_bukti_pkp')
                ->store("skp/{$pegawai->nip}", 'public');
        }

        $pkp->update([
            'tahun_penilaian' => $request->tahun_penilaian,
            'predikat'        => $request->predikat,
            'file_bukti_pkp'  => $filePath,
        ]);

        return back()->with('success', "Data PKP tahun {$request->tahun_penilaian} berhasil diperbarui.");
    }

    public function destroy(Pegawai $pegawai, PkpEvaluasi $pkp)
    {
        if ($pkp->file_bukti_pkp) {
            Storage::disk('public')->delete($pkp->file_bukti_pkp);
        }
        $pkp->delete();

        return back()->with('success', 'Data PKP berhasil dihapus.');
    }
}
