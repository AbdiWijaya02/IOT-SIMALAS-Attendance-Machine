 document.getElementById("generateReport").addEventListener("click", () => {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('landscape'); // Landscape

            doc.text("Laporan Data Barang", 14, 15);

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

            doc.save("laporan_barang.pdf");
        });