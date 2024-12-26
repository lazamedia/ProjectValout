@extends('layouts.main')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">

<style>
    .tombol-aksi {
        display: flex;
        gap: 20px;
        width: 100%;
        padding: 20px;
        box-sizing: border-box;
        justify-content: center;
    }
    .tombol-aksi a {
        text-decoration: none;
        color: #01cfbe;
        border: 1px solid #01cfbe;
        padding: 3px 10px;
        border-radius: 3px;
        transition: 0.3s;
    }
    .tombol-aksi a:hover {
        transform: translateY(-2px);
        transition: 0.3s;
    }
    .container {
        width: 100%;
        padding: 30px;
    }

    .btn {
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 10pt;
    }
    .btn-edit {
        color: #fff;
        background-color: #28a745;
    }
    .btn-add {
        color: #fff;
        background-color: #007bff;
    }
    .btn-delete {
        color: #fff;
        background-color: #dc3545;
    }
    .btn-delete:hover {
        background-color: #c82333;
        border: none !important;
    }
    .btn-edit:hover {
        background-color: #218838;
    }
    .btn-add:hover {
        background-color: #0062cc;
    }

    /* Style untuk status aktif/inaktif */
    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-active {
        background-color: rgb(2, 255, 2);
    }

    .status-expired {
        background-color: rgb(184, 27, 27);
    }
</style>

<div class="hero-section">
    <h4><span style="color: #01cfbe">Data,</span> Manajemen </h4>
    <h1>Dashboard <span style="color: #01cfbe" id="dynamic-title">Keaktifan Anggota</span></h1>
</div> 

<div class="container">
    <div class="box-tabel">
        <div class="header">
            <h3>Data Absen</h3>
            <div class="action-box">
                <a href="#" id="bulk-delete-btn" class="bulk-delete-btn" title="Hapus Terpilih"><i class="bi bi-trash"></i></a>
                <a href="{{ route('rooms.create') }}" class="add-btn" id="add-btn" title="Tambah Project">Add  <i class="bi bi-plus"></i></a>
                <div class="input-box">
                    <input type="text" class="form-control" id="search-input" placeholder="Cari Data">
                </div>
            </div>
        </div>
        <div class="table-responsive-wrapper">
            <table class="table table-dark table-hover table-responsive table-sm">
                <thead>
                    <tr>
                        <th></th> <!-- Menambahkan kolom status -->
                        <th>Kode Absen</th>
                        <th>Nama Room</th>
                        <th>Tema</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                        <tr>
                            <td>
                                @php
                                    $currentTime = \Carbon\Carbon::now();
                                    $endTime = \Carbon\Carbon::parse($room->tanggal . ' ' . $room->jam_berakhir);
                                @endphp

                                @if($currentTime->isBefore($endTime)) 
                                    <span class="status-indicator status-active" title="Room Absen Aktif"></span> <!-- Bulat hijau -->
                                @else
                                    <span class="status-indicator status-expired" title="Room Absen Kadaluarsa"></span> <!-- Bulat merah -->
                                @endif
                            </td>
                            <td>{{ $room->kode_absen }}</td>
                            <td>{{ $room->nama_room }}</td>
                            <td>{{ $room->tema }}</td>
                            <td>{{ \Carbon\Carbon::parse($room->tanggal)->format('d M Y') }}</td>
                            <td>{{ $room->absensis_count }}</td>
                            <td>
                                <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-edit">Edit</a>
                                <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-add">View</a>
                                <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="delete-form" data-nama-room="{{ $room->nama_room }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="spacer-new"></div>

<!-- Sertakan SweetAlert2 JS setelah semua konten -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                confirmButtonColor: '#01cfbe'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
            });
        @endif

        // Handle delete confirmation with SweetAlert
        const deleteForms = document.querySelectorAll('.delete-form');

        deleteForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Mencegah form langsung disubmit

                const namaRoom = this.getAttribute('data-nama-room');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Anda akan menghapus room "${namaRoom}".`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // Jika dikonfirmasi, submit form
                    }
                });
            });
        });
    });
</script>

@endsection
