// public/js/admin-user.js

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
            const rolesJson = this.getAttribute('data-role');
            let roles = [];
            try {
                roles = JSON.parse(rolesJson);
                console.log('Parsed roles:', roles); // Debug: Roles yang diparsing
            } catch (e) {
                console.error('Error parsing roles JSON:', e);
            }

            // Mendapatkan daftar peran yang tersedia dari variabel global
            const availableRoles = window.AdminUser.roles;

            // Mengisi form popup dengan data pengguna
            document.getElementById('edit-user-id').value = userId;
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-username').value = username;

            // Mengatur opsi role
            const roleSelect = document.getElementById('edit-role');
            roleSelect.innerHTML = '<option value="">Pilih Role</option>'; // Reset options
            availableRoles.forEach(role => {
                const option = document.createElement('option');
                option.value = role;
                option.textContent = role.charAt(0).toUpperCase() + role.slice(1);
                if(roles.includes(role)){
                    option.selected = true;
                }
                roleSelect.appendChild(option);
            });

            // Menampilkan popup
            showPopup();
        });
    });

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
                showNotification('Error!', 'Nama, Username, dan Role harus diisi', 'error');
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
            fetch(`${window.AdminUser.routes.update}/${userId}`, { // Pastikan route sesuai
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
                if (data.success) {
                    showNotification('Success!', data.message, 'success');
                    // Reload halaman atau update tabel secara dinamis
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showNotification('Error!', data.message || 'Terjadi kesalahan.', 'error');
                }
            })
            .catch(error => {
                showNotification('Error!', error.message, 'error');
                console.error('Error:', error);
            });
        });
    }

    /*** Fitur Hapus Individual ***/
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
                    fetch(`${window.AdminUser.routes.destroy}/${userId}`, { // Pastikan route sesuai
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

    /*** Popup Handling ***/
    window.showPopup = function() {
        const popup = document.getElementById('popupOverlay');
        if (popup) {
            popup.style.display = 'flex';
            console.log('Popup displayed'); // Debug: Popup ditampilkan
        }
    }

    window.hidePopup = function(event) {
        if (event) {
            // Jika klik di luar konten popup
            if (event.target.id === 'popupOverlay') {
                const popup = document.getElementById('popupOverlay');
                popup.classList.add('hide');
                setTimeout(() => {
                    popup.style.display = 'none';
                    popup.classList.remove('hide');
                }, 300); // Durasi animasi
            }
        } else {
            // Jika tombol close diklik
            const popup = document.getElementById('popupOverlay');
            popup.classList.add('hide');
            setTimeout(() => {
                popup.style.display = 'none';
                popup.classList.remove('hide');
            }, 300); // Durasi animasi
        }
        // Reset form setelah ditutup
        const editUserForm = document.getElementById('edit-user-form');
        if (editUserForm) {
            editUserForm.reset();
        }
    }

    /*** Notification Handling ***/
    window.showNotification = function(title, message, type) {
        console.log(`Showing notification: ${title} - ${message} - ${type}`); // Debug: Notifikasi
        // Cek apakah elemen notifikasi sudah ada
        let notification = document.querySelector('.notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.classList.add('notification');
            document.body.appendChild(notification);
        }

        // Atur kelas berdasarkan tipe
        notification.className = 'notification show';
        if (type === 'success') {
            notification.classList.add('success');
        } else if (type === 'error') {
            notification.classList.add('error');
        }

        // Atur konten notifikasi
        notification.innerHTML = `<strong>${title}</strong><br>${message}`;

        // Hapus notifikasi setelah animasi selesai
        setTimeout(() => {
            notification.classList.remove('show');
        }, 4000); // Durasi total animasi
    }
});
