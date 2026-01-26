<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $product;
    public $isEdit = false;

    public $name = '';
    public $slug = '';
    public $price = '';
    public $category_id = '';
    public $description = '';
    public $is_active = true;
    public $thumbnail;=
    public $existingThumbnail;

    public function mount($id = null)
    {
        if ($id) {
            $this->isEdit = true;
            $this->product = Product::findOrFail($id);
            $this->name = $this->product->name;
            $this->slug = $this->product->slug;
            $this->price = $this->product->price;
            $this->category_id = $this->product->category_id;
            $this->description = $this->product->description;
            $this->is_active = $this->product->is_active;
            $this->existingThumbnail = $this->product->getFirstMediaUrl('thumbnail');
        }
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'slug' => 'required|unique:products,slug,' . ($this->isEdit ? $this->product->id : 'NULL'),
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|max:1000',
            'thumbnail' => $this->isEdit ? 'nullable|image|max:2048' : 'required|image|max:2048',
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        $product = $this->isEdit ? $this->product : new Product();
        $product->fill($data);
        $product->save();

        if ($this->thumbnail) {
            $product
                ->addMedia($this->thumbnail->getRealPath())
                ->usingFileName($this->thumbnail->getClientOriginalName())
                ->toMediaCollection('thumbnail');
        }

        session()->flash('success', 'Produk berhasil disimpan.');
        return $this->redirectRoute('pos', navigate: true);
    }

    public function with()
    {
        return ['categories' => Category::orderBy('name')->get()];
    }
}; ?>

<div class="max-w-4xl mx-auto p-4 sm:p-6" x-data="{
    initFilePond() {
        FilePond.registerPlugin(FilePondPluginImagePreview);
        FilePond.create($refs.input, {
            server: {
                process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                    @this.upload('thumbnail', file, load, error, progress)
                },
                revert: (uniqueFileId, load, error) => {
                    @this.removeUpload('thumbnail', uniqueFileId, load)
                },
            },
            labelIdle: '<span class=&quot;text-2xs font-black uppercase text-gray-400&quot;>Tarik foto atau <span class=&quot;text-orange-500&quot;>Klik</span></span>',
            imagePreviewHeight: 150,
            stylePanelLayout: 'compact',
        });
    }
}" x-init="initFilePond()">

    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden flex flex-col"
        style="max-height: calc(100vh - 120px);">

        {{-- FIXED HEADER --}}
        <div class="p-6 border-b border-gray-50 flex items-center justify-between shrink-0 bg-white z-10">
            <div class="flex items-center gap-4">
                <div
                    class="w-10 h-10 {{ $isEdit ? 'bg-blue-50 text-blue-500' : 'bg-orange-50 text-orange-500' }} rounded-2xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-gray-800 tracking-tight">
                        {{ $isEdit ? 'Edit Menu' : 'Tambah Menu' }}</h2>
                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest italic">Product Management
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-gray-50 px-3 py-2 rounded-xl border border-gray-100">
                <span class="text-[9px] font-black uppercase {{ $is_active ? 'text-green-500' : 'text-gray-400' }}">
                    {{ $is_active ? 'Active' : 'Inactive' }}
                </span>
                <label class="relative inline-flex items-center cursor-pointer scale-90">
                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                    <div
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500 shadow-inner">
                    </div>
                </label>
            </div>
        </div>

        {{-- SCROLLABLE CONTENT --}}
        <div class="p-8 overflow-y-auto custom-scrollbar flex-1">
            <form id="productForm" wire:submit.prevent="save" class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                {{-- Data Inputs --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-2xs font-black text-gray-400 uppercase tracking-widest ml-1">Nama
                                Produk</label>
                            <input type="text" wire:model.live="name"
                                class="w-full bg-gray-50 border-none rounded-xl py-3 px-5 focus:ring-2 focus:ring-orange-500/20 font-bold text-gray-700 transition-all">
                            @error('name')
                                <p class="text-red-500 text-[8px] font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-2xs font-black text-gray-400 uppercase tracking-widest ml-1">Slug</label>
                            <input type="text" wire:model="slug"
                                class="w-full bg-gray-100 border-none rounded-xl py-3 px-5 font-bold text-gray-500"
                                readonly>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label
                                class="text-2xs font-black text-gray-400 uppercase tracking-widest ml-1">Kategori</label>
                            <select wire:model="category_id"
                                class="w-full bg-gray-50 border-none rounded-xl py-3 px-5 focus:ring-2 focus:ring-orange-500/20 font-bold text-gray-700 appearance-none">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-2xs font-black text-gray-400 uppercase tracking-widest ml-1">Harga
                                (Rp)</label>
                            <input type="number" wire:model="price"
                                class="w-full bg-gray-50 border-none rounded-xl py-3 px-5 focus:ring-2 focus:ring-orange-500/20 font-bold text-gray-700 transition-all">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label
                            class="text-2xs font-black text-gray-400 uppercase tracking-widest ml-1">Deskripsi</label>
                        <textarea wire:model="description" rows="5"
                            class="w-full bg-gray-50 border-none rounded-xl py-3 px-5 focus:ring-2 focus:ring-orange-500/20 font-bold text-gray-700 resize-none transition-all"></textarea>
                    </div>
                </div>

                {{-- Image Upload --}}
                <div class="lg:col-span-5 space-y-4">
                    <label class="text-2xs font-black text-gray-400 uppercase tracking-widest text-center block">Media
                        Visual</label>

                    @if ($isEdit && !$thumbnail && $existingThumbnail)
                        <div class="mx-auto w-28 h-28 rounded-2xl overflow-hidden shadow-lg border-4 border-white mb-4">
                            <img src="{{ $existingThumbnail }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div wire:ignore class="rounded-2xl overflow-hidden border-2 border-dashed border-gray-100">
                        <input type="file" x-ref="input">
                    </div>
                    @error('thumbnail')
                        <p class="text-red-500 text-[8px] font-bold text-center mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        </div>

        {{-- FIXED FOOTER --}}
        <div class="p-6 border-t border-gray-50 flex items-center justify-between shrink-0 bg-white">
            <button type="button" onclick="history.back()"
                class="text-2xs font-black text-gray-300 uppercase tracking-[0.2em] hover:text-gray-600 transition-colors">Batal</button>
            <button type="submit" form="productForm" wire:loading.attr="disabled" style="background-color: #b58d69;"
                class="px-12 py-4 text-white text-[11px] font-black rounded-2xl shadow-xl active:scale-95 transition-all">
                <span wire:loading.remove>{{ $isEdit ? 'PERBARUI MENU' : 'SIMPAN MENU' }}</span>
                <span wire:loading>Uploading...</span>
            </button>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #fff;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #f3f4f6;
            border-radius: 10px;
        }
    </style>
</div>
