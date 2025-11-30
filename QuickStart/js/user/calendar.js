let attendanceMap = {}; // {"2025-07-01": "Present", ...}
            fetch("User/calendar.php")
                .then(response => response.json())
                .then(data => {
                    attendanceMap = data;
                    generateCalendar(parseInt(monthSelect.value), parseInt(yearSelect.value));
                });


            const monthSelect = document.getElementById("monthSelect");
            const yearSelect = document.getElementById("yearSelect");
            const calendarBody = document.getElementById("calendarBody");

            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            function generateDropdowns() {
                // Isi bulan
                monthNames.forEach((month, index) => {
                    let option = document.createElement("option");
                    option.value = index;
                    option.text = month;
                    monthSelect.appendChild(option);
                });

                // Isi tahun dari 2020 sampai 2030
                for (let year = 2020; year <= 2030; year++) {
                    let option = document.createElement("option");
                    option.value = year;
                    option.text = year;
                    yearSelect.appendChild(option);
                }
            }

            function generateCalendar(month, year) {
                calendarBody.innerHTML = ""; // Kosongkan isi sebelumnya
                const today = new Date();

                const firstDay = new Date(year, month, 1).getDay();
                const totalDays = new Date(year, month + 1, 0).getDate();

                let date = 1;
                for (let i = 0; i < 6; i++) {
                    let row = document.createElement("tr");

                    for (let j = 0; j < 7; j++) {
                        let cell = document.createElement("td");
                        if (i === 0 && j < firstDay) {
                            cell.innerHTML = "";
                        } else if (date > totalDays) {
                            break;
                        } else {
                            cell.innerHTML = date;

                            // Format jadi YYYY-MM-DD
                            const formattedDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;

                            // Tandai warna berdasarkan status
                            const status = attendanceMap[formattedDate];
                            if (status === "Present") {
                                cell.classList.add("bg-success", "text-white");
                            } else if (status === "In Late") {
                                cell.classList.add("bg-warning", "text-dark");
                            } else if (status === "Absent") {
                                cell.classList.add("bg-danger", "text-white");
                            }

                            // Tandai hari ini
                            if (
                                date === today.getDate() &&
                                month === today.getMonth() &&
                                year === today.getFullYear()
                            ) {
                                cell.classList.add("border", "border-primary", "font-weight-bold");
                            }

                            date++;
                        }

                        row.appendChild(cell);
                    }
                    calendarBody.appendChild(row);
                }
            }

            // Event saat user memilih bulan/tahun
            monthSelect.addEventListener("change", () => {
                generateCalendar(parseInt(monthSelect.value), parseInt(yearSelect.value));
            });
            yearSelect.addEventListener("change", () => {
                generateCalendar(parseInt(monthSelect.value), parseInt(yearSelect.value));
            });

            // Inisialisasi awal
            const today = new Date();
            generateDropdowns();
            monthSelect.value = today.getMonth();
            yearSelect.value = today.getFullYear();
            generateCalendar(today.getMonth(), today.getFullYear());