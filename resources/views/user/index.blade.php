@extends('layouts.main')

@section('content')

<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">

<div class="hero-section">
    <h4><span style="color: #01cfbe">Welcome,</span> {{ auth()->user()->nama }} </h4>
    <h1>Dashboard <span style="color: #01cfbe" id="dynamic-title">Project Mingguan</span></h1>
    <p>Web ini khusus untuk pengumpulan project pribadi</p>
    <form action="/logout" method="post">
        @csrf
        <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-in-left"></i> Log Out</button>
    </form>
</div> 

<div class="container my-4">
    <!-- Tabel Data --> 
    <div class="box-tabel">
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

        <div class="header">
            <h3>Data Project</h3>
            <div class="action-box">
                <a href="#" id="bulk-delete-btn" class="bulk-delete-btn" title="Hapus Terpilih"><i class="bi bi-trash"></i></a>
                <a href="{{ route('user.create') }}" class="add-btn" id="add-btn" title="Tambah Project">Add  <i class="bi bi-plus"></i></a>
                <div class="input-box">
                    <input type="text" class="form-control" id="search-input" placeholder="Cari Data">
                </div>
            </div>
        </div>
        <form id="bulk-delete-form" method="POST" action="{{ route('user.projects.bulkDelete') }}">
            @csrf
            @method('DELETE')
            <div class="table-responsive-wrapper">
                <table class="table table-dark table-hover table-responsive table-sm">
                    <thead>
                        <tr>
                            <th scope="col"><input class="form-check-input" type="checkbox" onclick="toggleCheckboxes(this)" title="Pilih Semua"></th>
                            <th scope="col">Nama Project</th>
                            <th scope="col">Tanggal Dibuat</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        @forelse($projects as $project)
                            <tr data-name="{{ strtolower($project->name) }}">
                                <td><input type="checkbox" class="form-check-input bulk-delete" name="ids[]" value="{{ $project->id }}"></td>
                                <td>{{ $project->name }}</td>
                                <td>
                                    {{ $project->tanggal }}
                                </td>
                                <td>
                                    <a href="{{ route('user.edit', $project->id) }}" class="btn btn-info btn-sm" title="Edit Project">
                                        <i style="color: #01cfbe" class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('user.projects.download', $project->id) }}" class="btn btn-success btn-sm" title="Download Semua File">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="no-data-row">
                                <td colspan="4" class="no-data">Oops, data tidak ada</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div style="justify-content: right" class="d-flex ">
            {{ $projects->links('pagination::bootstrap-4') }}
        </div>
        
    </div>
    <div class="spacer"></div>
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
                // Submit the form
                document.getElementById('bulk-delete-form').submit();
            }
        });
    });

    // Handle individual delete buttons with confirmation
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e){
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus proyek ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
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
