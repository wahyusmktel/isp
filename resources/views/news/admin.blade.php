@extends('layouts.app')
@section('title', 'Manajemen Berita')
@section('page-title', 'Manajemen Berita')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Manajemen Berita</h1>
        <p class="text-sm text-gray-400 mt-0.5">Kelola artikel, pengumuman, dan informasi yang ditampilkan di website</p>
    </div>
    <button onclick="openCreate()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-lg shadow-blue-500/20 self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Tulis Artikel
    </button>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
        </div>
        <div><p class="text-2xl font-extrabold text-gray-900">{{ $stats['total'] }}</p><p class="text-xs text-gray-500">Total Artikel</p></div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div><p class="text-2xl font-extrabold text-gray-900">{{ $stats['published'] }}</p><p class="text-xs text-gray-500">Dipublikasikan</p></div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <div><p class="text-2xl font-extrabold text-gray-900">{{ $stats['draft'] }}</p><p class="text-xs text-gray-500">Draft</p></div>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <div><p class="text-2xl font-extrabold text-gray-900">{{ $stats['month'] }}</p><p class="text-xs text-gray-500">Bulan Ini</p></div>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white rounded-2xl border border-gray-100 px-4 py-3 mb-4">
    <form method="GET" action="{{ route('news.admin') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
        <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-72">
            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul atau penulis..." class="bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none flex-1">
        </div>
        <div class="flex items-center gap-2 ml-auto flex-wrap">
            <select name="status" onchange="this.form.submit()" class="inp text-xs py-2 px-3">
                <option value="">Semua Status</option>
                <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            @if($search || $status)
            <a href="{{ route('news.admin') }}" class="text-xs text-gray-500 hover:text-gray-700 px-2 py-2 rounded-lg hover:bg-gray-100 transition-colors">Reset</a>
            @endif
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold px-3 py-2 rounded-xl">Cari</button>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-5 py-3">Artikel</th>
                    <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Kategori</th>
                    <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Status</th>
                    <th class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Views</th>
                    <th class="text-right text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden lg:table-cell">Komentar</th>
                    <th class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3 hidden md:table-cell">Tanggal</th>
                    <th class="text-center text-xs font-semibold text-gray-400 uppercase tracking-wider px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($news as $item)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            @if($item->cover_image_url)
                            <img src="{{ $item->cover_image_url }}" class="w-14 h-10 rounded-lg object-cover flex-shrink-0 hidden sm:block" alt="">
                            @else
                            <div class="w-14 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 hidden sm:block">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            </div>
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900 text-sm leading-snug line-clamp-2 max-w-xs">{{ $item->title }}</p>
                                <p class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $item->author }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-center hidden md:table-cell">
                        @php
                            $cc = $item->category_color;
                            $cmap = ['blue'=>'bg-blue-100 text-blue-700','red'=>'bg-red-100 text-red-700','amber'=>'bg-amber-100 text-amber-700','green'=>'bg-green-100 text-green-700','gray'=>'bg-gray-100 text-gray-600'];
                        @endphp
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $cmap[$cc] ?? 'bg-gray-100 text-gray-600' }}">{{ $item->category_label }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        @if($item->status === 'published')
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-green-100 text-green-700">Tayang</span>
                        @else
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700">Draft</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-right hidden lg:table-cell">
                        <span class="text-sm font-semibold text-gray-700">{{ number_format($item->view_count) }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-right hidden lg:table-cell">
                        <span class="text-sm font-semibold text-gray-700">{{ $item->comments_count }}</span>
                    </td>
                    <td class="px-4 py-3.5 hidden md:table-cell">
                        <p class="text-xs text-gray-600">{{ $item->published_at ? $item->published_at->format('d M Y') : ($item->created_at->format('d M Y')) }}</p>
                        <p class="text-[10px] text-gray-400">{{ $item->published_at ? $item->published_at->format('H:i') : 'Draft' }}</p>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            @if($item->status === 'published')
                            <a href="{{ route('news.show', $item->slug) }}" target="_blank"
                               class="w-7 h-7 rounded-lg bg-green-50 hover:bg-green-100 text-green-600 flex items-center justify-center transition-colors" title="Lihat">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            @endif
                            <button onclick="openEdit({{ json_encode(['id'=>$item->id,'title'=>$item->title,'category'=>$item->category,'status'=>$item->status,'author'=>$item->author,'excerpt'=>$item->excerpt,'body'=>$item->body,'meta_title'=>$item->meta_title,'meta_description'=>$item->meta_description,'published_at'=>$item->published_at?->format('Y-m-d\TH:i')]) }})"
                                    class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition-colors" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button onclick="deleteNews({{ $item->id }}, '{{ addslashes($item->title) }}')"
                                    class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors" title="Hapus">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">
                                <svg class="w-7 h-7 text-blue-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada artikel. Klik "Tulis Artikel" untuk memulai.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($news->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $news->links() }}</div>
    @endif
</div>

{{-- Create / Edit Slide-in Modal --}}
<div id="modal-news" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="absolute right-0 top-0 h-full w-full sm:max-w-2xl bg-white shadow-2xl flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <h3 id="modal-title" class="text-base font-bold text-gray-900">Tulis Artikel Baru</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-xl hover:bg-gray-100 flex items-center justify-center text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto">
            <form id="news-form" onsubmit="submitNews(event)" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" id="n-id">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Judul Artikel <span class="text-red-500">*</span></label>
                    <input type="text" id="n-title" required class="inp w-full" placeholder="Judul berita atau pengumuman">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                        <select id="n-category" required class="inp w-full">
                            <option value="pengumuman">Pengumuman</option>
                            <option value="gangguan">Gangguan</option>
                            <option value="promo">Promo</option>
                            <option value="tips">Tips & Trik</option>
                            <option value="umum" selected>Umum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-500">*</span></label>
                        <select id="n-status" required class="inp w-full">
                            <option value="draft">Draft</option>
                            <option value="published">Publikasikan</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Penulis <span class="text-red-500">*</span></label>
                        <input type="text" id="n-author" required class="inp w-full" value="Tim-7 Net" placeholder="Nama penulis">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Publikasi</label>
                        <input type="datetime-local" id="n-published-at" class="inp w-full">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Ringkasan / Excerpt</label>
                    <textarea id="n-excerpt" rows="2" class="inp w-full resize-none" placeholder="Ringkasan singkat (maks. 500 karakter, digunakan untuk SEO meta description)" maxlength="500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Isi Artikel <span class="text-red-500">*</span></label>
                    <textarea id="n-body" required rows="10" class="inp w-full resize-y" placeholder="Tulis isi artikel di sini..."></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Foto Cover</label>
                    <input type="file" id="n-cover" accept="image/jpg,image/jpeg,image/png,image/webp" class="inp w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-[10px] text-gray-400 mt-1">JPG/PNG/WebP, maks 3MB. Ukuran ideal: 1200×630px</p>
                    <div id="cover-preview" class="hidden mt-2">
                        <img id="cover-preview-img" class="h-28 rounded-xl object-cover border border-gray-200" src="" alt="">
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">SEO (Opsional)</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Meta Title</label>
                            <input type="text" id="n-meta-title" class="inp w-full" placeholder="Judul untuk mesin pencari (maks. 60 karakter)" maxlength="255">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Meta Description</label>
                            <textarea id="n-meta-desc" rows="2" class="inp w-full resize-none" placeholder="Deskripsi untuk mesin pencari (maks. 155 karakter)" maxlength="500"></textarea>
                        </div>
                    </div>
                </div>
                <div id="modal-error" class="hidden bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-600"></div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-xl transition-colors text-sm">Batal</button>
                    <button type="submit" id="btn-save" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 rounded-xl transition-colors text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast" class="fixed bottom-5 right-5 z-[999] hidden">
    <div id="toast-inner" class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm">
        <span id="toast-msg"></span>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function showToast(msg, type = 'success') {
    const el = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    document.getElementById('toast-inner').className = `flex items-center gap-3 px-4 py-3 rounded-2xl shadow-lg text-sm font-medium text-white min-w-[240px] max-w-sm ${type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
    el.classList.remove('hidden');
    setTimeout(() => el.classList.add('hidden'), 3500);
}

function openCreate() {
    document.getElementById('modal-title').textContent = 'Tulis Artikel Baru';
    document.getElementById('news-form').reset();
    document.getElementById('n-id').value = '';
    document.getElementById('modal-error').classList.add('hidden');
    document.getElementById('cover-preview').classList.add('hidden');
    document.getElementById('modal-news').classList.remove('hidden');
}

function openEdit(d) {
    document.getElementById('modal-title').textContent = 'Edit Artikel';
    document.getElementById('n-id').value          = d.id;
    document.getElementById('n-title').value       = d.title;
    document.getElementById('n-category').value    = d.category;
    document.getElementById('n-status').value      = d.status;
    document.getElementById('n-author').value      = d.author || 'Tim-7 Net';
    document.getElementById('n-excerpt').value     = d.excerpt || '';
    document.getElementById('n-body').value        = d.body;
    document.getElementById('n-meta-title').value  = d.meta_title || '';
    document.getElementById('n-meta-desc').value   = d.meta_description || '';
    document.getElementById('n-published-at').value = d.published_at || '';
    document.getElementById('n-cover').value       = '';
    document.getElementById('cover-preview').classList.add('hidden');
    document.getElementById('modal-error').classList.add('hidden');
    document.getElementById('modal-news').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal-news').classList.add('hidden');
}

// Cover preview
document.getElementById('n-cover').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('cover-preview-img').src = e.target.result;
        document.getElementById('cover-preview').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
});

async function submitNews(e) {
    e.preventDefault();
    const id  = document.getElementById('n-id').value;
    const btn = document.getElementById('btn-save');
    const err = document.getElementById('modal-error');
    btn.disabled = true;
    btn.textContent = 'Menyimpan...';
    err.classList.add('hidden');

    const fd = new FormData();
    fd.append('title',            document.getElementById('n-title').value);
    fd.append('category',         document.getElementById('n-category').value);
    fd.append('status',           document.getElementById('n-status').value);
    fd.append('author',           document.getElementById('n-author').value);
    fd.append('excerpt',          document.getElementById('n-excerpt').value);
    fd.append('body',             document.getElementById('n-body').value);
    fd.append('meta_title',       document.getElementById('n-meta-title').value);
    fd.append('meta_description', document.getElementById('n-meta-desc').value);
    fd.append('published_at',     document.getElementById('n-published-at').value);
    const coverFile = document.getElementById('n-cover').files[0];
    if (coverFile) fd.append('cover_image', coverFile);
    if (id) fd.append('_method', 'POST');

    const url = id ? `/news/${id}` : '/news';

    try {
        const res  = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: fd });
        const data = await res.json();
        if (!data.success) {
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Terjadi kesalahan.');
            err.textContent = msg;
            err.classList.remove('hidden');
            return;
        }
        showToast(data.message);
        closeModal();
        setTimeout(() => location.reload(), 700);
    } catch (ex) {
        err.textContent = 'Gagal: ' + ex.message;
        err.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Simpan';
    }
}

async function deleteNews(id, title) {
    if (!confirm(`Hapus artikel "${title}"? Tindakan ini tidak dapat dibatalkan.`)) return;
    try {
        const res  = await fetch(`/news/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
        const data = await res.json();
        if (!data.success) { showToast(data.message, 'error'); return; }
        showToast(data.message);
        setTimeout(() => location.reload(), 700);
    } catch (ex) {
        showToast('Gagal: ' + ex.message, 'error');
    }
}
</script>
@endsection
