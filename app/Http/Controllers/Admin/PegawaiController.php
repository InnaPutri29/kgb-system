<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\PegawaiImport;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::with('user')->latest()->paginate(15);
        return view('admin.pegawai.index', compact('pegawai'));
    }

    public function showImportForm()
    {
        return view('admin.pegawai.import');
    }

    public function create()
    {
        return view('admin.pegawai.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:pegawai,nip',
            'nama_lengkap' => 'required|string|max:255',
            'pangkat' => 'nullable|string|max:100',
            'golongan' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:255',
            'kantor_tempat_kerja' => 'nullable|string|max:255',
            'tmt_gaji_terakhir' => 'nullable|date',
            'masa_kerja_tahun' => 'required|integer|min:0',
            'masa_kerja_bulan' => 'required|integer|min:0|max:11',
            'gaji_pokok_terakhir' => 'nullable|numeric',
            'is_sedang_hukuman_disiplin' => 'boolean'
        ]);

        DB::transaction(function () use ($validated) {
            // Create user account
            $user = User::create([
                'name' => $validated['nama_lengkap'],
                'nip' => $validated['nip'],
                'email' => strtolower(str_replace(' ', '', $validated['nip'])) . '@kgb.rsd-sidawangi.id',
                'password' => Hash::make($validated['nip']),
                'is_first_login' => true,
            ]);
            
            $user->assignRole('pegawai');

            // Create pegawai record
            $validated['user_id'] = $user->id;
            $validated['is_sedang_hukuman_disiplin'] = $validated['is_sedang_hukuman_disiplin'] ?? false;
            
            Pegawai::create($validated);
        });

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function show(Pegawai $pegawai)
    {
        $pegawai->load(['riwayatKgb', 'skpEvaluasi' => fn($q) => $q->orderByDesc('tahun_penilaian')]);
        return view('admin.pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        return view('admin.pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:50', Rule::unique('pegawai', 'nip')->ignore($pegawai->id)],
            'nama_lengkap' => 'required|string|max:255',
            'pangkat' => 'nullable|string|max:100',
            'golongan' => 'nullable|string|max:20',
            'jabatan' => 'nullable|string|max:255',
            'kantor_tempat_kerja' => 'nullable|string|max:255',
            'tmt_gaji_terakhir' => 'nullable|date',
            'masa_kerja_tahun' => 'required|integer|min:0',
            'masa_kerja_bulan' => 'required|integer|min:0|max:11',
            'gaji_pokok_terakhir' => 'nullable|numeric',
            'is_sedang_hukuman_disiplin' => 'boolean'
        ]);

        DB::transaction(function () use ($validated, $pegawai) {
            $validated['is_sedang_hukuman_disiplin'] = $validated['is_sedang_hukuman_disiplin'] ?? false;
            $pegawai->update($validated);

            if ($pegawai->user) {
                $pegawai->user->update([
                    'name' => $validated['nama_lengkap'],
                    'nip' => $validated['nip'],
                ]);
            }
        });

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.mimes' => 'Format file harus .xlsx atau .xls.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        try {
            $import = new PegawaiImport();
            Excel::import($import, $request->file('file'));

            $errors = $import->errors();
            if ($errors->count() > 0) {
                $errorMessages = $errors->map(fn($e) => $e->getMessage())->join(', ');
                return back()->with('warning', "Import selesai dengan beberapa baris dilewati: {$errorMessages}");
            }

            return redirect()->route('admin.pegawai.index')
                ->with('success', 'Data pegawai berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport: ' . $e->getMessage());
        }
    }

    public function destroy(Pegawai $pegawai)
    {
        $user = $pegawai->user;
        $pegawai->delete();
        if ($user) {
            $user->delete();
        }
        return back()->with('success', 'Data pegawai berhasil dihapus.');
    }
}
