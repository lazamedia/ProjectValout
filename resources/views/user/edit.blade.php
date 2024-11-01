@extends('layouts.main')

@section('content')
    
<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* Dark Theme */
    body {
        background-color: #111325;
        color: #f5f5f5;
        font-family: 'Quantico';
    }

    .hero-section h2{
        margin: 0;
        font-size: 25pt;
    }

    .hero-section p{
        margin: 5px 0 0 0;
        font-size: 14pt;
    }

    .hero-section{
        padding: 20px;
        margin-top: 50px;
        min-height: 100px;
        text-align: center;
    }

    .box-tabel {
        padding: 20px;
        box-sizing: border-box;
        box-shadow: 0 8px 30px rgba(4, 187, 156, 0.1);
        background-color: #1c1c2e;
        border-radius: 8px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        background-color: #2a2a3d;
        border: 1px solid #01cfbe;
        color: #f5f5f5;
    }

    .form-control:focus {
        background-color: #2a2a3d;
        color: #f5f5f5;
        border-color: #01cfbe;
        box-shadow: 0 0 0 0.2rem rgba(1, 207, 190, 0.25);
    }

    .form-control::placeholder {
        color: #f5f5f59a;
        font-size: 10pt;
    }

    /* Styling for drag-and-drop area */
    .dropzone {
        border: 2px dashed #01cfbe;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.3s, border-color 0.3s;
        background-color: #2a2a3d;
        color: #f5f5f5;
        display: flex;
        flex-direction: column; /* Stack icon below the text */
        align-items: center;
        justify-content: center;
    }

    .dropzone.dragover {
        background-color: #01cfbe20;
        border-color: #01cfbe;
    }

    .dropzone i {
        font-size: 40px; /* Adjust icon size as needed */
        margin-bottom: 10px; /* Space between icon and text */
    }

    .file-preview {
        margin-top: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .file-item {
        background-color: #2a2a3d;
        padding: 10px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        width: 200px;
    }

    .file-item img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
    }

    .file-item .file-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .file-item .remove-file {
        color: #dc3545;
        cursor: pointer;
    }
    .tombol-aksi {
        display: flex;
        justify-content: end;
        gap: 10px;
    }
    .submit-btn {
        background-color: #01cfbe;
        border: none;
        color: #111325;
        padding: 6px 15px;
        border-radius: 4px;
        font-size: 12pt;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .back-btn {
        background-color: #8d8d8d;
        border: none;
        color: #111325;
        padding: 6px 15px;
        border-radius: 4px;
        font-size: 12pt;
        cursor: pointer;
        transition: background-color 0.3s;
        text-decoration: none;
    }
    .back-btn a{
        text-decoration: none;
        color: #111325;
    }
    .submit-btn:hover {
        background-color: #02e8d2;
    }

    .form-group label {
        font-size: 10pt;
        color: #f5f5f5;
    }

    @media (max-width: 768px) {
        .file-item {
            width: 100%;
        }
    }

    /* Hide the file input but keep it accessible */
    #file-input {
        position: absolute;
        left: -9999px;
    }

    /* Styling for existing files list */
    .existing-files {
        margin-top: 20px;
    }

    .existing-file-item {
        background-color: #2a2a3d;
        padding: 10px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top:10px;
        width: 100%;
    }

    .existing-file-item img {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
    }

    .existing-file-item .file-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .existing-file-item .download-file {
        color: #01cfbe;
        cursor: pointer;
        margin-right: 10px;
    }

    .existing-file-item .delete-file {
        color: #dc3545;
        cursor: pointer;
    }
</style>

<div class="hero-section">
    <h2>Edit <span style="color: #01cfbe" id="dynamic-title">Project</span></h2>
    <p>Update your project details below</p>
</div>

