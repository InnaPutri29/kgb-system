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
    public function index(Request $request)
    {
        $query = Pegawai::with('user');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('golongan', 'like', "{$search}%")
                  ->orWhere('pangkat', 'like', "%{$search}%");
            });
        }
        if ($request->filled('golongan')) {
            $golongan = $request->golongan;
            if (str_contains($golongan, '/')) {
                $query->where('golongan', $golongan);
            } else {
                $query->where('golongan', 'like', $golongan . '/%');
            }
        }

        if ($request->filled('tahun_tmt')) {
            $query->whereYear('tmt_gaji_terakhir', $request->tahun_tmt);
        }

        // Ambil daftar tahun TMT dan golongan unik yang ada di database untuk dropdown
        $tahunTmtList = Pegawai::whereNotNull('tmt_gaji_terakhir')
            ->selectRaw('YEAR(tmt_gaji_terakhir) as tahun')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $golonganList = Pegawai::whereNotNull('golongan')
            ->where('golongan', '!=', '')
            ->select('golongan')
            ->groupBy('golongan')
            ->orderBy('golongan')
            ->pluck('golongan');

        $pegawai = $query->latest()->paginate(request('per_page', 20))->withQueryString();
        return view('admin.pegawai.index', compact('pegawai', 'tahunTmtList', 'golonganList'));
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
            'nip' => 'required|string|digits:18|unique:pegawai,nip',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
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
            $email = !empty($validated['email']) ? $validated['email'] : strtolower(str_replace(' ', '', $validated['nip'])) . '@kgb.rsd-sidawangi.id';
            
            // Create user account
            $user = User::create([
                'name' => $validated['nama_lengkap'],
                'nip' => $validated['nip'],
                'email' => $email,
                'password' => Hash::make(substr($validated['nip'], 0, 8)),
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
        $pegawai->load(['riwayatKgb' => fn($q) => $q->orderByDesc('tmt_baru'), 'skpEvaluasi' => fn($q) => $q->orderByDesc('tahun_penilaian')]);
        return view('admin.pegawai.show', compact('pegawai'));
    }

    public function edit(Pegawai $pegawai)
    {
        return view('admin.pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'digits:18', Rule::unique('pegawai', 'nip')->ignore($pegawai->id)],
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($pegawai->user_id)],
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
                $userUpdateData = [
                    'name' => $validated['nama_lengkap'],
                    'nip' => $validated['nip'],
                ];
                
                if (isset($validated['email'])) {
                    $userUpdateData['email'] = $validated['email'];
                }
                
                $pegawai->user->update($userUpdateData);
            }
        });

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\TemplatePegawaiExport, 'template_import_pegawai.xlsx');
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
                return back()->with('warning', "Impor selesai dengan beberapa kesalahan: {$errorMessages}");
            }

            $stats = $import->getStats();
            $message = "Impor selesai! Berhasil menambahkan {$stats['success']} data pegawai baru";
            
            $details = [];
            if ($stats['skipped'] > 0) {
                $details[] = "{$stats['skipped']} data dilewati karena sudah terdaftar";
            }
            if ($stats['invalid'] > 0) {
                $details[] = "{$stats['invalid']} data tidak valid/kosong";
            }
            
            if (count($details) > 0) {
                $message .= " (" . implode(', ', $details) . ").";
            } else {
                $message .= ".";
            }

            if ($stats['success'] === 0 && count($details) > 0) {
                return redirect()->route('admin.pegawai.index')
                    ->with('warning', $message);
            }

            // Jalankan deteksi otomatis agar notifikasi langsung muncul
            \Illuminate\Support\Facades\Artisan::call('kgb:check-due');

            return redirect()->route('admin.pegawai.index')
                ->with('success', $message);
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
