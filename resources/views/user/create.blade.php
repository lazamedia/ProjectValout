@extends('layouts.main')

@section('content')
    
<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<!-- Optional: Font Awesome for additional icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pkEcOm05p8eYu4ZvfTHF/hXhZ4GZwKC1K9K+GJZwrwQsy81suRQGqosQ6l1ZzlYriJT1Plh6Tv5MBsP4B2BtLg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    /* Dark Theme */
    body {
        background-color: #111325;
        color: #f5f5f5;
        font-family: 'tilt-neon', sans-serif;
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
</style>

<div class="hero-section">
    <h2>Add <span style="color: #01cfbe" id="dynamic-title">Project</span></h2>
    <p>Save Your Best Projects During Our Training</p>
</div>

<div class="container my-4">
    <div class="box-tabel">
        <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data" id="create-project-form">
            @csrf

            <!-- Project Name Input -->
            <div class="form-group">
                <label for="project-name">Project Name</label>
                <input type="text" class="form-control" id="project-name" name="name" placeholder="Enter project name" required>
            </div>

            <!-- Drag and Drop File Upload -->
            <div class="form-group">
                <label>Upload Files</label>
                <div class="dropzone" id="file-dropzone">
                    <i class="bi bi-upload"></i>
                    <input type="file" id="file-input" multiple style="display: none;">
                    <p>Drag & drop files here or click to select</p>
                </div>
                <div class="file-preview" id="file-preview"></div>
            </div>

            <!-- Submit Buttons -->
            <div class="tombol-aksi">
                <button type="button" class="back-btn"><a href="{{ route('user.index') }}">Back</a></button>
                <button type="submit" class="submit-btn">Create Project</button>
            </div>

        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropzone = document.getElementById('file-dropzone');
        const fileInput = document.getElementById('file-input');
        const filePreview = document.getElementById('file-preview');

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

        // Function to handle files
        function handleFiles(files) {
            files.forEach(file => {
                // Check if file already added
                if (document.querySelector(`.file-item[data-name="${file.name}"]`)) {
                    Swal.fire('File Already Added', `${file.name} has been added already.`, 'info');
                    return;
                }

                const fileItem = document.createElement('div');
                fileItem.classList.add('file-item');
                fileItem.setAttribute('data-name', file.name);

                // If file is image, show preview
                if(file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.onload = () => {
                        URL.revokeObjectURL(img.src); // Free memory
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
                removeBtn.title = 'Remove File';
                removeBtn.addEventListener('click', () => {
                    fileItem.remove();
                });
                fileItem.appendChild(removeBtn);

                filePreview.appendChild(fileItem);
            });
        }

        // Optional: Handle form submission
        const form = document.getElementById('create-project-form');
        form.addEventListener('submit', (e) => {
            // Collect files from preview
            const files = [];
            const fileItems = document.querySelectorAll('.file-item');
            fileItems.forEach(item => {
                const fileName = item.getAttribute('data-name');
                const file = Array.from(fileInput.files).find(f => f.name === fileName);
                if(file){
                    files.push(file);
                }
            });

            // Append files to form data
            if(files.length > 0){
                const dataTransfer = new DataTransfer();
                files.forEach(file => {
                    dataTransfer.items.add(file);
                });
                fileInput.files = dataTransfer.files;
            }
        });
    });
</script>

@endsection
