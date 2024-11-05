{{-- resources/views/absensi/create.blade.php --}}
@extends('layouts.main') 

@section('content')

<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">

<!-- Sertakan SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    .container{
        min-height: 70vh !important;
        background-color: #019a9500;
    }
    .judul{
        width: 100%;
        padding: 30px;
        justify-content: left;
        text-align: center;
        margin-top: 50px;
    }
    .judul h4{
        margin: 0%;
        color: #01cfbe;
    }
    .form-row{
        display: flex;
        justify-content: space-between;
        gap: 20px;
        box-sizing: border-box;
    }
    .form-group input{
        width: 100%;
        background-color: #019a9500;
        color: #019a94;
        border: 1px solid #019a94;
    }
    .form-group input:focus{
        background-color: #019a9500;
        color: #86b3b1;
        box-shadow: 0 2px 8px rgba(10, 190, 175, 0);
    }
    .form-group input::placeholder {
        color: #c2b8b8;
        font-size: 9pt;
    }
    .form-container {
        background-color: #f9f9f900;
        border: 1px solid #019a94;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(10, 190, 175, 0.26);
        margin-bottom: 50px;
    }
    .form-group{
        width: 100%;
    }
    .form-group label {
        font-weight: 100;
    }
    .btn-create {
        background-color: #01cfbe;
        border: 1px solid #019a94;
        color: rgb(12, 26, 46);
        text-decoration: none;
        box-sizing: border-box;
        padding: 3px 13px;
        margin: 10px;
    }
    .btn-create:hover {
        background-color: #019a9500;
        border: 1px solid #019a94;
        color: #019a94;
    }
    @media (max-width: 780px) {
        .container{
            width: 90%;
        }
    }

</style>

<div class="judul">
    <h4>Create Room Absen</h4>
    <p>Room untuk anggota melakukan absen</p>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12 form-container">
            <form action="{{ route('rooms.store') }}" method="POST">
                @csrf
                <!-- Nama Room -->
                <div class="form-group mb-4">
                    <label for="nama_room">Nama Room</label>
                    <input type="text" class="form-control" id="nama_room" name="nama_room" placeholder="Masukkan nama room" required>
                </div>

                <!-- Tema -->
                <div class="form-group mb-4">
                    <label for="tema">Tema</label>
                    <input type="text" class="form-control" id="tema" name="tema" placeholder="Masukkan tema" required>
                </div>

                <!-- Tanggal, Jam Mulai, dan Jam Berakhir -->
                <div class="form-row mb-4">
                    <div class="form-group ">
                        <label for="tanggal">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>
                    <div class="form-group ">
                        <label for="jam_mulai">Jam Mulai</label>
                        <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" required>
                    </div>
                    <div class="form-group ">
                        <label for="jam_berakhir">Jam Berakhir</label>
                        <input type="time" class="form-control" id="jam_berakhir" name="jam_berakhir" required>
                    </div>
                </div>

                <!-- Tombol Create -->
                <div class="text-center mt-5" style="align-content: center; align-items:center;">
                    <button type="submit" class="btn btn-create btn-lg">Create</button>
                    <a href="/rooms" class="btn-create">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sertakan SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                html: `<ul style="text-align: left;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                      </ul>`,
                confirmButtonColor: '#d33'
            });
        @endif

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                confirmButtonColor: '#01cfbe'
            });
        @endif
    });
</script>

@endsection
