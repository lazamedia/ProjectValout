document.addEventListener('DOMContentLoaded', function () {
    console.log('admin-user.js loaded'); // Debug: Pastikan JS terhubung

    // Handle search functionality (Real-time)
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(){
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#table-body tr:not(.no-data)');
            let anyVisible = false;
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let rowText = '';
                cells.forEach(cell => {
                    rowText += cell.textContent.toLowerCase() + ' ';
                });
            
                if(rowText.includes(query)){
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
    }

    // Function to show "Oops data tidak ada" message
    function showNoDataMessage() {
        const tableBody = document.getElementById('table-body');
        if(!document.getElementById('no-data-row')){
            const noDataRow = document.createElement('tr');
            noDataRow.id = 'no-data-row';
            noDataRow.innerHTML = `
                <td colspan="5" class="no-data">Oops, data tidak ada</td>
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

    // Handle Edit User Button Click
    const editButtons = document.querySelectorAll('.btn-edit-user');
    console.log(`Found ${editButtons.length} edit buttons`); // Debug: Jumlah tombol edit
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Edit button clicked'); // Debug: Klik tombol edit
            const userId = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const username = this.getAttribute('data-username');
            const role = this.getAttribute('data-role');

            console.log('User Data:', { userId, nama, username, role }); // Debug: Data pengguna

            // Mengisi form popup dengan data pengguna
            document.getElementById('edit-user-id').value = userId;
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-username').value = username;

            // Mengatur opsi role
            const roleSelect = document.getElementById('edit-role');
            roleSelect.innerHTML = '<option value="">Pilih Role</option>'; // Reset options
            window.AdminUser.roles.forEach(roleOption => {
                const option = document.createElement('option');
                option.value = roleOption;
                option.textContent = roleOption.charAt(0).toUpperCase() + roleOption.slice(1);
                if(roleOption === role){
                    option.selected = true;
                }
                roleSelect.appendChild(option);
            });

            // Menampilkan popup
            showPopup();
        });
    });

    // Tambahkan event listener untuk overlay
    const popupOverlay = document.getElementById('popupOverlay');
    if (popupOverlay) {
        popupOverlay.addEventListener('click', function(event) {
            if (event.target === popupOverlay) {
                hidePopup();
            }
        });
    }

    // Definisikan fungsi showPopup dan hidePopup
    window.showPopup = function() {
        const popup = document.getElementById('popupOverlay');
        if (popup) {
            popup.classList.add('show');
            console.log('Popup displayed'); // Debug: Popup ditampilkan
        } else {
            console.error('Popup overlay not found');
        }
    }

    window.hidePopup = function() {
        const popup = document.getElementById('popupOverlay');
        if (popup) {
            popup.classList.remove('show');
            console.log('Popup hidden'); // Debug: Popup disembunyikan

            // Reset form setelah ditutup
            const editUserForm = document.getElementById('edit-user-form');
            if (editUserForm) {
                editUserForm.reset();
            }
        }
    }

    // Handle form submission
    const editUserForm = document.getElementById('edit-user-form');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const userId = document.getElementById('edit-user-id').value;
            const nama = document.getElementById('edit-nama').value.trim();
            const username = document.getElementById('edit-username').value.trim();
            const role = document.getElementById('edit-role').value;
            const password = document.getElementById('edit-password').value.trim();

            console.log('Form submitted:', { userId, nama, username, role, password }); // Debug: Data form

            if (!nama || !username || !role) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Nama, Username, dan Role harus diisi',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'custom-swal-popup',
                        confirmButton: 'custom-swal-confirm-button'
                    }
                });
                return;
            }

            // Persiapkan data yang akan dikirim
            const data = {
                nama: nama,
                username: username,
                role: role,
                password: password
            };

            // Hapus password dari data jika kosong
            if (!password) {
                delete data.password;
            }

            // Kirim permintaan AJAX untuk memperbarui data user
            fetch(`${window.AdminUser.routes.update}/${userId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.AdminUser.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                console.log('Response status:', response.status); // Debug: Status response
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan.'); });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data); // Debug: Data response

                // Sembunyikan popup sebelum notifikasi muncul
                hidePopup();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'custom-swal-confirm-button'
                        }
                    }).then(() => {
                        // Reload halaman atau update tabel secara dinamis
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan.',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'custom-swal-confirm-button'
                        }
                    });
                }
            })
            .catch(error => {
                // Sembunyikan popup jika terjadi error
                hidePopup();
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    text: error.message,
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'custom-swal-popup',
                        confirmButton: 'custom-swal-confirm-button'
                    }
                });
                console.error('Error:', error);
            });
        });
    }

    // Fitur Hapus User
    const deleteButtons = document.querySelectorAll('.btn-delete-user');
    console.log(`Found ${deleteButtons.length} delete buttons`); // Debug: Jumlah tombol hapus
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');

            // Konfirmasi penghapusan menggunakan SweetAlert2
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus pengguna "${nama}".`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'custom-swal-popup',
                    confirmButton: 'custom-swal-confirm-button',
                    cancelButton: 'custom-swal-cancel-button'
                },
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim permintaan AJAX untuk menghapus pengguna
                    fetch(`${window.AdminUser.routes.destroy}/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.AdminUser.csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        console.log('Delete response status:', response.status); // Debug: Status response
                        if (!response.ok) {
                            return response.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan.'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Delete response data:', data); // Debug: Data response
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    confirmButton: 'custom-swal-confirm-button'
                                }
                            }).then(() => {
                                // Reload halaman atau hapus baris secara dinamis
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan.',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    confirmButton: 'custom-swal-confirm-button'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            text: error.message,
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'custom-swal-popup',
                                confirmButton: 'custom-swal-confirm-button'
                            }
                        });
                        console.error('Error:', error);
                    });
                }
            });
        });
    });

});