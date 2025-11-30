 document.getElementById("generateReport").addEventListener("click", () => {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF('potrait'); // Landscape

                doc.text("Rekab Absensi", 14, 15);

                // Ambil tabel dan ubah jadi PDF
                doc.autoTable({
                    html: '#dataTable',
                    startY: 20,
                    styles: {
                        fontSize: 10,
                        cellPadding: 3
                    },
                    headStyles: {
                        fillColor: [41, 128, 185]
                    }
                });

                doc.save("Attendance Record.pdf");
            });