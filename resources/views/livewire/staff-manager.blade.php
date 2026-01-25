<?php

use Livewire\Volt\Component;
use App\Models\Staff;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $showModal = false;

    public $staffId, $name, $position, $phone, $email, $join_date;

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:staffs,email,' . $this->staffId,
            'join_date' => 'required|date',
        ];
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'position', 'phone', 'email', 'join_date', 'staffId']);

        if ($id) {
            $staff = Staff::find($id);
            $this->staffId = $staff->id;
            $this->name = $staff->name;
            $this->position = $staff->position;
            $this->phone = $staff->phone;
            $this->email = $staff->email;
            $this->join_date = $staff->join_date;
        }
        $this->showModal = true;
    }

    public function save()
    {
        $data = $this->validate();

        Staff::updateOrCreate(['id' => $this->staffId], $data);

        $this->showModal = false;
        $this->dispatch('notify', $this->staffId ? 'Data staff diperbarui!' : 'Staff baru ditambahkan!');
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
                ->paginate(1),
        ];
    }
}; ?>

<div class="p-6 bg-white min-h-screen">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-black text-primary uppercase tracking-tight">Manajemen Staff</h1>
        </div>
        <button wire:click="openModal()"
            class="bg-secondary text-primary px-5 py-2.5 rounded-xl font-bold flex items-center gap-2 hover:brightness-110 transition shadow-lg shadow-primary/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Add Staff
        </button>
    </div>

    {{-- Search --}}
    <div class="mb-6">
        <div class="relative max-w-md">
            <input type="text" wire:model.live="search" placeholder="Search name, position or contact..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border-none ring-1 ring-primary focus:ring-2 focus:ring-primary shadow-sm text-sm">
            <svg class="w-5 h-5 absolute left-3 top-3 text-secondary" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-secondary overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse ">
            <thead class="bg-secondary text-primary text-[11px] uppercase font-black tracking-wider">
                <tr>
                    <th class="p-4">Name</th>
                    <th class="p-4">Posititon</th>
                    <th class="p-4">Contact</th>
                    <th class="p-4">Join Date</th>
                    <th class="p-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm text-center">
                @forelse($staffs as $staff)
                    <tr class="hover:bg-gray-50/50 transition" wire:key="staff-{{ $staff->id }}">
                        <td class="p-4 ">
                            <div class="font-bold text-primary">{{ $staff->name }}</div>
                        </td>
                        <td class="p-4">
                            <span
                                class="px-3 py-1 bg-secondary/10 text-primary rounded-full text-2xs font-black uppercase">
                                {{ $staff->position }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-600 font-medium">{{ $staff->phone }}</td>
                        <td class="p-4 text-gray-500">{{ \Carbon\Carbon::parse($staff->join_date)->format('d M Y') }}
                        </td>
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
            </tbody>
        </table>
        <div class="p-4 bg-gray-50">
            {{ $staffs->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    {{-- MODAL --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-secondary/20 backdrop-blur-sm">
            <div
                class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-xl font-black text-secondary uppercase">{{ $staffId ? 'Edit' : 'Tambah' }} Staff
                    </h2>
                    <button wire:click="$set('showModal', false)"
                        class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>

                <form wire:submit="save" class="p-6 space-y-4">
                    <div class="space-y-1">
                        <label class="text-2xs font-black text-gray-400 uppercase">Nama Lengkap</label>
                        <input type="text" wire:model="name"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:ring-primary focus:border-primary text-sm shadow-sm">
                        @error('name')
                            <span class="text-red-500 text-2xs font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-2xs font-black text-gray-400 uppercase">Posisi / Jabatan</label>
                            <select wire:model="position"
                                class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm shadow-sm">
                                <option value="">Pilih Posisi</option>
                                <option value="Manager">Manager</option>
                                <option value="Kasir">Kasir</option>
                                <option value="Chef">Chef</option>
                                <option value="Waiter">Waiter</option>
                            </select>
                            @error('position')
                                <span class="text-red-500 text-2xs font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-2xs font-black text-gray-400 uppercase">No. Telepon</label>
                            <input type="text" wire:model="phone"
                                class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm shadow-sm">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-2xs font-black text-gray-400 uppercase">Email</label>
                        <input type="email" wire:model="email"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm shadow-sm">
                        @error('email')
                            <span class="text-red-500 text-2xs font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-2xsfont-black text-gray-400 uppercase">Tanggal Bergabung</label>
                        <input type="date" wire:model="join_date"
                            class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm shadow-sm">
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="flex-1 py-3 text-xs font-black uppercase text-gray-400 bg-gray-100 rounded-xl hover:bg-gray-200 transition">Batal</button>
                        <button type="submit"
                            class="flex-1 py-3 text-xs font-black uppercase text-white bg-primary rounded-xl shadow-lg shadow-primary/20 hover:brightness-110 transition">Simpan
                            Data</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
