<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;

new class extends Component {
    public $search = '';
    public $selectedCategory = null;

    public function setCategory($id = null)
    {
        $this->selectedCategory = $id;
    }

    public function with()
    {
        return [
            'products' => Product::query()->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))->get(),
            'categories' => Category::all(),
        ];
    }
}; ?>


{{-- Tambahkan w-full dan pastikan overflow dikontrol di sini --}}
<div class="flex h-full w-full overflow-hidden">
    {{-- Bagian List Produk (Tengah) --}}
    {{-- flex-1 adalah kunci agar area ini menghabiskan sisa ruang --}}
    <div class="flex-1 p-6 overflow-y-auto min-w-0">
        <h1 class="text-2xl font-bold">Titik Koma .</h1>
        {{-- Grid Produk kamu --}}
    </div>

    {{-- Bagian Keranjang (Aside Kanan) --}}
    {{-- shrink-0 memastikan sidebar kanan tidak gepeng/mengecil --}}
    <aside class="w-96 bg-white border-l shrink-0 overflow-y-auto">
        @include('orders')
    </aside>
</div>
