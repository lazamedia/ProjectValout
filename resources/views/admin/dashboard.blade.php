<!-- resources/views/admin/dashboard.blade.php -->

@extends('layouts.main')

@section('content')

<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">

<style>
    .btn-download-all {
        background-color: #01cfbe00;
        border: 1px solid #01cfbe;
        color: #01cfbe;
        font-size: 9pt;
        margin-right: 10px;
    }

    .btn-download-all:hover {
        background-color: #01cfbe;
        color: #020829;
    }

    /* Custom SweetAlert Background */
    .custom-swal-popup {
        background-color: #172433 !important; /* Latar belakang SweetAlert */
        color: #ffffff; /* Warna teks SweetAlert */
        border: 1px solid #01cfbe;
    }

    /* Custom Confirm Button */
    .custom-swal-confirm-button {
        background-color: #01cfbe !important; /* Warna tombol konfirmasi */
        color: #020829 !important;
        border: none;
        padding: 10px 15px;
        font-weight: bold;
        font-size: 10pt;
    }

    .custom-swal-confirm-button:hover {
        background-color: #019e98 !important; /* Warna tombol saat hover */
    }

    /* Custom Cancel Button */
    .custom-swal-cancel-button {
        background-color: #333 !important; /* Warna tombol batal */
        color: #ffffff !important;
        border: none;
        padding: 10px 15px;
        font-weight: bold;
        font-size: 10pt;
    }

    .custom-swal-cancel-button:hover {
        background-color: #555 !important; /* Warna tombol batal saat hover */
    }
    .box-pagination{
        align-content: center;
        justify-content: space-between;
        align-items: center;
    }
    @media (max-width:780px){
        .box-pagination{
            display: flex;
            flex-direction: column;
            width: 100%;
            justify-content: right;
        }
    }
</style>

<div class="hero-section">
    <h4><font style="color: #01cfbe">Welcome,</font> Admin </h4>
    <h1>Dashboard <span style="color: #01cfbe" id="dynamic-title">Semua Project Pengguna</span></h1>
    <p>Web ini khusus untuk pengelolaan project oleh Admin</p>
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
            <h3 class="mb-2">Data Project Semua User</h3>
            <div class="action-box">
                <!-- Tombol Download All dengan event JavaScript -->
                <button id="btn-download-all" class="btn btn-download-all" title="Download Semua Project">
                    <i class="bi bi-download"></i> All
                </button>
                <div class="input-box">
                    <input type="text" class="form-control" id="search-input" placeholder="Cari Data">
                </div>
            </div>
        </div>
        
        <div class="table-responsive-wrapper">
            <table class="table table-dark table-hover table-responsive table-sm">
                <thead>
                    <tr>
                        <th scope="col">#</th> <!-- Kolom untuk nomor -->
                        <th scope="col">Nama User</th>
                        <th scope="col">Nama Project</th>
                        <th scope="col">Tanggal Dibuat</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @forelse($projects as $index => $project)
                        <tr data-name="{{ strtolower($project->name) }}">
                            <td>{{ $projects->firstItem() + $index }}</td> <!-- Menampilkan nomor baris sesuai dengan pagination -->
                            <td>{{ $project->user->nama }}</td>
                            <td>{{ $project->name ?? 'User Tidak Ditemukan' }}</td>
                            <td>{{ $project->tanggal }}</td> 
                            <td>
                                <a href="{{ route('admin.edit', $project->id) }}" class="btn btn-info btn-sm" title="Edit Project">
                                    <i style="color: #02e2bd" class="bi bi-pencil-square"></i>
                                </a>
                                <a href="{{ route('user.projects.download', $project->id) }}" class="btn btn-success btn-sm" title="Download Semua File">
                                    <i class="bi bi-download"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="no-data">Oops, data tidak ada</td> <!-- Sesuaikan jumlah kolom colspan -->
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
        </div>
        
        <div class="d-flex box-pagination">
            <strong>Total Project: {{ $projects->total() }}</strong>
            {{ $projects->links('pagination::bootstrap-4') }}
        </div>
        
    </div>
    <div class="spacer"></div>
</div>

<script>
    // Handle search functionality (Real-time)
    document.getElementById('search-input').addEventListener('input', function(){
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#table-body tr:not(.no-data)');
        let anyVisible = false;

        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            if(name.includes(query)){
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

    // Handle Download All Button Click
    document.getElementById('btn-download-all').addEventListener('click', function() {
        Swal.fire({
            title: 'Pilih Tanggal',
            html: '<input type="date" id="download-date" class="swal2-input">',
            showCancelButton: true,
            confirmButtonText: 'Download',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'custom-swal-popup', // Kelas khusus untuk popup
                confirmButton: 'custom-swal-confirm-button', // Kelas khusus untuk tombol konfirmasi
                cancelButton: 'custom-swal-cancel-button' // Kelas khusus untuk tombol batal
            },
            preConfirm: () => {
                const downloadDate = document.getElementById('download-date').value;
                if (!downloadDate) {
                    Swal.showValidationMessage('Silakan pilih tanggal');
                    return false;
                }
                return downloadDate;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const downloadDate = result.value;

                // Kirim permintaan AJAX ke checkDownload
                fetch(`{{ route('admin.projects.checkDownload') }}?date=${downloadDate}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.error || 'Terjadi kesalahan.'); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Tampilkan SweetAlert sukses
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Unduhan akan dimulai...',
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'custom-swal-popup',
                                confirmButton: 'custom-swal-confirm-button'
                            }
                        }).then(() => {
                            // Inisiasi pengunduhan dengan mengarahkan ke rute unduhan
                            window.location.href = `{{ route('admin.projects.downloadAll') }}?date=${downloadDate}`;
                        });
                    }
                })
                .catch(error => {
                    // Tampilkan SweetAlert error
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
</script>

@endsection
