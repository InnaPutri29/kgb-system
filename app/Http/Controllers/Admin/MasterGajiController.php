<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterGaji;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterGajiController extends Controller
{
    public function index(Request $request)
    {
        $query = MasterGaji::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            
            // Bersihkan format Rupiah (contoh: "Rp 2.700.000" atau "2.700.000" -> "2700000")
            $cleanSalary = preg_replace('/[^0-9]/', '', $search);

            // Bersihkan format tahun (contoh: "3 tahun" atau "3 th" -> "3")
            $cleanMasaKerja = null;
            if (preg_match('/^(\d+)\s*(tahun|th|t)?$/i', $search, $matches)) {
                $cleanMasaKerja = $matches[1];
            }

            $query->where(function ($q) use ($search, $cleanSalary, $cleanMasaKerja) {
                $q->where('golongan', 'like', "%{$search}%");
                
                if (is_numeric($search)) {
                    $q->orWhere('masa_kerja', $search);
                } elseif ($cleanMasaKerja !== null) {
                    $q->orWhere('masa_kerja', $cleanMasaKerja);
                }

                if (!empty($cleanSalary)) {
                    $q->orWhere('nominal_gaji', 'like', "%{$cleanSalary}%");
                }
            });
        }

        if ($request->filled('kategori')) {
            $kategori = $request->kategori;
            if (str_contains($kategori, '/')) {
                $query->where('golongan', $kategori);
            } else {
                $query->where('golongan', 'like', "{$kategori}/%");
            }
        }

        $gaji = $query->orderBy('golongan')->orderBy('masa_kerja')->paginate(request('per_page', 20))->withQueryString();
        
        return view('admin.master-gaji.index', compact('gaji'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'golongan'     => ['required', 'string', 'max:5'],
            'masa_kerja'   => ['required', 'integer', 'min:0'],
            'nominal_gaji' => ['required', 'numeric', 'min:0'],
        ]);

        // Cek duplikasi manual karena ada compound unique key
        $exists = MasterGaji::where('golongan', $request->golongan)
            ->where('masa_kerja', $request->masa_kerja)
            ->exists();

        if ($exists) {
            return back()->with('error', "Data untuk Golongan {$request->golongan} dan Masa Kerja {$request->masa_kerja} tahun sudah ada.");
        }

        MasterGaji::create($request->only(['golongan', 'masa_kerja', 'nominal_gaji']));

        return back()->with('success', 'Data Master Gaji berhasil ditambahkan.');
    }

    public function update(Request $request, MasterGaji $masterGaji)
    {
        $request->validate([
            'golongan'     => ['required', 'string', 'max:5'],
            'masa_kerja'   => ['required', 'integer', 'min:0'],
            'nominal_gaji' => ['required', 'numeric', 'min:0'],
        ]);

        // Cek duplikasi kecuali id sendiri
        $exists = MasterGaji::where('golongan', $request->golongan)
            ->where('masa_kerja', $request->masa_kerja)
            ->where('id', '!=', $masterGaji->id)
            ->exists();

        if ($exists) {
            return back()->with('error', "Data untuk Golongan {$request->golongan} dan Masa Kerja {$request->masa_kerja} tahun sudah ada.");
        }

        $masterGaji->update($request->only(['golongan', 'masa_kerja', 'nominal_gaji']));

        return back()->with('success', 'Data Master Gaji berhasil diperbarui.');
    }

    public function destroy(MasterGaji $masterGaji)
    {
        $masterGaji->delete();
        return back()->with('success', 'Data Master Gaji berhasil dihapus.');
    }
}
