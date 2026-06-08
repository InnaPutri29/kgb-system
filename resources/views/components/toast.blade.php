<div x-data="{ 
        show: false, 
        message: '', 
        type: 'success',
        init() {
            window.addEventListener('show-toast', (e) => {
                this.message = e.detail.message;
                this.type = e.detail.type || 'success';
                this.show = true;
                setTimeout(() => { this.show = false }, 4000);
            });
            
            @if(session('success'))
                this.message = '{{ session('success') }}';
                this.type = 'success';
                this.show = true;
                setTimeout(() => { this.show = false }, 4000);
            @endif

            @if(session('error'))
                this.message = '{{ session('error') }}';
                this.type = 'error';
                this.show = true;
                setTimeout(() => { this.show = false }, 4000);
            @endif

            @if(session('warning'))
                this.message = '{{ session('warning') }}';
                this.type = 'warning';
                this.show = true;
                setTimeout(() => { this.show = false }, 4000);
            @endif
        }
     }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
     x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 sm:translate-x-0"
     x-transition:leave-end="opacity-0 sm:translate-x-4"
     x-cloak
     class="fixed bottom-6 right-6 z-[100] max-w-sm w-full bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 p-4 flex items-start gap-4 overflow-hidden"
     :class="{
         'ring-1 ring-green-500/50': type === 'success',
         'ring-1 ring-yellow-500/50': type === 'warning',
         'ring-1 ring-red-500/50': type === 'error',
         'ring-1 ring-blue-500/50': type === 'info'
     }">
     
    <!-- Decorator bar -->
    <div class="absolute left-0 top-0 bottom-0 w-1.5"
         :class="{
             'bg-green-500': type === 'success',
             'bg-yellow-500': type === 'warning',
             'bg-red-500': type === 'error',
             'bg-blue-500': type === 'info'
         }"></div>
    
    <!-- Icon -->
    <div class="shrink-0 ml-1 mt-0.5">
        <template x-if="type === 'success'">
            <div class="w-9 h-9 rounded-full bg-green-100/80 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
        </template>
        <template x-if="type === 'warning'">
            <div class="w-9 h-9 rounded-full bg-yellow-100/80 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </template>
        <template x-if="type === 'error'">
            <div class="w-9 h-9 rounded-full bg-red-100/80 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
        </template>
        <template x-if="type === 'info'">
            <div class="w-9 h-9 rounded-full bg-blue-100/80 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </template>
    </div>

    <div class="flex-1 min-w-0 py-0.5">
        <p class="text-sm font-bold text-gray-900" x-text="type === 'success' ? 'Berhasil!' : (type === 'warning' ? 'Perhatian!' : (type === 'error' ? 'Gagal!' : 'Informasi'))"></p>
        <p class="text-sm text-gray-600 mt-0.5 leading-relaxed" x-text="message"></p>
    </div>

    <button @click="show = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1.5 rounded-xl transition shrink-0 -mr-1 -mt-1 focus:outline-none focus:ring-2 focus:ring-gray-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
</div>
