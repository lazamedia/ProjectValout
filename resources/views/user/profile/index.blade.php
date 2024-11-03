{{-- resources/views/user/profile/edit.blade.php --}}

@extends('layouts.main')

@section('content')

<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    /* ... (CSS Anda tetap sama) ... */
    .c-profile{
        padding: 30px;
        margin-top: -50px;
        box-sizing: border-box;
        margin-bottom: 200px;
        box-shadow: 0 8px 30px rgba(4, 187, 156, 0.1);
        border-radius: 8px;
    }

    .hero-section {
        text-align: center;
        padding: 40px 20px;
        margin-bottom: 30px;
        border-radius: 8px;
    }

    .hero-section h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        color: #f0f0f0;
    }

    .hero-section p {
        font-size: 1rem;
        color: #dddbdb;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 200;
        color: #f7efef;
    }

    .form-control {
        border-radius: 4px;
        border: 1px solid #cccccc;
        padding: 10px 15px;
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: #01cfbe;
        background-color: #00b3a400;
        color: #00b3a3; 
        box-shadow: 0 0 5px rgba(1, 207, 190, 0.5);
    }
    .form-control::placeholder{
        color: #a3a3a3;
        font-size: 9pt;
    }

    .btn-primary {
        background-color: #01cfbe;
        border-color: #01cfbe;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 4px;
    }

    .btn-primary:hover {
        background-color: #00b3a3;
        border-color: #00b3a3;
    }

    .alert-success {
        background-color: #d4edda00;
        border-color: #00b3a3;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da00;
        border-color: #00b3a3;
        color: #721c24;
    }

    .form-group input{
        background-color: #33333300;
        border: 1px solid #00b3a3;
        color: #00b3a3;
    }

    .box-tombol{
        width: 100%;
        justify-content: center !important;
        text-align: center;
        align-items: center;
        align-content: center;
        color: #01cfbe;
    }
    .btn-primary{
        text-decoration: none;
        background-color: #00b3a400 !important;
        color: #00b3a3;
        border: 1px solid #00b3a3;
        padding: 5px 10px !important;  
        border-radius: 2px;
        margin: 20px;
    }
    .tombol-1{
        text-decoration: none;
        color: #00b3a3;
        border: 1px solid #00b3a3;
        padding: 5px 10px !important;  
        border-radius: 2px;
    }
    .tombol-1:hover{
        border: 1px solid #00b3a3 !important;
        transform: scale(1.0.3)
    }


    @media (max-width: 576px) {
        .hero-section h1 {
            font-size: 2rem;
        }

        .hero-section p {
            font-size: 1rem;
        }

        .c-profile {
            margin-bottom: 150px;
        }
    }
</style>

<div class="hero-section">
    <h1>Hai.. <span style="color: #01cfbe" id="dynamic-title">{{ auth()->user()->nama }}</span></h1>
    <p>Kelola profil Anda dengan mudah dan aman</p>
</div> 

<div class="container mt-5">
   <div class="c-profile">
        {{-- Hapus atau komentari notifikasi Bootstrap
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Ada beberapa masalah dengan input Anda.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        --}}

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf

            {{-- Username --}}
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input 
                    type="text" 
                    class="form-control @error('username') is-invalid @enderror" 
                    id="username" 
                    name="username" 
                    value="{{ old('username', $user->username) }}" 
                    required
                    placeholder="Masukkan username baru"
                >
                @error('username')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Nama --}}
            <div class="form-group">
                <label for="nama" class="form-label">Nama</label>
                <input 
                    type="text" 
                    class="form-control @error('nama') is-invalid @enderror" 
                    id="nama" 
                    name="nama" 
                    value="{{ old('nama', $user->nama) }}" 
                    required
                    placeholder="Masukkan nama lengkap Anda"
                >
                @error('nama')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password dan Konfirmasi Password Bersebelahan --}}
            <div class="row password-row">
                {{-- Password --}}
                <div class="form-group col-md-6">
                    <label for="password" class="form-label">Password Baru</label>
                    <input 
                        type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        placeholder="Masukkan password baru jika ingin mengubah"
                    >
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="form-group col-md-6">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                    <input 
                        type="password" 
                        class="form-control @error('password_confirmation') is-invalid @enderror" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        placeholder="Konfirmasi password baru"
                    >
                    @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="box-tombol">
                <button type="submit" class="btn btn-primary">Perbarui Profil</button>
            </div>
        </form>
    </div>
</div>

{{-- Tambahkan script SweetAlert2 --}}
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Script untuk menampilkan SweetAlert berdasarkan session --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Cek session success
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: "{{ session('success') }}",
                confirmButtonColor: '#01cfbe'
            });
        @endif

        // Cek session error (jika ada)
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
                confirmButtonColor: '#01cfbe'
            });
        @endif

        // Cek jika ada error validasi
        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Ada Kesalahan',
                html: `
                    <ul style="text-align: left;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                confirmButtonColor: '#01cfbe'
            });
        @endif

        // Validasi password real-time
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        function validatePassword() {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.classList.add('is-invalid');
                // Cek apakah sudah ada pesan error
                if (!passwordConfirmation.nextElementSibling || !passwordConfirmation.nextElementSibling.classList.contains('invalid-feedback')) {
                    const errorDiv = document.createElement('div');
                    errorDiv.classList.add('invalid-feedback');
                    errorDiv.textContent = 'Password tidak sama.';
                    passwordConfirmation.parentNode.appendChild(errorDiv);
                }
            } else {
                passwordConfirmation.classList.remove('is-invalid');
                if (passwordConfirmation.nextElementSibling && passwordConfirmation.nextElementSibling.classList.contains('invalid-feedback')) {
                    passwordConfirmation.nextElementSibling.remove();
                }
            }
        }

        password.addEventListener('input', validatePassword);
        passwordConfirmation.addEventListener('input', validatePassword);
    });
</script>

@endsection
