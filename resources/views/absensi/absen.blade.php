{{-- resources/views/absensi/absen.blade.php --}} 
@extends('layouts.main')

@section('content')

<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">

<style>
    .judul{
        width: 100%;
        justify-content: space-between;
        display: flex;
        text-align: left;
        margin-top: 50px;
        margin-bottom:30px; 
        padding: 10px;
    }
    .add-form {
        display: flex;
        gap: 10px;
    }
    .judul h4{
        margin: 0%;
        color: #01cfbe;
    }
    .form-container {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    .btn-download {
        background-color: #007bff;
        border: none;
        color: white;
    }
    .btn-download:hover {
        background-color: #0056b3;
    }
    .btn-add {
        background-color: #28a745;
        border: none;
        color: white;
    }
    .btn-add:hover {
        background-color: #218838;
    }
    .table-container {
        overflow-x: auto;
    }
    .tombol{
        color: #f5f5f5;
        font-size: 10pt;
        text-decoration: none;
        border: 1px solid #01cfbe;
        padding: 2px 10px;
        border-radius: 4px;
        gap: 10px;
        margin-left: 20px;
    }
    @media (max-width: 576px) {
        .form-row {
            flex-direction: column;
        }
        .form-group {
            width: 100%;
        }
        .tombol{
            margin: 0%;
        }
    }
    /* Styling untuk SweetAlert position */
    .swal2-popup {
        font-size: 1.6rem !important;
    }
</style>

<div class="container">
    <div class="judul">
        <div class="judul-kanan">
            <h4> {{ $room->nama_room }}</h4>
        <p>Tema: {{ $room->tema }}</p>
        <p>{{ \Carbon\Carbon::parse($room->tanggal)->format('d M Y') }}</p>
        </div>
        <div class="judul-kiri">
            <a href="/rooms" class="tombol">Back</a>
        </div>
    </div>
    
    <div class="box-tabel">

        <div class="header">
            <h3>Data Absen</h3>
            {{-- <a href="#" class="tombol">Download</a> --}}
            <div class="action-box">
                <form class="add-form" action="{{ route('absensi.addByNim', $room->id) }}" method="POST">
                    @csrf
                    <input type="text" class="form-control" id="nimInput" name="nim" placeholder="Masukkan NIM dan tekan Enter" required autofocus>
                    <button type="submit" class="btn add-btn">Add</button>
                </form>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive-wrapper">
                <table class="table table-dark table-hover table-responsive table-sm">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">NIM</th>
                            <th scope="col">Kelas</th>
                            <th scope="col">Waktu Absen</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        @forelse($room->absensis as $absensi)
                            <tr>
                                <td>{{ $absensi->user->nama }}</td>
                                <td>{{ $absensi->user->nim }}</td>
                                <td>{{ $absensi->user->kelas }}</td>
                                <td>{{ $absensi->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('absensi.destroy', $absensi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus absensi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Remove Absen">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data absensi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>



<script>
        function confirmDelete(event) {
        event.preventDefault(); // Mencegah submit langsung

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan bisa mengembalikan data ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit(); // Submit form setelah konfirmasi
            }
        });
    }

    // Tampilkan pesan SweetAlert jika ada session success
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}'
        });
    @endif
    document.addEventListener('DOMContentLoaded', function() {
        const nimInput = document.getElementById('nimInput');
        let lastInputTime = 0;
        let isTyping = false;

        nimInput.addEventListener('input', function(event) {
            const currentTime = new Date().getTime();
            const timeDifference = currentTime - lastInputTime;

            // Jika waktu antara input sangat kecil (input cepat), anggap itu dari scanner
            if (timeDifference < 50) {
                isTyping = false;
                nimInput.closest('form').submit(); // Submit form otomatis
            } else {
                isTyping = true;
            }

            lastInputTime = currentTime;
        });

        // Event listener untuk mendeteksi Enter jika pengguna sedang mengetik
        nimInput.addEventListener('keypress', function(event) {
            if (isTyping && event.key === 'Enter') {
                nimInput.closest('form').submit();
            }
        });
    });
</script>


@endsection
