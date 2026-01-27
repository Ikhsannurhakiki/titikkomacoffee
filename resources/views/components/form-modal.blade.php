@props(['id' => '', 'name' => '', 'title' => ''])

<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-secondary/20 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-xl font-black text-secondary uppercase">{{ $title }}</h2>
            <button wire:click="$set('showModal', false)"
                class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        {{ $slot }}
    </div>
</div>
