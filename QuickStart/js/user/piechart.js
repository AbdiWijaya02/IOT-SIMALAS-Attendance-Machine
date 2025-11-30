document.addEventListener("DOMContentLoaded", function() {
            fetch("User/attendance_data.php") // Atur path sesuai posisi file JS/HTML kamu
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById("myPieChart").getContext("2d");
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Present', 'In Late', 'Absent'],
                            datasets: [{
                                data: [data.present, data.in_late, data.absent],
                                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                                hoverBackgroundColor: ['#17a673', '#dda20a', '#be2617'],
                                hoverBorderColor: "rgba(234, 236, 244, 1)",
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            tooltips: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyFontColor: "#858796",
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                xPadding: 15,
                                yPadding: 15,
                                displayColors: false,
                                caretPadding: 10,
                            },
                            legend: {
                                display: false // kamu sudah pakai legend manual di bawah canvas
                            },
                            cutoutPercentage: 60,
                        }
                    });

                    // Tampilkan total hari
                    document.getElementById("totalDays").innerText = `Total: ${data.total} hari`;
                });
        });