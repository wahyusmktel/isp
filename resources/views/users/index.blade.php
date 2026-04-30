@extends('layouts.app')

@section('title', 'Kelola Pengguna - Tim-7 Net')
@section('page-title', 'Kelola Pengguna')

@section('content')
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-users-cog text-sky-500"></i> Kelola Pengguna
            </h1>
            <p class="text-sm text-gray-500 mt-1">Manajemen akun admin dan operator sistem.</p>
        </div>
        <button onclick="openModal('add')" class="btn-primary flex items-center gap-2 text-sm">
            <i class="fas fa-plus"></i> Tambah Pengguna
        </button>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-5 font-semibold">Nama & Email</th>
                        <th class="py-3 px-5 font-semibold">Role</th>
                        <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-bold">
                                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                @if($user->role === 'admin')
                                    <span class="bg-purple-100 text-purple-700 text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Admin</span>
                                @else
                                    <span class="bg-sky-100 text-sky-700 text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Operator</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                <button onclick="openModal('edit', {{ json_encode($user) }})" class="p-2 text-gray-400 hover:text-indigo-600 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-gray-500">
                                <i class="fas fa-users-slash text-4xl mb-3 text-gray-300"></i>
                                <p>Belum ada data pengguna.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    <div id="userModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop" onclick="closeModal()"></div>
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md transform transition-all scale-95 opacity-0" id="modalContent">
                <form id="userForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Tambah Pengguna</h3>
                        <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nama Lengkap</label>
                            <input type="text" name="name" id="userName" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Email</label>
                            <input type="email" name="email" id="userEmail" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Role</label>
                            <select name="role" id="userRole" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all">
                                <option value="admin">Admin</option>
                                <option value="operator">Operator</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Password <span id="passwordHint" class="hidden text-gray-400 normal-case font-normal">(Kosongkan jika tidak ingin mengubah)</span></label>
                            <input type="password" name="password" id="userPassword" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="p-6 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                        <button type="submit" class="bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 text-sm font-semibold rounded-xl shadow-lg shadow-sky-500/30 transition-all flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openModal(mode, data = null) {
        const modal = document.getElementById('userModal');
        const backdrop = document.getElementById('modalBackdrop');
        const content = document.getElementById('modalContent');
        const form = document.getElementById('userForm');
        
        modal.classList.remove('hidden');
        
        // Setup form based on mode
        if (mode === 'edit') {
            document.getElementById('modalTitle').textContent = 'Edit Pengguna';
            form.action = `/users/${data.id}`;
            document.getElementById('formMethod').value = 'PUT';
            
            document.getElementById('userName').value = data.name;
            document.getElementById('userEmail').value = data.email;
            document.getElementById('userRole').value = data.role;
            
            document.getElementById('userPassword').required = false;
            document.getElementById('passwordHint').classList.remove('hidden');
        } else {
            document.getElementById('modalTitle').textContent = 'Tambah Pengguna';
            form.action = `{{ route('users.store') }}`;
            document.getElementById('formMethod').value = 'POST';
            
            form.reset();
            document.getElementById('userPassword').required = true;
            document.getElementById('passwordHint').classList.add('hidden');
        }

        // Animation
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            content.classList.remove('opacity-0', 'scale-95');
            content.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('userModal');
        const backdrop = document.getElementById('modalBackdrop');
        const content = document.getElementById('modalContent');
        
        backdrop.classList.add('opacity-0');
        content.classList.remove('opacity-100', 'scale-100');
        content.classList.add('opacity-0', 'scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endpush
