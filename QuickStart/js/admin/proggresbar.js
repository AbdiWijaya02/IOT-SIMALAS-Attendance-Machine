document.addEventListener("DOMContentLoaded", function() {
            fetch("../Adminbackend/proggresbar_data.php")
                .then(response => response.json())
                .then(data => {
                    // Update Absent
                    document.getElementById("absentText").innerText = data.absent + "%";
                    document.getElementById("absentBar").style.width = data.absent + "%";
                    document.getElementById("absentBar").setAttribute("aria-valuenow", data.absent);

                    // Update In Late
                    document.getElementById("lateText").innerText = data.late + "%";
                    document.getElementById("lateBar").style.width = data.late + "%";
                    document.getElementById("lateBar").setAttribute("aria-valuenow", data.late);

                    // Update Present
                    document.getElementById("presentText").innerText = data.present + "%";
                    document.getElementById("presentBar").style.width = data.present + "%";
                    document.getElementById("presentBar").setAttribute("aria-valuenow", data.present);
                })
                .catch(error => {
                    console.error("Gagal mengambil data presentasi kehadiran:", error);
                });
        });