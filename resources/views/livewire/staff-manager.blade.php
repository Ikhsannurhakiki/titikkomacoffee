<?php

use Livewire\Volt\Component;
use App\Models\Staff;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

new class extends Component {
    use WithPagination;
    use WithFileUploads;
    public $isEdit = false;
    public $search = '';
    public $showModal = false;
    public $staff;

    public $staffId, $name, $position, $phone, $pin, $join_date, $image, $existingImage;

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'position', 'phone', 'pin', 'join_date', 'staffId', 'image', 'existingImage']);

        if ($id) {
            $this->isEdit = true;
            $this->staff = Staff::find($id);
            $this->staffId = $this->staff->id;
            $this->name = $this->staff->name;
            $this->position = $this->staff->position;
            $this->phone = $this->staff->phone;
            $this->pin = $this->staff->pin;
            $this->join_date = $this->staff->join_date;

            $this->existingImage = $this->staff->getFirstMediaUrl('staff-profile');
        }
        $this->showModal = true;

        if ($id && $this->existingImage) {
            $this->dispatch('load-existing-image', image: $this->existingImage);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'pin' => ['required', 'numeric', 'digits:5', $this->staffId ? 'unique:staffs,pin,' . $this->staffId : 'unique:staffs,pin'],
            'join_date' => 'required|date',
            'image' => $this->staffId ? 'nullable|image|max:1024' : 'required|image|max:1024',
        ]);

        $data = [
            'name' => $this->name,
            'position' => $this->position,
            'phone' => $this->phone,
            'pin' => $this->pin,
            'join_date' => $this->join_date,
        ];

        $staff = $this->isEdit ? $this->staff : new Staff();
        $staff->fill($data);
        $staff->save();

        if ($this->image) {
            $staff->clearMediaCollection('staff-profile');
            $staff
                ->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('staff-profile');
        }
        $this->showModal = false;
        $this->dispatch('notify', $this->staffId ? 'Data staff diperbarui!' : 'Staff baru ditambahkan!');
        return redirect()->route('staff-manager');
    }

    public function toggleStatus($id)
    {
        $staff = Staff::find($id);
        $staff->update(['is_active' => !$staff->is_active]);
        $this->dispatch('notify', 'Status staff diperbarui!');
    }

    public function delete($id)
    {
        Staff::find($id)->delete();
        $this->dispatch('notify', 'Staff berhasil dihapus!');
    }

    public function with()
    {
        return [
            'staffs' => Staff::query()
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('position', 'like', "%{$this->search}%")
                ->latest()
                ->paginate(5),
        ];
    }
}; ?>

