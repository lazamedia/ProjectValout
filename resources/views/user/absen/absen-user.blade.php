{{-- resources/views/user/absen-user.blade.php --}}
@extends('layouts.main')

@section('content')

<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">

<!-- Sertakan SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    .form-group {
        margin-bottom: 20px;
    }
    .btn-primary {
        background-color: #01cfbe00;
        border: 1px solid #01cfbe;
        color: #01cfbe;
        font-size: 10pt;
        padding: 3px 10px;
    }
    .btn-primary:hover {
        background-color: #0e6b5f44;
        border: 1px solid #328f87;
        color: #06c9c9;
    }
    .form-absen{
        display: flex;
        gap: 10px;
        justify-content: space-between;
    }
    .alert {
        /* Hapus atau sesuaikan gaya alert bawaan jika diperlukan */
    }
</style>

<div class="container">
    
    <div class="hero-section">
        <h1 style="color: #01cfbe">Absen</h1>
        <p>Data absensi Anda</p>
    </div>

    <!-- Form untuk memasukkan kode absensi -->

    <!-- Tabel Data Absensi -->
    <div class="box-tabel">
        <div class="header">
            <h3>Riwayat Absensi</h3>
            <div class="action-box">
                <form action="{{ route('absenku.check') }}" method="POST">
                    @csrf
                    <div class="form-absen">
                        <input type="text" class="form-control" id="kode_absen" name="kode_absen" placeholder="Masukkan kode absensi" required>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle-dotted"></i> Absen</button>
                    </div>
                </form>
                
            </div>
        </div>
        <div class="table-responsive-wrapper">
            <table class="table table-dark table-hover table-responsive table-sm">
                <thead>
                    <tr>
                        <th scope="col">NIM</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Waktu Absen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($absensis as $absensi)
                        <tr>
                            <td>{{ $absensi->user->nim }}</td>
                            <td>{{ $absensi->user->nama }}</td>
                            <td>{{ $absensi->user->kelas }}</td>
                            <td>{{ $absensi->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
</div>

<!-- Sertakan SweetAlert2 JS -->
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
                confirmButtonColor: '#01cfbe'
            });
        @endif
    });
</script>

@endsection
