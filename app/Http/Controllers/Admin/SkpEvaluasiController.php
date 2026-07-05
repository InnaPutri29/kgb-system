<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\SkpEvaluasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SkpEvaluasiController extends Controller
{
    public function store(Request $request, Pegawai $pegawai)
    {
        $request->validate([
            'tahun_penilaian' => ['required', 'integer', 'min:2000', 'max:' . now()->year],
            'predikat'        => ['required', 'in:Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang'],
            'file_bukti_skp'  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'tahun_penilaian.required' => 'Tahun penilaian wajib diisi.',
            'tahun_penilaian.max'      => 'Tahun penilaian tidak boleh melebihi tahun ini.',
            'predikat.required'        => 'Predikat SKP wajib dipilih.',
            'predikat.in'              => 'Predikat SKP tidak valid.',
        ]);

        // Cek duplikasi: 1 tahun hanya 1 entri per pegawai
        $exists = SkpEvaluasi::where('pegawai_id', $pegawai->id)
            ->where('tahun_penilaian', $request->tahun_penilaian)
            ->exists();

        if ($exists) {
            return back()->with('error', "Data SKP tahun {$request->tahun_penilaian} untuk pegawai ini sudah ada.");
        }

        $filePath = null;
        if ($request->hasFile('file_bukti_skp')) {
            $filePath = $request->file('file_bukti_skp')
                ->store("skp/{$pegawai->nip}", 'public');
        }

        SkpEvaluasi::create([
            'pegawai_id'      => $pegawai->id,
            'tahun_penilaian' => $request->tahun_penilaian,
            'predikat'        => $request->predikat,
            'file_bukti_skp'  => $filePath,
        ]);

        return back()->with('success', "Data SKP tahun {$request->tahun_penilaian} berhasil ditambahkan.");
    }

    public function update(Request $request, Pegawai $pegawai, SkpEvaluasi $skp)
    {
        $request->validate([
            'tahun_penilaian' => ['required', 'integer', 'min:2000', 'max:' . now()->year],
            'predikat'        => ['required', 'in:Sangat Baik,Baik,Cukup,Kurang,Sangat Kurang'],
            'file_bukti_skp'  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Cek duplikasi kecuali diri sendiri
        $exists = SkpEvaluasi::where('pegawai_id', $pegawai->id)
            ->where('tahun_penilaian', $request->tahun_penilaian)
            ->where('id', '!=', $skp->id)
            ->exists();

        if ($exists) {
            return back()->with('error', "Data SKP tahun {$request->tahun_penilaian} untuk pegawai ini sudah ada.");
        }

        $filePath = $skp->file_bukti_skp;
        if ($request->hasFile('file_bukti_skp')) {
            // Hapus file lama
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file_bukti_skp')
                ->store("skp/{$pegawai->nip}", 'public');
        }

        $skp->update([
            'tahun_penilaian' => $request->tahun_penilaian,
            'predikat'        => $request->predikat,
            'file_bukti_skp'  => $filePath,
        ]);

        return back()->with('success', "Data SKP tahun {$request->tahun_penilaian} berhasil diperbarui.");
    }

    public function destroy(Pegawai $pegawai, SkpEvaluasi $skp)
    {
        if ($skp->file_bukti_skp) {
            Storage::disk('public')->delete($skp->file_bukti_skp);
        }
        $skp->delete();

        return back()->with('success', 'Data SKP berhasil dihapus.');
    }
}
