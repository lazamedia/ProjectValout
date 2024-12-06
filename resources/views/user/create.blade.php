@extends('layouts.main')

@section('content')
    
<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<!-- Optional: Font Awesome for additional icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pkEcOm05p8eYu4ZvfTHF/hXhZ4GZwKC1K9K+GJZwrwQsy81suRQGqosQ6l1ZzlYriJT1Plh6Tv5MBsP4B2BtLg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="{{ asset('css/user-create.css') }}">


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
                    <input type="file" id="file-input" name="files[]" multiple webkitdirectory>
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
    <div class="spacer"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropzone = document.getElementById('file-dropzone');
        const fileInput = document.getElementById('file-input');
        const filePreview = document.getElementById('file-preview');
        const dataTransfer = new DataTransfer();

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

                // Add file to dataTransfer
                dataTransfer.items.add(file);

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
                    // Remove file from dataTransfer
                    for (let i = 0; i < dataTransfer.items.length; i++) {
                        if (dataTransfer.items[i].getAsFile().name === file.name) {
                            dataTransfer.items.remove(i);
                            break;
                        }
                    }
                    // Update the file input's files property
                    fileInput.files = dataTransfer.files;

                    // Remove the file item from the preview
                    fileItem.remove();
                });
                fileItem.appendChild(removeBtn);

                filePreview.appendChild(fileItem);

                // Update the file input's files property
                fileInput.files = dataTransfer.files;
            });
        }
    });
</script>

@endsection
