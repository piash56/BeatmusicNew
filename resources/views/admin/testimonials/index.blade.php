@extends('layouts.admin')

@section('title', 'Testimonials')
@section('page-title', 'Testimonials')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-gray-400 text-sm">{{ $testimonials->total() }} total testimonials</p>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">
            Add Testimonial
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-900/30 border border-green-500/30 text-green-300 rounded-xl p-3 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($testimonials as $t)
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-5 flex flex-col space-y-3">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    @if($t->has_profile_image)
                        <img src="{{ $t->profile_picture_url }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($t->customer_name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="text-white font-medium text-sm">{{ $t->customer_name }}</p>
                        <p class="text-gray-400 text-xs">{{ $t->title }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-1 text-yellow-400 text-xs">
                    @for($i = 0; $i < ($t->rating ?? 5); $i++)★@endfor
                </div>
            </div>
            <p class="text-gray-300 text-sm leading-relaxed flex-1">"{{ $t->feedback }}"</p>
            <div class="flex items-center justify-between pt-2 border-t border-white/5">
                <span class="text-xs px-2 py-0.5 rounded-full {{ $t->status === 'active' ? 'bg-green-600/20 text-green-300' : 'bg-white/5 text-gray-400' }}">
                    {{ $t->status === 'active' ? 'Active' : 'Inactive' }}
                </span>
                <div class="flex items-center space-x-2">
                    <button type="button"
                        class="text-xs text-blue-400 hover:text-blue-300 transition"
                        data-id="{{ $t->id }}"
                        data-name="{{ e($t->customer_name) }}"
                        data-role="{{ e($t->title) }}"
                        data-feedback="{{ e($t->feedback) }}"
                        data-rating="{{ $t->rating ?? 5 }}"
                        data-status="{{ $t->status }}"
                        data-display-on='@json($t->display_on ?? [])'
                        onclick="handleEditClick(this)">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('admin.testimonials.destroy', $t->id) }}" class="inline" onsubmit="return false;">
                        @csrf @method('DELETE')
                        <button type="button"
                            class="text-xs text-red-400 hover:text-red-300 transition"
                            onclick="openDeleteModal(this.form)">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 bg-gray-900 rounded-2xl border border-white/5 p-12 text-center">
            <p class="text-gray-400">No testimonials yet.</p>
        </div>
        @endforelse
    </div>

    <div>{{ $testimonials->links() }}</div>
</div>

{{-- Add Modal --}}
<div id="addModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-white/10 rounded-2xl w-full max-w-lg p-6">
        <h2 class="text-white font-semibold mb-5">Add Testimonial</h2>
        <form method="POST"
              action="{{ route('admin.testimonials.store') }}"
              enctype="multipart/form-data"
              class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Customer Name</label>
                    <input type="text" name="customer_name" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Title / Role</label>
                    <input type="text" name="title" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
            </div>
            <div>
                <label class="text-xs text-gray-400 mb-1 block">Feedback</label>
                <textarea name="feedback" rows="4" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500 resize-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Rating (1–5)</label>
                    <select name="rating" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl text-sm focus:outline-none">
                        @for($i=5;$i>=1;$i--)<option value="{{ $i }}" {{ $i==5?'selected':'' }}>{{ $i }} stars</option>@endfor
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Status</label>
                    <select name="status" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl text-sm focus:outline-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Profile Picture (optional)</label>
                    <input type="file" name="profile_picture" accept="image/jpeg,image/png" class="w-full text-xs text-gray-300 file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:text-xs file:bg-purple-600 file:text-white hover:file:bg-purple-700 bg-gray-800 border border-white/10 rounded-xl">
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Show On</label>
                    <div class="flex flex-col space-y-1 text-xs text-gray-300">
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" name="display_on[]" value="home" class="rounded border-white/20 bg-gray-800 text-purple-500">
                            <span>Home page</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" name="display_on[]" value="about" class="rounded border-white/20 bg-gray-800 text-purple-500">
                            <span>About page</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')"
                    class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-sm rounded-xl border border-white/10 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Add</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-white/10 rounded-2xl w-full max-w-lg p-6">
        <h2 class="text-white font-semibold mb-5">Edit Testimonial</h2>
        <form method="POST" id="editForm" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Customer Name</label>
                    <input type="text" name="customer_name" id="editName" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Title / Role</label>
                    <input type="text" name="title" id="editRole" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
            </div>
            <div>
                <label class="text-xs text-gray-400 mb-1 block">Feedback</label>
                <textarea name="feedback" id="editContent" rows="4" required class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl text-sm focus:outline-none focus:border-purple-500 resize-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Rating</label>
                    <select name="rating" id="editRating" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl text-sm focus:outline-none">
                        @for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} stars</option>@endfor
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Status</label>
                    <select name="status" id="editStatus" class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl text-sm focus:outline-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Profile Picture (optional)</label>
                    <input type="file" name="profile_picture" accept="image/jpeg,image/png" class="w-full text-xs text-gray-300 file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:text-xs file:bg-purple-600 file:text-white hover:file:bg-purple-700 bg-gray-800 border border-white/10 rounded-xl">
                    <p class="text-[11px] text-gray-500 mt-1">Uploading a new image will replace the existing one.</p>
                </div>
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Show On</label>
                    <div class="flex flex-col space-y-1 text-xs text-gray-300">
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" name="display_on[]" value="home" id="editShowHome" class="rounded border-white/20 bg-gray-800 text-purple-500">
                            <span>Home page</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" name="display_on[]" value="about" id="editShowAbout" class="rounded border-white/20 bg-gray-800 text-purple-500">
                            <span>About page</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                    class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-sm rounded-xl border border-white/10 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirm Modal --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 border border-white/10 rounded-2xl w-full max-w-sm p-6">
        <h2 class="text-white font-semibold mb-3">Delete testimonial?</h2>
        <p class="text-gray-400 text-sm mb-5">This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
            <button type="button"
                    onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-300 text-sm rounded-xl border border-white/10 transition">
                Cancel
            </button>
            <button type="button"
                    onclick="confirmDelete()"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-xl transition">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
let deleteTargetForm = null;

function handleEditClick(button) {
    const dataset = button.dataset;
    const displayOn = dataset.displayOn ? JSON.parse(dataset.displayOn) : [];
    openEdit(
        dataset.id,
        dataset.name || '',
        dataset.role || '',
        dataset.feedback || '',
        parseInt(dataset.rating || '5', 10),
        dataset.status || 'active',
        displayOn
    );
}

function openEdit(id, name, role, content, rating, status, displayOn) {
    document.getElementById('editForm').action = '/admin/testimonials/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editRole').value = role;
    document.getElementById('editContent').value = content;
    document.getElementById('editRating').value = rating;
    document.getElementById('editStatus').value = status;

    var showHome = document.getElementById('editShowHome');
    var showAbout = document.getElementById('editShowAbout');
    if (Array.isArray(displayOn)) {
        showHome.checked = displayOn.includes('home');
        showAbout.checked = displayOn.includes('about');
    } else {
        showHome.checked = false;
        showAbout.checked = false;
    }

    document.getElementById('editModal').classList.remove('hidden');
}

function openDeleteModal(form) {
    deleteTargetForm = form;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    deleteTargetForm = null;
    document.getElementById('deleteModal').classList.add('hidden');
}

function confirmDelete() {
    if (deleteTargetForm) {
        deleteTargetForm.submit();
    }
    closeDeleteModal();
}
</script>
@endsection
