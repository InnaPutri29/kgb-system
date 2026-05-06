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
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('golongan', 'like', "%{$search}%")
                  ->orWhere('masa_kerja', 'like', "%{$search}%")
                  ->orWhere('nominal_gaji', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $kategori = $request->kategori;
            $query->where('golongan', 'like', "{$kategori}/%");
        }

        $gaji = $query->orderBy('golongan')->orderBy('masa_kerja')->paginate(20)->withQueryString();
        
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
