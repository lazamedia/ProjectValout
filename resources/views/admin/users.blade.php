@extends('layouts.main')

@section('content')

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-user.css') }}">

<!-- Popup Edit User -->
<div class="popup-overlay" id="popupOverlay">
    <div class="popup-content">
        <span class="popup-close" onclick="hidePopup()">×</span>
        <h4>Edit User</h4>
        <form id="edit-user-form">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit-user-id" name="id">
            <div class="form-group mb-3">
                <label for="edit-nama">Nama</label>
                <input type="text" id="edit-nama" name="nama" class="form-control" required value="">
            </div>
            <div class="form-group mb-3">
                <label for="edit-username">Username</label>
                <input type="text" id="edit-username" name="username" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="edit-role">Role</label>
                <select id="edit-role" name="role" class="form-control" required>
                    <option value="">Pilih Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="edit-password">Password</label>
                <input type="password" id="edit-password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak diubah">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary" onclick="hidePopup()">Batal</button>
        </form>
    </div>
</div>

<div class="hero-section">
    <h4><span style="color: #01cfbe">Welcome,</span> Admin</h4>
    <h1>Dashboard <span style="color: #01cfbe" id="dynamic-title">Data Pengguna</span></h1>
    <p>Web ini khusus untuk pengelolaan data pengguna oleh Admin</p>
    <form action="/logout" method="post">
        @csrf
        <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-in-left"></i> Log Out</button>
    </form>
</div> 

<div class="container my-4">
    <!-- Tabel Data --> 
    <div class="box-tabel">
        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'custom-swal-confirm-button'
                        }
                    });
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: '{{ session('error') }}',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'custom-swal-confirm-button'
                        }
                    });
                });
            </script>
        @endif

        <div class="header">
            <h3 class="mb-2">Data Pengguna</h3>
            <div class="action-box">
                <div class="input-box">
                    <input type="text" class="form-control" id="search-input" placeholder="Cari Data Pengguna">
                </div>
            </div>
        </div>
        
        <div class="table-responsive-wrapper">
            <table class="table table-dark table-hover table-responsive table-sm">
                <thead>
                    <tr>
                        <th scope="col">#</th> <!-- Kolom untuk nomor -->
                        <th scope="col">Nama</th>
                        <th scope="col">Nim</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Username</th>
                        <th scope="col">Role</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                
                <tbody id="table-body">
                    @forelse($users as $index => $user)
                        <tr data-name="{{ strtolower($user->nama) }}">
                            <td>{{ $users->firstItem() + $index }}</td> <!-- Menampilkan nomor baris sesuai dengan pagination -->
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->nim }}</td>
                            <td>{{ $user->kelas }}</td>
                            <td>{{ $user->username }}</td>
                            <td>
                                @if($user->roles->count() > 0)
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary">{{ ucfirst($role->name) }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">No Role</span>
                                @endif
                            </td>
                            <td>
                                <button 
                                    class="btn btn-edit-user btn-sm" 
                                    title="Edit User" 
                                    data-id="{{ $user->id }}"
                                    data-nama="{{ $user->nama }}"
                                    data-username="{{ $user->username }}"
                                    data-role="{{ $user->roles->pluck('name')->first() }}">
                                    <i style="color: #000000" class="bi bi-pencil-square"></i>
                                </button>

                                <button 
                                    class="btn btn-delete-user btn-sm" 
                                    title="Hapus User" 
                                    data-id="{{ $user->id }}"
                                    data-nama="{{ $user->nama }}">
                                    <i style="color: #dc3545" class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="no-data">Oops, data tidak ada</td>
                        </tr>
                    @endforelse
                </tbody>
                
            </table>
        </div>
        
        <div class="d-flex box-pagination">
            <strong>Total Pengguna: {{ $users->total() }}</strong>
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
        
    </div>
    <div class="spacer"></div>
</div>

<!-- Definisikan data yang diperlukan untuk JavaScript -->
<script>
    window.AdminUser = {
        roles: @json($roles->pluck('name')),
        routes: {
            update: "{{ route('users.update', '') }}",
            destroy: "{{ route('users.destroy', '') }}"
        },
        csrfToken: "{{ csrf_token() }}"
    };
</script>

<script src="{{ asset('js/admin-user.js') }}"></script>

<script>

</script>

@endsection
