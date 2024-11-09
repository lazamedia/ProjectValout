<!-- resources/views/admin/users/index.blade.php -->

@extends('layouts.main')

@section('content')

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-user.css') }}">

<style>

</style>

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
                    Swal.fire(
                        'Success!',
                        '{{ session('success') }}',
                        'success'
                    );
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire(
                        'Error!',
                        '{{ session('error') }}',
                        'error'
                    );
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
                        <th scope="col">Username</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status</th> <!-- Kolom Status Baru -->
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                
                <tbody id="table-body">
                    @forelse($users as $index => $user)
                        <tr data-name="{{ strtolower($user->nama) }}">
                            <td>{{ $users->firstItem() + $index }}</td> <!-- Menampilkan nomor baris sesuai dengan pagination -->
                            <td>{{ $user->nama }}</td>
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
                                @if($user->last_login_at)
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $lastLogin = \Carbon\Carbon::parse($user->last_login_at);
                                        $diffInMinutes = $now->diffInMinutes($lastLogin);
                                    @endphp
                                    @if($diffInMinutes <= 5) <!-- Misalnya, aktif dalam 5 menit terakhir dianggap aktif -->
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-warning">Aktif {{ $lastLogin->diffForHumans() }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Tidak Diketahui</span>
                                @endif
                            </td>
                            <td>
                                <button 
                                    class="btn btn-edit-user btn-sm" 
                                    title="Edit User" 
                                    data-id="{{ $user->id }}"
                                    data-nama="{{ $user->nama }}"
                                    data-username="{{ $user->username }}"
                                    data-role="{{ $user->roles->pluck('name')->toJson() }}" >
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
                            <td colspan="6" class="no-data">Oops, data tidak ada</td> <!-- Sesuaikan jumlah kolom colspan -->
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

<script src="{{ asset('js/admin-user.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Handle search functionality (Real-time)
document.getElementById('search-input').addEventListener('input', function(){
const query = this.value.toLowerCase().trim();
const rows = document.querySelectorAll('#table-body tr:not(.no-data)');
let anyVisible = false;

rows.forEach(row => {
    const cells = row.querySelectorAll('td');
    let rowText = '';
    cells.forEach(cell => {
        rowText += cell.textContent.toLowerCase() + ' ';
    });

    if(rowText.includes(query)){
        row.style.display = '';
        anyVisible = true;
    } else {
        row.style.display = 'none';
    }
});

// Check if any row is visible
if(query !== '' && !anyVisible){
    showNoDataMessage();
} else {
    removeNoDataMessage();
}
});

// Function to show "Oops data tidak ada" message
function showNoDataMessage() {
const tableBody = document.getElementById('table-body');
if(!document.getElementById('no-data-row')){
    const noDataRow = document.createElement('tr');
    noDataRow.id = 'no-data-row';
    noDataRow.innerHTML = `
        <td colspan="4" class="no-data">Oops, data tidak ada</td>
    `;
    tableBody.appendChild(noDataRow);
}
}

// Function to remove "Oops data tidak ada" message
function removeNoDataMessage() {
const noDataRow = document.getElementById('no-data-row');
if(noDataRow){
    noDataRow.remove();
}
}


    // Handle Edit User Button Click
    const editButtons = document.querySelectorAll('.btn-edit-user');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const username = this.getAttribute('data-username');
            const rolesJson = this.getAttribute('data-role');
            const roles = JSON.parse(rolesJson); // Parsing JSON peran

            // Mendapatkan daftar peran yang tersedia
            const availableRoles = @json($roles->pluck('name'));

            // Membuat opsi untuk dropdown peran
            let options = '';
            availableRoles.forEach(role => {
                if(roles.includes(role)){
                    options += `<option value="${role}" selected>${role.charAt(0).toUpperCase() + role.slice(1)}</option>`;
                } else {
                    options += `<option value="${role}">${role.charAt(0).toUpperCase() + role.slice(1)}</option>`;
                }
            });

            Swal.fire({
                title: 'Edit User',
                html: `
                    <input type="hidden" id="user-id" value="${userId}">
                    <input type="text" id="nama" class="swal2-input" placeholder="Nama" value="${nama}">
                    <input type="text" id="username" class="swal2-input" placeholder="Username" value="${username}">
                    <select id="role" class="swal2-select">
                        ${options}
                    </select>
                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'custom-swal-popup', // Kelas khusus untuk popup
                    confirmButton: 'custom-swal-confirm-button', // Kelas khusus untuk tombol konfirmasi
                    cancelButton: 'custom-swal-cancel-button' // Kelas khusus untuk tombol batal
                },
                preConfirm: () => {
                    const id = document.getElementById('user-id').value;
                    const nama = document.getElementById('nama').value.trim();
                    const username = document.getElementById('username').value.trim();
                    const role = document.getElementById('role').value;

                    if (!nama || !username || !role) {
                        Swal.showValidationMessage('Semua field harus diisi');
                        return false;
                    }

                    return { id, nama, username, role };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const { id, nama, username, role } = result.value;

                    // Kirim permintaan AJAX untuk memperbarui data user
                    fetch(`{{ route('users.update', '') }}/${id}`, { // Pastikan route sesuai
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            nama: nama,
                            username: username,
                            role: role
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan.'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    confirmButton: 'custom-swal-confirm-button'
                                }
                            }).then(() => {
                                // Reload halaman atau update tabel secara dinamis
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan.',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    confirmButton: 'custom-swal-confirm-button'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            text: error.message,
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'custom-swal-popup',
                                confirmButton: 'custom-swal-confirm-button'
                            }
                        });
                        console.error('Error:', error);
                    });
                }
            });
        });
    });
});

/*** Fitur Hapus Individual ***/
const deleteButtons = document.querySelectorAll('.btn-delete-user');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus pengguna "${nama}".`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'custom-swal-popup',
                    confirmButton: 'custom-swal-confirm-button',
                    cancelButton: 'custom-swal-cancel-button'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan AJAX untuk menghapus pengguna
                    fetch(`{{ route('users.destroy', '') }}/${userId}`, { // Pastikan route sesuai
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan.'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    confirmButton: 'custom-swal-confirm-button'
                                }
                            }).then(() => {
                                // Reload halaman atau hapus baris secara dinamis
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan.',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    confirmButton: 'custom-swal-confirm-button'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            text: error.message,
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'custom-swal-popup',
                                confirmButton: 'custom-swal-confirm-button'
                            }
                        });
                        console.error('Error:', error);
                    });
                }
            });
        });
    });
</script>

@endsection
