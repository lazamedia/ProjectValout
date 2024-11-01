@extends('layouts.main')

@section('content')

<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* Tema gelap */
    body {
        background-color: #111325;
        color: #f5f5f5;
        font-family: 'tilt-neon', sans-serif;
    }
    .hero-section h1{
        margin: 0;
        font-size: 31pt;
    }
    .hero-section h4{
        margin: 0;
        font-size: 14pt;
    }
    .hero-section{
        padding: 20px;
        margin-top: 50px;
        min-height: 200px;
    }
    .table-dark{
        background-color: #01cfbe00 !important;
    }
    .box-tabel {
        padding: 20px;
        box-sizing: border-box;
        box-shadow: 0 8px 30px rgba(4, 187, 156, 0.1);
    }

    .table-dark th, .table-dark td {
        color: #ffffff;
        background-color: #01cfbe00;
        font-size: 11pt;
    }
    
    .table-dark .btn {
        background-color: #4a4a4a96;
        color: #ffffff;
        border: none;
    }

    .btn-create, .btn-delete {
        transition: all 0.3s ease;
        border-radius: 2px;
        font-size: 11pt;
        padding: 3px 10px;
    }

    .btn-create:hover {
        background-color: #28a74600;
        color: #ffffff;
        border: 1px solid #01cfbe;
    }

    .btn-delete:hover {
        background-color: #dc3545;
        color: #ffffff;
    }

    .hero-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        text-align: center;
    }
    .form-check-input{
        background-color: #01cfbe00;
        border-color: #01cfbe;
    }   
    .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(1, 207, 190, 0);
    }
    .form-check-input:checked {
        background-color: #01cfbe00;
    }
    .box-tabel h3 {
        font-size: 14pt;
    }
    .header{
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        align-content: center;  
    }
    .action-box{
        display: flex;
        gap: 20px;
        align-content: center;
        align-items: center;
    }
    .input-box{
        display: flex;
        gap: 5px;
    }
    .header input{
        width: 200px;
        background-color: #01cfbe00;
        border: 1px solid #01cfbe;
        color: #ffffff;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 11pt;
        height: 30px;
    }
    .header input:focus{
        outline: none;
        border: 1px solid #01cfbe;
        box-shadow: 0 0 0 0.25rem rgba(1, 207, 190, 0);
        background-color: #01cfbe00;
        color: #f5f5f5;
    }
    .header input::placeholder{
        color: #f5f5f59a;
        font-size: 10pt;
    }
    /* Memisahkan tombol hapus dan tambah */
    .action-box .bulk-delete-btn {
        color: #f5f5f5;
        font-size: 15pt;
        display: none; /* Sembunyikan ikon hapus secara default */
    }
    .action-box .bulk-delete-btn.show {
        display: inline; /* Tampilkan ikon hapus saat ada yang dipilih */
    }
    .action-box .add-btn{
        color: #f5f5f5;
        font-size: 15pt;
        text-decoration: none;
    }
    .action-box .add-btn:hover{
        color: #01cfbe;
    }
    .action-box .bulk-delete-btn:hover{
        color: #dc3545;
    }
    /* Styling untuk pesan "Oops data tidak ada" */
    .no-data {
        text-align: center;
        font-style: italic;
        color: #f5f5f5;
    }
    @media (max-width: 780px) {
        .hero-section{
            padding: 10px;
            min-height: 150px;
        }
        .hero-section h1{
            font-size: 20pt;
        }
        .hero-section h4{
            font-size: 12pt;
        }
        .hero-section p{
            font-size: 10pt;
            margin-top: 10px;
        }
        .header{
            flex-direction: column;
        }
    }
</style>

<div class="hero-section">
    <h4><span style="color: #01cfbe">Welcome,</span> Lazuardi Mandegar</h4>
    <h1>Dashboard <span style="color: #01cfbe" id="dynamic-title">Project Mingguan</span></h1>
    <p>Web ini khusus untuk pengumpulan project pribadi</p>
</div>

