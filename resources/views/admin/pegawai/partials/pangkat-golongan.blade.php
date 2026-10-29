{{-- Dropdown pangkat dan golongan PNS yang saling mengisi otomatis (gaya sama dengan filter Master Gaji) --}}
@php
    $daftar = \App\Models\Pegawai::GOLONGAN_PANGKAT;
    $golonganAwal = old('golongan', $golongan ?? '');
    $pangkatAwal = old('pangkat', $pangkat ?? '');
    // Data lama yang tidak sesuai daftar tetap ditampilkan agar tidak hilang tanpa disadari
    $golonganLama = $golonganAwal && !array_key_exists($golonganAwal, $daftar) ? $golonganAwal : null;
    $pangkatLama = $pangkatAwal && !in_array($pangkatAwal, $daftar, true) ? $pangkatAwal : null;
    $kelompok = collect($daftar)->groupBy(fn ($pkt, $gol) => strtok($gol, '/'), true);
@endphp

<div class="grid grid-cols-2 gap-4"
     x-data="{
        peta: @js($daftar),
        golongan: @js($golonganAwal),
        pangkat: @js($pangkatAwal),
        bukaGolongan: false,
        bukaPangkat: false,
        pilihGolongan(g) { this.golongan = g; if (this.peta[g]) this.pangkat = this.peta[g]; this.bukaGolongan = false; },
        pilihPangkat(p) { this.pangkat = p; const g = Object.keys(this.peta).find(k => this.peta[k] === p); if (g) this.golongan = g; this.bukaPangkat = false; },
     }">

    {{-- Pangkat --}}
    <div class="relative">
        <x-input-label value="Pangkat" />
        <input type="hidden" name="pangkat" :value="pangkat">
        <button type="button" @click="bukaPangkat = !bukaPangkat; bukaGolongan = false" @click.away="bukaPangkat = false"
            class="mt-1 w-full flex items-center justify-between text-left px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all h-[42px]">
            <span x-text="pangkat || '-- Pilih Pangkat --'" :class="!pangkat ? 'text-gray-500' : 'text-gray-900'" class="truncate pr-4"></span>
            <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="bukaPangkat ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="bukaPangkat" x-cloak
            x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="dropdown-kaca absolute z-50 mt-1 w-full rounded-lg overflow-hidden">
            <ul class="scroll-kaca max-h-60 overflow-y-auto py-1">
                <li @click="pilihPangkat('')" class="px-4 py-2 text-sm text-gray-500 hover:bg-gray-100 cursor-pointer">-- Pilih Pangkat --</li>
                @if($pangkatLama)
                    <li @click="pilihPangkat(@js($pangkatLama))" class="px-4 py-2 text-sm text-yellow-700 hover:bg-gray-100 cursor-pointer">{{ $pangkatLama }} (data lama)</li>
                @endif
                @foreach($kelompok as $ruang => $isi)
                    <li class="px-4 py-1 mt-1 text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50">Golongan {{ $ruang }}</li>
                    @foreach($isi as $gol => $pkt)
                        <li @click="pilihPangkat(@js($pkt))"
                            class="px-4 py-2 text-sm hover:bg-indigo-50 cursor-pointer border-l-2 transition-colors"
                            :class="pangkat === @js($pkt) ? 'bg-indigo-50 border-indigo-500 font-medium text-indigo-900' : 'border-transparent text-gray-700'">
                            {{ $pkt }}
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
        <x-input-error :messages="$errors->get('pangkat')" class="mt-2" />
    </div>

    {{-- Golongan --}}
    <div class="relative">
        <x-input-label value="Golongan" />
        <input type="hidden" name="golongan" :value="golongan">
        <button type="button" @click="bukaGolongan = !bukaGolongan; bukaPangkat = false" @click.away="bukaGolongan = false"
            class="mt-1 w-full flex items-center justify-between text-left px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition-all h-[42px]">
            <span x-text="golongan ? 'Golongan ' + golongan : '-- Pilih Golongan --'" :class="!golongan ? 'text-gray-500' : 'text-gray-900'" class="truncate pr-4"></span>
            <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="bukaGolongan ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="bukaGolongan" x-cloak
            x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="dropdown-kaca absolute z-50 mt-1 w-full rounded-lg overflow-hidden">
            <ul class="scroll-kaca max-h-60 overflow-y-auto py-1">
                <li @click="pilihGolongan('')" class="px-4 py-2 text-sm text-gray-500 hover:bg-gray-100 cursor-pointer">-- Pilih Golongan --</li>
                @if($golonganLama)
                    <li @click="pilihGolongan(@js($golonganLama))" class="px-4 py-2 text-sm text-yellow-700 hover:bg-gray-100 cursor-pointer">{{ $golonganLama }} (data lama)</li>
                @endif
                @foreach($kelompok as $ruang => $isi)
                    <li class="px-4 py-1 mt-1 text-xs font-bold text-gray-400 uppercase tracking-wider bg-gray-50">Golongan {{ $ruang }}</li>
                    @foreach($isi as $gol => $pkt)
                        <li @click="pilihGolongan(@js($gol))"
                            class="px-4 py-2 text-sm hover:bg-indigo-50 cursor-pointer border-l-2 transition-colors"
                            :class="golongan === @js($gol) ? 'bg-indigo-50 border-indigo-500 font-medium text-indigo-900' : 'border-transparent text-gray-700'">
                            Golongan {{ $gol }}
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
        <x-input-error :messages="$errors->get('golongan')" class="mt-2" />
    </div>
</div>
