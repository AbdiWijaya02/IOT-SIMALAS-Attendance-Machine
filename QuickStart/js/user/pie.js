 var ctx = document.getElementById("myPieChart").getContext('2d');
            var myPieChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["Present", "In Late", "Absent"],
                    datasets: [{
                        data: [0, 0, 0], // Akan diisi lewat fetch
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                        hoverBackgroundColor: ['#218838', '#e0a800', '#c82333'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)"
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 80,
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var label = data.labels[tooltipItem.index] || '';
                                var value = data.datasets[0].data[tooltipItem.index];
                                return label + ': ' + value + ' hari';
                            }
                        }
                    }
                }
            });

            function updateAttendanceUI() {
                // Pie Chart (jumlah hari)
                fetch("insertdata/Varbin_data/Absen.php")
                    .then(response => response.json())
                    .then(data => {
                        if (myPieChart) {
                            myPieChart.data.datasets[0].data = [
                                data.present,
                                data.late,
                                data.absent
                            ];
                            myPieChart.update();
                        }

                        // Tambahkan total jika mau
                        const total = data.present + data.late + data.absent;
                        document.getElementById("totalDays").textContent = `Total: ${total} hari`;
                    });

                // Progress Bar (persentase)
                fetch("insertdata/Varbin_data/Absen.php")
                    .then(response => response.json())
                    .then(data => {
                        document.querySelector(".progress-bar.bg-success").style.width = data.present + "%";
                        document.querySelector(".progress-bar.bg-warning").style.width = data.late + "%";
                        document.querySelector(".progress-bar.bg-danger").style.width = data.absent + "%";

                        const spans = document.querySelectorAll("h4.small span.float-right");
                        spans[0].textContent = data.absent + "%";
                        spans[1].textContent = data.late + "%";
                        spans[2].textContent = data.present + "%";
                    });
            }

            updateAttendanceUI();
            setInterval(updateAttendanceUI, 5000);