<div class="container my-4">
    <!-- Tabel Data --> 
    <div class="box-tabel">
        <div class="header">
            <h3>Data Project</h3>
            <div class="action-box">
                <a href="#" id="bulk-delete-btn" class="bulk-delete-btn" title="Hapus Terpilih"><i class="bi bi-trash"></i></a>
                <a href="/" class="add-btn" id="add-btn" title="Tambah Project"><i class="bi bi-plus-square-dotted"></i></a>
                <div class="input-box">
                    <input type="text" class="form-control" id="search-input" placeholder="Cari Data">
                </div>
            </div>
        </div>
        <table class="table table-dark table-hover table-responsive">
            <thead>
                <tr>
                    <th scope="col"><input class="form-check-input" type="checkbox" onclick="toggleCheckboxes(this)" title="Pilih Semua"></th>
                    <th scope="col">Nama Project</th>
                    <th scope="col">Jenis File</th>
                    <th scope="col">Edit</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <!-- Data Manual -->
                <tr data-name="proyek 1">
                    <td><input type="checkbox" class="form-check-input bulk-delete" value="1"></td>
                    <td>Proyek 1</td>
                    <td>PDF</td>
                    <td>
                        <button class="btn btn-info btn-sm">
                            <i style="color: #01cfbe" class="bi bi-pencil-square"></i>
                        </button>
                    </td>
                </tr>
                <tr data-name="proyek 2">
                    <td><input type="checkbox" class="form-check-input bulk-delete" value="2"></td>
                    <td>Proyek 2</td>
                    <td>Word</td>
                    <td>
                        <button class="btn btn-info btn-sm">
                            <i style="color: #01cfbe" class="bi bi-pencil-square"></i>
                        </button>
                    </td>
                </tr>
                <tr data-name="proyek 3">
                    <td><input type="checkbox" class="form-check-input bulk-delete" value="3"></td>
                    <td>Proyek 3</td>
                    <td>Excel</td>
                    <td>
                        <button class="btn btn-info btn-sm">
                            <i style="color: #01cfbe" class="bi bi-pencil-square"></i>
                        </button>
                    </td>
                </tr>
                <tr data-name="proyek 4">
                    <td><input type="checkbox" class="form-check-input bulk-delete" value="4"></td>
                    <td>Proyek 4</td>
                    <td>PowerPoint</td>
                    <td>
                        <button class="btn btn-info btn-sm">
                            <i style="color: #01cfbe" class="bi bi-pencil-square"></i>
                        </button>
                    </td>
                </tr>
                <!-- Tambahkan data lainnya sesuai kebutuhan -->
                <!-- End Data Manual -->
            </tbody>
        </table>
    </div>
</div>

<script>
    // Function to toggle all checkboxes
    function toggleCheckboxes(source) {
        const checkboxes = document.querySelectorAll('.bulk-delete');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
        toggleDeleteButton();
    }

    // Function to toggle delete button visibility
    function toggleDeleteButton() {
        const deleteBtn = document.getElementById('bulk-delete-btn');
        const anyChecked = document.querySelectorAll('.bulk-delete:checked').length > 0;
        if(anyChecked){
            deleteBtn.classList.add('show');
        } else {
            deleteBtn.classList.remove('show');
        }
    }

    // Event listeners for individual checkboxes
    document.querySelectorAll('.bulk-delete').forEach(checkbox => {
        checkbox.addEventListener('change', toggleDeleteButton);
    });

    // Handle bulk delete button click
    document.getElementById('bulk-delete-btn').addEventListener('click', function(e){
        e.preventDefault();
        const selectedCheckboxes = document.querySelectorAll('.bulk-delete:checked');
        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
        if(selectedIds.length === 0){
            Swal.fire('Tidak ada yang dipilih', 'Pilih setidaknya satu item untuk dihapus.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Anda akan menghapus ${selectedIds.length} item.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Hapus baris secara manual
                selectedCheckboxes.forEach(cb => {
                    const row = cb.closest('tr');
                    row.remove();
                });
                Swal.fire('Terhapus!', 'Item yang dipilih telah dihapus.', 'success');
                toggleDeleteButton();
                checkNoData();
            }
        });
    });

    // Handle search functionality (Real-time)
    document.getElementById('search-input').addEventListener('input', function(){
        const query = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#table-body tr:not(#no-data-row)');
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
        // Check if the no data row already exists
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

    // Function to check and display no data message after deletion
    function checkNoData(){
        const query = document.getElementById('search-input').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#table-body tr:not(#no-data-row)');
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

        if(query !== '' && !anyVisible){
            showNoDataMessage();
        } else {
            removeNoDataMessage();
        }
    }


</script>

@endsection
