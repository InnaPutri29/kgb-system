<div x-data="{ 
        action: '', 
        title: '', 
        description: '' 
     }"
     @confirm-delete.window="
        action = $event.detail.action; 
        title = $event.detail.title; 
        description = $event.detail.description; 
        $dispatch('open-modal', 'global-delete-modal');
     ">
    <x-modal name="global-delete-modal" maxWidth="md" focusable>
        <form method="post" :action="action">
            @csrf
            @method('delete')
            
            <div class="p-6">
                <div class="flex items-start gap-4 mb-2">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0 mt-1">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900" x-text="title">
                            Konfirmasi Hapus
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 leading-relaxed" x-text="description">
                            Apakah Anda yakin ingin menghapus data ini?
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-gray-200">
                        Batal
                    </button>

                    <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Ya, Hapus Data
                    </button>
                </div>
            </div>
        </form>
    </x-modal>
</div>
