 // Populate dropdown PBL otomatis dari database
        window.addEventListener("DOMContentLoaded", () => {
            const anggotaSelect = document.getElementById("anggotaFilter");

            // Daftar event sekali saja
            anggotaSelect.addEventListener("change", () => {
                const nim = anggotaSelect.value;
                if (!nim) return;

                fetch(`../Adminbackend/proggresbar_data.php?nim=${encodeURIComponent(nim)}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById("absentText").textContent = `${data.absent}%`;
                        document.getElementById("lateText").textContent = `${data.late}%`;
                        document.getElementById("presentText").textContent = `${data.present}%`;

                        document.getElementById("absentBar").style.width = `${data.absent}%`;
                        document.getElementById("absentBar").setAttribute("aria-valuenow", data.absent);

                        document.getElementById("lateBar").style.width = `${data.late}%`;
                        document.getElementById("lateBar").setAttribute("aria-valuenow", data.late);

                        document.getElementById("presentBar").style.width = `${data.present}%`;
                        document.getElementById("presentBar").setAttribute("aria-valuenow", data.present);
                    });
                     updateAllCharts();
            });

            // Setelah daftar event, baru load PBL awal
            fetch("../Adminbackend/pbl_option.php")
                .then(res => res.json())
                .then(pbls => {
                    const select = document.getElementById("pblFilter");
                    pbls.forEach(pbl => {
                        const opt = document.createElement("option");
                        opt.value = pbl;
                        opt.textContent = pbl;
                        select.appendChild(opt);
                    });

                    filterByPBL(); // Trigger pertama kali
                });
        });


        // Fungsi filter dan update progress bar + anggota
        function filterByPBL() {
            const pbl = document.getElementById("pblFilter").value;
            const anggotaContainer = document.getElementById("anggotaContainer");
            const anggotaSelect = document.getElementById("anggotaFilter");

            // Fetch data kehadiran
            fetch(`../Adminbackend/proggresbar_data.php?pbl=${encodeURIComponent(pbl)}`)
                .then(response => response.json())
                .then(data => {
                    // Update text
                    document.getElementById("absentText").textContent = `${data.absent}%`;
                    document.getElementById("lateText").textContent = `${data.late}%`;
                    document.getElementById("presentText").textContent = `${data.present}%`;

                    // Update progress bar
                    document.getElementById("absentBar").style.width = `${data.absent}%`;
                    document.getElementById("absentBar").setAttribute("aria-valuenow", data.absent);

                    document.getElementById("lateBar").style.width = `${data.late}%`;
                    document.getElementById("lateBar").setAttribute("aria-valuenow", data.late);

                    document.getElementById("presentBar").style.width = `${data.present}%`;
                    document.getElementById("presentBar").setAttribute("aria-valuenow", data.present);
                })
                .catch(err => {
                    console.error("Gagal ambil data absensi:", err);
                });

            // Fetch anggota jika bukan "Semua PBL"
            if (pbl === "") {
                anggotaContainer.style.display = "none";
                anggotaSelect.innerHTML = "";
                return;
            }

            fetch(`../Adminbackend/anggota_pbl.php?pbl=${encodeURIComponent(pbl)}`)
                .then(res => res.json())
                .then(anggotaList => {
                    anggotaSelect.innerHTML = "";
                    anggotaList.forEach(anggota => {
                        const opt = document.createElement("option");
                        opt.value = anggota.NIM;
                        opt.textContent = `${anggota.Nama} (${anggota.NIM})`;
                        anggotaSelect.appendChild(opt);
                    });
                    anggotaContainer.style.display = "block";
                    updateAllCharts();
                })
                .catch(err => {
                    console.error("Gagal ambil data anggota:", err);
                });
            anggotaSelect.addEventListener("change", () => {
                const nim = anggotaSelect.value;
                if (!nim) return;

                // Fetch data statistik KHUSUS anggota
                fetch(`../Adminbackend/proggresbar_data.php?nim=${encodeURIComponent(nim)}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById("absentText").textContent = `${data.absent}%`;
                        document.getElementById("lateText").textContent = `${data.late}%`;
                        document.getElementById("presentText").textContent = `${data.present}%`;

                        document.getElementById("absentBar").style.width = `${data.absent}%`;
                        document.getElementById("absentBar").setAttribute("aria-valuenow", data.absent);

                        document.getElementById("lateBar").style.width = `${data.late}%`;
                        document.getElementById("lateBar").setAttribute("aria-valuenow", data.late);

                        document.getElementById("presentBar").style.width = `${data.present}%`;
                        document.getElementById("presentBar").setAttribute("aria-valuenow", data.present);
                    });
            });

        }