<div class=" bg-white min-h-screen">
    {{-- Header --}}
    <x-header title="Staff Manager">
        <x-search-bar placeholder="Search staff, position or contact..." model="search" class="w-full md:w-96" />
        <button wire:click="openModal()"
            class="bg-secondary text-white px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:brightness-110 transition shadow-lg shadow-primary/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </button>
    </x-header>

    {{-- Table --}}
    <div class="bg-white px-6 border-secondary overflow-hidden shadow-sm">
        <x-table :headers="['Name', 'Position', 'Contact', 'Join Date', 'Status', 'Action']">
            @forelse($staffs as $staff)
                <tr class="hover:bg-gray-50/50 transition text-center">
                    <td class="p-4 flex items-center gap-3 ">
                        <img src="{{ $staff->getFirstMediaUrl('staff-profile') ?: asset('images/people-icon.png') }}"
                            class="w-10 h-10 rounded-full object-cover bg-gray-100" alt="{{ $staff->name }}">
                        <span class="font-bold text-primary text-sm">{{ $staff->name }}</span>
                    </td>
                    <td class="p-4">
                        <span class="px-3 py-1 bg-secondary/10 text-primary rounded-full text-2xs font-black uppercase">
                            {{ $staff->position }}
                        </span>
                    </td>
                    <td class="font-bold text-primary text-sm">{{ $staff->phone }}</td>
                    <td class="font-bold text-primary text-sm">
                        {{ \Carbon\Carbon::parse($staff->join_date)->format('d M Y') }}
                    </td>
                    <td class="p-4">
                        {{-- Label Status --}}
                        <span
                            class="text-2xs font-black uppercase {{ $staff->is_active ? 'text-green-500' : 'text-gray-400' }}">
                            {{ $staff->is_active ? 'Active' : 'Inactive' }}
                        </span>

                        {{-- Toggle Switch --}}
                        <button wire:click="toggleStatus({{ $staff->id }})"
                            class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $staff->is_active ? 'bg-primary' : 'bg-gray-200' }}">
                            <span
                                class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $staff->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    <td class="p-4">
                        <div class="flex justify-center gap-2">
                            <button wire:click="openModal({{ $staff->id }})"
                                class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button wire:confirm="Yakin ingin menghapus staff ini?"
                                wire:click="delete({{ $staff->id }})"
                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="p-10 text-center text-gray-400 italic text-sm">Data staff belum
                        tersedia.</td>
                </tr>
            @endforelse

            <x-slot name="pagination">
                {{ $staffs->links('vendor.pagination.tailwind') }}
            </x-slot>
        </x-table>
    </div>

    {{-- MODAL --}}
    @if ($showModal)
        <x-form-modal :productId="$staffId" :name="$name" title="{{ $staffId ? 'Edit' : 'Tambah' }} Staff">
            <form wire:submit="save" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-2xs font-black text-gray-400 uppercase">Nama Lengkap</label>
                        <input type="text" wire:model="name"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:ring-primary focus:border-primary text-sm shadow-sm">
                        @error('name')
                            <span class="text-red-500 text-2xs font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-2xs font-black text-gray-400 uppercase tracking-widest">PIN Keamanan</label>
                        <input type="password" wire:model="pin" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                            placeholder="••••••"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm shadow-sm focus:ring-secondary focus:border-secondary tracking-[1em] font-bold text-center">

                        @error('pin')
                            <span class="text-red-500 text-2xs font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-2xs font-black text-gray-400 uppercase">Posisi / Jabatan</label>
                        <select wire:model="position"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm shadow-sm">
                            <option value="">Pilih Posisi</option>
                            <option value="Manager">Manager</option>
                            <option value="cashier">Cashier</option>
                            <option value="kitchen">Kitchen</option>
                            <option value="waiter">Waiter</option>
                        </select>
                        @error('position')
                            <span class="text-red-500 text-2xs font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-2xs font-black text-gray-400 uppercase">No. Telepon</label>
                        <input type="text" wire:model="phone"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm shadow-sm">
                        @error('phone')
                            <span class="text-red-500 text-2xs font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-2xsfont-black text-gray-400 uppercase">Tanggal Bergabung</label>
                    <input type="date" wire:model="join_date"
                        class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm shadow-sm">
                    @error('join_date')
                        <span class="text-red-500 text-2xs font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1" wire:ignore x-data="{
                    pond: null,
                
                    init() {
                        this.pond = FilePond.create(this.$refs.input, {
                            acceptedFileTypes: ['image/*'],
                            maxFileSize: '1MB',
                            server: {
                                process: (fieldName, file, metadata, load, error, progress) => {
                                    @this.upload('image', file, load, error, progress);
                                },
                                revert: (filename, load) => {
                                    @this.removeUpload('image', filename, load);
                                    load();
                                },
                                load: (source, load, error, progress, abort, headers) => {
                                    fetch(source).then(res => res.blob()).then(load);
                                },
                            },
                            allowImagePreview: true,
                            imagePreviewHeight: 120,
                            labelIdle: 'Drop image or <span class=\'text-secondary font-bold\'>Browse</span>',
                            credits: false,
                        });
                
                        window.addEventListener('load-existing-image', e => {
                            this.pond.removeFiles();
                            if (e.detail.image) {
                                this.pond.addFile(e.detail.image, {
                                    type: 'local'
                                });
                            }
                        });
                    }
                }">
                    <label class="text-2xs font-black text-gray-400 uppercase">Foto Profile</label>
                    <input type="file" x-ref="input">
                    @error('image')
                        <span class="text-red-500 text-2xs italic">{{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" wire:click="$set('showModal', false)"
                        class="flex-1 py-3 text-xs font-black uppercase text-gray-400 bg-gray-100 rounded-xl hover:bg-gray-200 transition">Batal</button>
                    <button type="submit"
                        class="flex-1 py-3 text-xs font-black uppercase text-white bg-primary rounded-xl shadow-lg shadow-primary/20 hover:brightness-110 transition">Simpan
                        Data</button>
                </div>
            </form>
        </x-form-modal>
    @endif
</div>
