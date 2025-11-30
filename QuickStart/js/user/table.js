 function loadAbsenHTML() {
                fetch("insertdata/Varbin_data/absen_rows.php")
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById("absen-body").innerHTML = html;
                    })
                    .catch(err => {
                        document.getElementById("absen-body").innerHTML = "<tr><td colspan='6'>Gagal memuat data</td></tr>";
                        console.error("Gagal AJAX:", err);
                    });
            }

            loadAbsenHTML(); // Jalankan saat halaman dimuat
            setInterval(loadAbsenHTML, 5000); // Ulangi setiap 5 detik (opsional)