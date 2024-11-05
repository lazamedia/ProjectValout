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
        margin-bottom: 10px;
    }

    .form-label {
        font-size: 10pt;
        font-weight: 100;
        color: #f7efef;
    }

    .form-control {
        border-radius: 4px;
        border: 1px solid #cccccc;
        padding: 5px 15px;
        font-size: 15px;
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

    /* Tambahan CSS untuk Drag and Drop Image Upload */
    .image-upload-wrapper {
        position: relative;
        border: 2px dashed #00b3a3;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        color: #00b3a3;
        cursor: pointer;
        transition: background-color 0.3s, border-color 0.3s;
    }

    .image-upload-wrapper.dragover {
        background-color: rgba(1, 207, 190, 0.1);
        border-color: #01cfbe;
    }

    .image-upload-wrapper img {
        max-width: 100%;
        height: auto;
        max-height: 200px;
        object-fit: contain;
        margin-top: 10px;
        border-radius: 4px;
    }

    .delete-image-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(255, 255, 255, 0.7);
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #ff0000;
        font-size: 18px;
    }

    .delete-image-btn:hover {
        background-color: rgba(255, 255, 255, 1);
    }

    /* Atur agar teks drag-and-drop ditampilkan secara default */
    .drag-drop-text {
        display: block;
    }

    /* Sembunyikan image-preview-container secara default */
    .image-preview-container {
        display: none;
    }

    /* Media Queries */
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

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                {{-- Kotak Kiri: Username, Nama, NIM, Kelas --}}
                <div class="col-md-6">
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

                    {{-- NIM --}}
                    <div class="form-group">
                        <label for="nim" class="form-label">NIM</label>
                        <input 
                            type="text" 
                            class="form-control @error('nim') is-invalid @enderror" 
                            id="nim" 
                            name="nim" 
                            value="{{ old('nim', $user->nim) }}" 
                            required
                            placeholder="Masukkan NIM Anda"
                        >
                        @error('nim')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Kelas --}}
                    <div class="form-group">
                        <label for="kelas" class="form-label">Kelas</label>
                        <input 
                            type="text" 
                            class="form-control @error('kelas') is-invalid @enderror" 
                            id="kelas" 
                            name="kelas" 
                            value="{{ old('kelas', $user->kelas) }}" 
                            required
                            placeholder="Masukkan kelas Anda"
                        >
                        @error('kelas')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Kotak Kanan: Drag and Drop Image Upload --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="image" class="form-label">Foto Profil</label>
                        <div 
                            class="image-upload-wrapper @error('image') is-invalid @enderror" 
                            id="image-upload-wrapper"
                        >
                            {{-- Teks Drag and Drop --}}
                            <div class="drag-drop-text" id="drag-drop-text">
                                <p>Seret dan lepas gambar di sini atau klik untuk memilih gambar</p>
                            </div>

                            {{-- Preview Gambar dan Tombol Delete --}}
                            <div class="image-preview-container" id="image-preview-container">
                                <img src="#" alt="Profile Image" id="profile-image-preview">
                                <button type="button" class="delete-image-btn" id="delete-image-btn">&times;</button>
                            </div>

                            {{-- Input File Tersembunyi --}}
                            <input 
                                type="file" 
                                id="image" 
                                name="image" 
                                accept="image/*" 
                                style="display: none;"
                            >
                        </div>
                        @error('image')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                        {{-- Input tersembunyi untuk menandai penghapusan gambar --}}
                        @if($user->image)
                            <input type="hidden" id="delete_image" name="delete_image" value="{{ old('delete_image', $user->delete_image) }}">
                        @endif
                    </div>
                </div>
            </div>

            {{-- Password dan Konfirmasi Password --}}
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

        // Script untuk Drag and Drop Image Upload
        const imageUploadWrapper = document.getElementById('image-upload-wrapper');
        const profileImageInput = document.getElementById('image');
        const dragDropText = document.getElementById('drag-drop-text');
        const imagePreviewContainer = document.getElementById('image-preview-container');
        const profileImagePreview = document.getElementById('profile-image-preview');
        const deleteImageBtn = document.getElementById('delete-image-btn');
        const deleteProfileImageInput = document.getElementById('delete_image');

        // Fungsi untuk menampilkan preview gambar
        function previewImage(file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                profileImagePreview.src = e.target.result;
                imagePreviewContainer.style.display = 'block';
                dragDropText.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }

        // Inisialisasi jika sudah ada gambar
        @if($user->image && !$user->delete_image)
            imagePreviewContainer.style.display = 'block';
            dragDropText.style.display = 'none';
            profileImagePreview.src = "{{ asset('storage/' . $user->image) }}";
        @endif

        // Event click untuk membuka dialog file
        imageUploadWrapper.addEventListener('click', () => {
            profileImageInput.click();
        });

        // Event dragover
        imageUploadWrapper.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUploadWrapper.classList.add('dragover');
        });

        // Event dragleave
        imageUploadWrapper.addEventListener('dragleave', () => {
            imageUploadWrapper.classList.remove('dragover');
        });

        // Event drop
        imageUploadWrapper.addEventListener('drop', (e) => {
            e.preventDefault();
            imageUploadWrapper.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                profileImageInput.files = files;
                previewImage(files[0]);
                // Jika ada gambar yang dihapus sebelumnya, reset input delete
                if (deleteProfileImageInput) {
                    deleteProfileImageInput.value = '';
                }
            }
        });

        // Event change pada input file
        profileImageInput.addEventListener('change', () => {
            if (profileImageInput.files && profileImageInput.files[0]) {
                previewImage(profileImageInput.files[0]);
                // Jika ada gambar yang dihapus sebelumnya, reset input delete
                if (deleteProfileImageInput) {
                    deleteProfileImageInput.value = '';
                }
            }
        });

        // Script untuk menghapus gambar profil
        if (deleteImageBtn) {
            deleteImageBtn.addEventListener('click', function (e) {
                e.stopPropagation(); // Mencegah event click pada wrapper
                // Menghapus preview gambar
                imagePreviewContainer.style.display = 'none';
                profileImagePreview.src = '#';
                // Menampilkan kembali teks drag-and-drop
                dragDropText.style.display = 'block';
                // Mengosongkan input file
                profileImageInput.value = '';
                // Menandai bahwa gambar harus dihapus
                if (deleteProfileImageInput) {
                    deleteProfileImageInput.value = '1';
                }
            });
        }
    });
</script>

@endsection
