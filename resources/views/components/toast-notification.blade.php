<div x-data="{
    notifications: [],
    add(message, type = 'success') {
        if (!message) return;
        const id = Date.now() + Math.random();
        this.notifications.push({ id, message, type });
        setTimeout(() => this.remove(id), 4000);
    },
    remove(id) {
        this.notifications = this.notifications.filter(n => n.id !== id);
    }
}" 
x-on:notify.window="add($event.detail.message || $event.detail, $event.detail.type || 'success')"
@if (session()->has('message'))
    x-init="add('{{ addslashes(session('message')) }}', 'success')"
@endif
@if (session()->has('success'))
    x-init="add('{{ addslashes(session('success')) }}', 'success')"
@endif
@if (session()->has('error'))
    x-init="add('{{ addslashes(session('error')) }}', 'error')"
@endif
@if (session()->has('warning'))
    x-init="add('{{ addslashes(session('warning')) }}', 'warning')"
@endif
class="fixed top-6 right-6 z-[9999] space-y-3 max-w-sm w-full pointer-events-none pr-4 sm:pr-0">
    <template x-for="item in notifications" :key="item.id">
        <div x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
             class="pointer-events-auto p-4 rounded-2xl shadow-xl border flex items-center justify-between gap-3 text-xs sm:text-sm font-semibold"
             :class="{
                 'border-emerald-200 text-emerald-900 bg-emerald-50': item.type === 'success',
                 'border-rose-200 text-rose-900 bg-rose-50': item.type === 'error',
                 'border-amber-200 text-amber-900 bg-amber-50': item.type === 'warning'
             }">
            <div class="flex items-center gap-3 min-w-0">
                <span class="flex-shrink-0 text-base" x-text="item.type === 'success' ? '✓' : (item.type === 'error' ? '!' : 'i')"></span>
                <span class="break-words leading-snug" x-text="item.message"></span>
            </div>
            <button type="button" 
                    @click="remove(item.id)" 
                    class="flex-shrink-0 text-ink-500 hover:text-ink-900 p-1 rounded-lg hover:bg-black/5 transition focus:outline-none focus:ring-2 focus:ring-blush-400"
                    aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </template>
</div>