<div class="container my-4">
    <div class="box-tabel">
        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: '{!! implode('<br>', $errors->all()) !!}'
                    });
                });
            </script>
        @endif

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

        <form action="{{ route('user.projects.update', $project->id) }}" method="POST" enctype="multipart/form-data" id="edit-project-form">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="project-name">Project Name</label>
                <input type="text" class="form-control" id="project-name" name="name" value="{{ old('name', $project->name) }}" placeholder="Enter project name" required>
            </div>

            <div class="form-group">
                <label>Upload New Files (optional)</label>
                <div class="dropzone" id="file-dropzone">
                    <i class="bi bi-upload"></i>
                    <input type="file" id="file-input" name="files[]" multiple>
                    <p>Drag & drop files here or click to select</p>
                </div>
                <div class="file-preview" id="file-preview"></div>
            </div>

            <!-- Daftar File yang Sudah Ada -->
            <div class="form-group existing-files">
                <label>Existing Files</label>
                @if($project->files->count() > 0)
                    <div class="mb-3">
                        <a href="{{ route('user.projects.download', $project->id) }}" class="btn btn-success btn-sm" title="Download Semua File">
                            <i class="bi bi-download"></i> Download All
                        </a>
                    </div>
                    @foreach($project->files as $file)
                        <div class="existing-file-item" data-id="{{ $file->id }}">
                            @if(str_starts_with(mime_content_type(storage_path('app/public/' . $file->file_path)), 'image/'))
                                <img src="{{ Storage::url($file->file_path) }}" alt="File Image">
                            @else
                                <i class="bi bi-file-earmark"></i>
                            @endif
                            <div class="file-name">{{ basename($file->file_path) }}</div>
                            <a href="{{ asset('storage/' . $file->file_path) }}" class="download-file" title="Download File" target="_blank">
                                <i class="bi bi-download"></i>
                            </a>
                            <button type="button" class="delete-file btn btn-sm" title="Delete File">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    <p>-</p>
                @endif
            </div>

            <!-- Hidden Inputs untuk Menghapus File -->
            <div id="deleted-files">
                <!-- Diisi oleh JavaScript saat pengguna menghapus file -->
            </div>

            <!-- Submit Buttons -->
            <div class="tombol-aksi">
                <a href="{{ route('user.index') }}" class="back-btn">Back</a>
                <button type="submit" class="submit-btn">Update Project</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropzone = document.getElementById('file-dropzone');
        const fileInput = document.getElementById('file-input');
        const filePreview = document.getElementById('file-preview');
        const deletedFilesContainer = document.getElementById('deleted-files');
        const dataTransfer = new DataTransfer(); // Objek DataTransfer untuk mengelola file

        // Fungsi untuk memuat ulang file yang sudah di-upload sebelumnya
        function initializeExistingFiles() {
            @foreach($project->files as $file)
                // Jika perlu, Anda dapat menambahkan logika untuk menandai file existing agar tidak bentrok dengan file baru
            @endforeach
        }

        // Inisialisasi jika diperlukan
        initializeExistingFiles();

        // Handle drag over
        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        // Handle drag leave
        dropzone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
        });

        // Handle drop
        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files);
            handleFiles(files);
        });

        // Handle click to open file dialog
        dropzone.addEventListener('click', () => {
            fileInput.click();
        });

        // Handle file input change
        fileInput.addEventListener('change', () => {
            const files = Array.from(fileInput.files);
            handleFiles(files);
            fileInput.value = ''; // Reset file input
        });

        // Function untuk menangani file yang diupload
        function handleFiles(files) {
            files.forEach(file => {
                // Cek apakah file sudah ditambahkan
                if (document.querySelector(`.file-item[data-name="${file.name}"]`)) {
                    Swal.fire('File Sudah Ditambahkan', `${file.name} sudah ditambahkan sebelumnya.`, 'info');
                    return;
                }

                // Tambahkan file ke dataTransfer
                dataTransfer.items.add(file);

                const fileItem = document.createElement('div');
                fileItem.classList.add('file-item');
                fileItem.setAttribute('data-name', file.name);

                // Jika file adalah gambar, tampilkan preview
                if(file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.onload = () => {
                        URL.revokeObjectURL(img.src); // Bebaskan memori
                    };
                    fileItem.appendChild(img);
                } else {
                    const fileIcon = document.createElement('i');
                    fileIcon.classList.add('bi', 'bi-file-earmark');
                    fileIcon.style.fontSize = '40px';
                    fileItem.appendChild(fileIcon);
                }

                const fileName = document.createElement('div');
                fileName.classList.add('file-name');
                fileName.textContent = file.name;
                fileItem.appendChild(fileName);

                const removeBtn = document.createElement('i');
                removeBtn.classList.add('bi', 'bi-x-circle', 'remove-file');
                removeBtn.title = 'Hapus File';
                removeBtn.addEventListener('click', () => {
                    // Hapus file dari dataTransfer
                    for (let i = 0; i < dataTransfer.items.length; i++) {
                        if (dataTransfer.items[i].getAsFile().name === file.name) {
                            dataTransfer.items.remove(i);
                            break;
                        }
                    }
                    // Perbarui fileInput's files property
                    fileInput.files = dataTransfer.files;

                    // Hapus tampilan file dari UI
                    fileItem.remove();
                });
                fileItem.appendChild(removeBtn);

                filePreview.appendChild(fileItem);

                // Perbarui fileInput's files property
                fileInput.files = dataTransfer.files;
            });
        }

        // Handle deletion of existing files
        document.querySelectorAll('.delete-file').forEach(button => {
            button.addEventListener('click', function(){
                const fileItem = this.closest('.existing-file-item');
                const fileId = fileItem.getAttribute('data-id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan menghapus file ini.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tambahkan file ID ke input tersembunyi
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'deleted_files[]';
                        hiddenInput.value = fileId;
                        deletedFilesContainer.appendChild(hiddenInput);

                        // Hapus tampilan file dari UI
                        fileItem.remove();

                        Swal.fire(
                            'Terhapus!',
                            'File telah dihapus.',
                            'success'
                        );
                    }
                });
            });
        });
    });
</script>

@endsection
