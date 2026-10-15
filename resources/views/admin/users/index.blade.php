@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 drop-shadow-sm">Daftar Pengguna</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola data pengguna, role, dan email.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center gap-1.5 text-sm bg-blue-600 hover:bg-blue-700 border border-transparent text-white px-4 py-2 rounded-lg font-medium shadow-sm flex-1 sm:flex-none transition-all duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pengguna
        </a>
    </div>

    <div class="bg-white/50 backdrop-blur-3xl lg:bg-white lg:backdrop-blur-none rounded-[1.5rem] border border-blue-100 lg:border-slate-100 shadow-xl shadow-blue-500/10 lg:shadow-sm lg:shadow-black/5 overflow-hidden transition hover:shadow-2xl hover:shadow-blue-500/20 lg:hover:shadow-md lg:hover:shadow-black/10">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-blue-200/60 text-xs text-blue-900 uppercase tracking-wider border-b border-white/30">
                    <tr>
                        <th class="px-3 py-3 text-left">Nama</th>
                        <th class="px-3 py-3 text-left">Email</th>
                        <th class="px-3 py-3 text-center">Role</th>

                        <th class="px-3 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-white/40 transition">
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#0B3E6A]/10 to-[#234A9F]/20 flex items-center justify-center text-[#163375] font-bold text-xs shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-gray-700 font-medium">{{ $user->email }}</td>
                            <td class="px-3 py-3 text-center">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </td>

                            <td class="px-3 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.users.edit', ['pengguna' => $user->id]) }}" 
                                       class="p-1.5 bg-emerald-500/10 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-md transition"
                                       title="Edit Pengguna">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button type="button" 
                                            @click="$dispatch('confirm-delete', {
                                                action: '{{ route('admin.users.destroy', ['pengguna' => $user->id]) }}',
                                                title: 'Hapus Pengguna',
                                                description: 'Yakin ingin menghapus pengguna {{ $user->name }}? Aksi ini tidak dapat dibatalkan.'
                                            })"
                                            class="p-1.5 bg-red-500/10 text-red-600 hover:bg-red-600 hover:text-white rounded-md transition"
                                            title="Hapus Pengguna">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-500">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
