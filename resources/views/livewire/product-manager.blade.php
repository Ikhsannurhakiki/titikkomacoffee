<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\Category;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $search = '';
    public $showModal = false;

    public $productId, $name, $category_id, $price, $stock, $image;
    public $existingImage;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => $this->productId ? 'nullable|image|max:1024' : 'required|image|max:1024',
        ];
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'category_id', 'price', 'stock', 'image', 'productId', 'existingImage']);

        if ($id) {
            $product = Product::find($id);
            $this->productId = $product->id;
            $this->name = $product->name;
            $this->category_id = $product->category_id;
            $this->price = $product->price;
            $this->stock = $product->stock;
            $this->existingImage = $product->image;
        }
        $this->showModal = true;
    }

    public function toggleAvailability($id)
    {
        $product = Product::find($id);
        $product->update([
            'is_available' => !$product->is_available,
        ]);

        $this->dispatch('notify', 'Status menu diperbarui!');
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->image) {
            $data['image'] = $this->image->store('products', 'public');
        } else {
            $data['image'] = $this->existingImage;
        }

        Product::updateOrCreate(['id' => $this->productId], $data);

        $this->showModal = false;
        $this->dispatch('notify', $this->productId ? 'Produk diperbarui!' : 'Produk ditambahkan!');
    }

    public function delete($id)
    {
        Product::find($id)->delete();
    }

    public function with()
    {
        return [
            'products' => Product::query()
                ->with('category')
                ->where('name', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(10),
            'categories' => Category::all(),
        ];
    }
}; ?>

<div class="p-6 bg-white min-h-screen">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-black text-secondary uppercase">Menu Item List</h1>
        </div>
        <button wire:click="openModal()"
            class="bg-primary text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:brightness-110 transition shadow-lg shadow-primary/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Menu Item
        </button>
    </div>

    {{-- Search Bar --}}
    <div class="mb-6">
        <div class="relative max-w-md">
            <input type="text" wire:model.live="search" placeholder="Cari nama produk..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-primary shadow-sm">
            <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-500 text-[11px] uppercase font-black tracking-wider">
                <tr>
                    <th class="p-4">Item Name</th>
                    <th class="p-4">Category</th>
                    <th class="p-4">Price</th>
                    <th class="p-4">Availablity</th>
                    <th class="p-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ($products as $product)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="p-4 flex items-center gap-3">
                            <img src="{{ $product->getFirstMediaUrl('thumbnail') ?: asset('images/logo.png') }}"
                                class="w-10 h-10 rounded-lg object-cover bg-gray-100" alt="{{ $product->name }}">
                            <span class="font-bold text-secondary text-sm">{{ $product->name }}</span>
                        </td>
                        <td class="p-4 text-sm text-gray-600">{{ $product->category->name }}</td>
                        <td class="p-4 text-sm font-bold text-primary">
                            Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="p-4">
                            {{-- Label Status --}}
                            <span
                                class="text-[10px] font-black uppercase {{ $product->is_available ? 'text-green-500' : 'text-gray-400' }}">
                                {{ $product->is_available ? 'Available' : 'Out of Stock' }}
                            </span>

                            {{-- Toggle Switch --}}
                            <button wire:click="toggleAvailability({{ $product->id }})"
                                class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $product->is_available ? 'bg-primary' : 'bg-gray-200' }}">
                                <span
                                    class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $product->is_available ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button wire:click="openModal({{ $product->id }})"
                                    class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:confirm="Hapus produk ini?" wire:click="delete({{ $product->id }})"
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MODAL (Alpine.js integration) --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div
                class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-xl font-black text-secondary uppercase">{{ $productId ? 'Edit' : 'Tambah' }} Produk
                    </h2>
                    <button wire:click="$set('showModal', false)"
                        class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <form wire:submit="save" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 space-y-1">
                            <label class="text-[10px] font-black text-gray-400 uppercase">Nama Produk</label>
                            <input type="text" wire:model="name"
                                class="w-full px-4 py-2 rounded-xl border-gray-200 focus:ring-primary focus:border-primary text-sm">
                            @error('name')
                                <span class="text-red-500 text-[10px]">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-gray-400 uppercase">Kategori</label>
                            <select wire:model="category_id"
                                class="w-full px-4 py-2 rounded-xl border-gray-200 text-sm">
                                <option value="">Pilih</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-black text-gray-400 uppercase">Harga</label>
                            <input type="number" wire:model="price"
                                class="w-full px-4 py-2 rounded-xl border-gray-200 text-sm">
                        </div>
                        <div class="space-y-1">
                            {{-- Label Status --}}
                            <span
                                class="text-[10px] font-black uppercase {{ $product->is_available ? 'text-green-500' : 'text-gray-400' }}">
                                {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                            </span>

                            {{-- Toggle Switch --}}
                            <button wire:click="toggleAvailability({{ $product->id }})"
                                class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $product->is_available ? 'bg-primary' : 'bg-gray-200' }}">
                                <span
                                    class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $product->is_available ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                        <div class="col-span-2 space-y-1">
                            <label class="text-[10px] font-black text-gray-400 uppercase">Foto Produk</label>
                            <input type="file" wire:model="image" class="text-xs">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" class="mt-2 w-20 h-20 rounded-lg object-cover">
                            @elseif($existingImage)
                                <img src="{{ asset('storage/' . $existingImage) }}"
                                    class="mt-2 w-20 h-20 rounded-lg object-cover">
                            @endif
                        </div>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="flex-1 py-3 text-sm font-bold text-gray-500 bg-gray-100 rounded-xl">Batal</button>
                        <button type="submit"
                            class="flex-1 py-3 text-sm font-bold text-white bg-primary rounded-xl shadow-lg shadow-primary/20">Simpan
                            Produk</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
