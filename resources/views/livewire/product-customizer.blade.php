<?php

new class extends Component {
    public $showCustomizer = false;
    public $selectedProduct = null;

    public $customForm = [
        'size' => 'Regular',
        'sugar' => 'Normal',
        'ice' => 'Normal',
        'extras' => [],
    ];

    public function openCustomizer($id)
    {
        $this->selectedProduct = Product::find($id);
        $this->customForm = [
            'size' => 'Regular',
            'sugar' => 'Normal',
            'ice' => 'Normal',
            'extras' => [],
        ];
        $this->showCustomizer = true;
    }

    public function addToCart()
    {
        $extraPrice = 0;
        if ($this->customForm['size'] === 'Large') {
            $extraPrice += 5000;
        }

        $totalPrice = $this->selectedProduct->price + $extraPrice;

        $this->dispatch('add-to-cart', product: $this->selectedProduct, options: $this->customForm, finalPrice: $totalPrice);

        $this->showCustomizer = false;
    }
}; ?>

<x-modal wire:model="showCustomizer" maxWidth="2xl">
    @if ($selectedProduct)
        <div class="flex flex-col h-[80vh]">
            <div class="p-6 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-primary">{{ $selectedProduct->name }}</h2>
                    <p class="text-sm text-gray-400">Sesuaikan pesanan pelanggan</p>
                </div>
                <button wire:click="$set('showCustomizer', false)" class="text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-8">

                <section>
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400">Pilih Ukuran</label>
                    <div class="grid grid-cols-2 gap-3 mt-3">
                        @foreach (['Regular', 'Large'] as $size)
                            <button wire:click="$set('customForm.size', '{{ $size }}')"
                                class="py-4 rounded-2xl border-2 font-bold transition-all {{ $customForm['size'] == $size ? 'border-secondary bg-secondary/10 text-secondary' : 'border-gray-100 text-gray-400' }}">
                                {{ $size }} {{ $size == 'Large' ? '(+5k)' : '' }}
                            </button>
                        @endforeach
                    </div>
                </section>

                <section>
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400">Gula (Sugar Level)</label>
                    <div class="grid grid-cols-4 gap-2 mt-3">
                        @foreach (['No', 'Less', 'Normal', 'Extra'] as $sugar)
                            <button wire:click="$set('customForm.sugar', '{{ $sugar }}')"
                                class="py-3 rounded-xl border-2 text-sm font-bold {{ $customForm['sugar'] == $sugar ? 'border-primary bg-primary text-white' : 'border-gray-100 text-gray-500' }}">
                                {{ $sugar }}
                            </button>
                        @endforeach
                    </div>
                </section>

                <section>
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400">Es (Ice Level)</label>
                    <div class="flex gap-3 mt-3">
                        @foreach (['Less', 'Normal', 'Extra'] as $ice)
                            <button wire:click="$set('customForm.ice', '{{ $ice }}')"
                                class="flex-1 py-3 rounded-xl border-2 text-sm font-bold {{ $customForm['ice'] == $ice ? 'border-primary bg-primary text-white' : 'border-gray-100 text-gray-500' }}">
                                {{ $ice }}
                            </button>
                        @endforeach
                    </div>
                </section>

            </div>

            <div class="p-6 border-t bg-gray-50 rounded-b-3xl">
                <button wire:click="addToCart"
                    class="w-full py-4 bg-primary text-white rounded-2xl font-black text-lg shadow-xl shadow-primary/30 hover:brightness-110 transition-all flex justify-between px-8">
                    <span>Tambah ke Keranjang</span>
                    <span>Rp
                        {{ number_format($selectedProduct->price + ($customForm['size'] == 'Large' ? 5000 : 0), 0, ',', '.') }}</span>
                </button>
            </div>
        </div>
    @endif
</x-modal>
