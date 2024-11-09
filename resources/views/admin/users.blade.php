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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('admin-user.js loaded'); // Debug: Pastikan JS terhubung

        // Handle search functionality (Real-time)
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function(){
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
        }

        // Function to show "Oops data tidak ada" message
        function showNoDataMessage() {
            const tableBody = document.getElementById('table-body');
            if(!document.getElementById('no-data-row')){
                const noDataRow = document.createElement('tr');
                noDataRow.id = 'no-data-row';
                noDataRow.innerHTML = `
                    <td colspan="5" class="no-data">Oops, data tidak ada</td>
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
        console.log(`Found ${editButtons.length} edit buttons`); // Debug: Jumlah tombol edit
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                console.log('Edit button clicked'); // Debug: Klik tombol edit
                const userId = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const username = this.getAttribute('data-username');
                const role = this.getAttribute('data-role');

                console.log('User Data:', { userId, nama, username, role }); // Debug: Data pengguna

                // Mengisi form popup dengan data pengguna
                document.getElementById('edit-user-id').value = userId;
                document.getElementById('edit-nama').value = nama;
                document.getElementById('edit-username').value = username;

                // Mengatur opsi role
                const roleSelect = document.getElementById('edit-role');
                roleSelect.innerHTML = '<option value="">Pilih Role</option>'; // Reset options
                window.AdminUser.roles.forEach(roleOption => {
                    const option = document.createElement('option');
                    option.value = roleOption;
                    option.textContent = roleOption.charAt(0).toUpperCase() + roleOption.slice(1);
                    if(roleOption === role){
                        option.selected = true;
                    }
                    roleSelect.appendChild(option);
                });

                // Menampilkan popup
                showPopup();
            });
        });

        // Tambahkan event listener untuk overlay
        const popupOverlay = document.getElementById('popupOverlay');
        if (popupOverlay) {
            popupOverlay.addEventListener('click', function(event) {
                if (event.target === popupOverlay) {
                    hidePopup();
                }
            });
        }

        // Definisikan fungsi showPopup dan hidePopup
        window.showPopup = function() {
            const popup = document.getElementById('popupOverlay');
            if (popup) {
                popup.classList.add('show');
                console.log('Popup displayed'); // Debug: Popup ditampilkan
            } else {
                console.error('Popup overlay not found');
            }
        }

        window.hidePopup = function() {
            const popup = document.getElementById('popupOverlay');
            if (popup) {
                popup.classList.remove('show');
                console.log('Popup hidden'); // Debug: Popup disembunyikan

                // Reset form setelah ditutup
                const editUserForm = document.getElementById('edit-user-form');
                if (editUserForm) {
                    editUserForm.reset();
                }
            }
        }

        // Handle form submission
        const editUserForm = document.getElementById('edit-user-form');
        if (editUserForm) {
            editUserForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const userId = document.getElementById('edit-user-id').value;
                const nama = document.getElementById('edit-nama').value.trim();
                const username = document.getElementById('edit-username').value.trim();
                const role = document.getElementById('edit-role').value;
                const password = document.getElementById('edit-password').value.trim();

                console.log('Form submitted:', { userId, nama, username, role, password }); // Debug: Data form

                if (!nama || !username || !role) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Nama, Username, dan Role harus diisi',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'custom-swal-confirm-button'
                        }
                    });
                    return;
                }

                // Persiapkan data yang akan dikirim
                const data = {
                    nama: nama,
                    username: username,
                    role: role,
                    password: password
                };

                // Hapus password dari data jika kosong
                if (!password) {
                    delete data.password;
                }

                // Kirim permintaan AJAX untuk memperbarui data user
                fetch(`${window.AdminUser.routes.update}/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.AdminUser.csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    console.log('Response status:', response.status); // Debug: Status response
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan.'); });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data); // Debug: Data response

                    // Sembunyikan popup sebelum notifikasi muncul
                    hidePopup();

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
                    // Sembunyikan popup jika terjadi error
                    hidePopup();
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
            });
        }

        // Fitur Hapus User
        const deleteButtons = document.querySelectorAll('.btn-delete-user');
        console.log(`Found ${deleteButtons.length} delete buttons`); // Debug: Jumlah tombol hapus
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');

                // Konfirmasi penghapusan menggunakan SweetAlert2
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
                    },
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kirim permintaan AJAX untuk menghapus pengguna
                        fetch(`${window.AdminUser.routes.destroy}/${userId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.AdminUser.csrfToken,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            console.log('Delete response status:', response.status); // Debug: Status response
                            if (!response.ok) {
                                return response.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan.'); });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Delete response data:', data); // Debug: Data response
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

    });
</script>

@endsection
