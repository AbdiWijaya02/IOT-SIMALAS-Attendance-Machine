let myBarChart;

document.addEventListener("DOMContentLoaded", function () {
  const filterType = document.getElementById("filterType");
  const filterValue1 = document.getElementById("filterValue1");
  const filterValue2 = document.getElementById("filterValue2");

  // 🔄 Ambil opsi PBL / Angkatan berdasarkan filterType
  filterType.addEventListener("change", function () {
    const type = this.value;
    filterValue1.innerHTML = '<option value="">Semua</option>';
    filterValue2.innerHTML = '<option value="">Semua Nama</option>';

    let url = "";
    if (type === "PBL") {
      url = "../insertdata/Varbin_data/pbl_option.php";
    } else if (type === "Angkatan") {
      url = "../insertdata/Varbin_data/angkatan_option.php";
    }

    if (url) {
      fetch(url)
        .then(res => res.json())
        .then(options => {
          options.forEach(opt => {
            const option = document.createElement("option");
            option.value = opt;
            option.textContent = opt;
            filterValue1.appendChild(option);
          });
        })
        .catch(err => console.error("Gagal ambil filter utama:", err));
    }

    updateAttendanceChart(); // Load grafik default
  });

  // 🔄 Ambil daftar nama setelah pilih Angkatan / PBL
  filterValue1.addEventListener("change", function () {
    const type = filterType.value;
    const value = this.value;
    filterValue2.innerHTML = '<option value="">Semua Nama</option>';

    if (type && value) {
      let url = `../insertdata/Varbin_data/nama_option.php?filter_by=${type}&filter_value=${encodeURIComponent(value)}`;
      fetch(url)
        .then(res => res.json())
        .then(namaList => {
          namaList.forEach(nama => {
            const opt = document.createElement("option");
            opt.value = nama;
            opt.textContent = nama;
            filterValue2.appendChild(opt);
          });
        })
        .catch(err => console.error("Gagal ambil nama:", err));
    }

    updateAttendanceChart(); // Refresh grafik juga
  });

  // 🔁 Ketika filter nama diubah
  filterValue2.addEventListener("change", updateAttendanceChart);

  // 🔄 Fungsi load/update grafik
  function updateAttendanceChart() {
    const type = filterType.value;
    const val1 = filterValue1.value;
    const val2 = filterValue2.value;

    let url = "../insertdata/Varbin_data/get_chart_data.php";
    const params = new URLSearchParams();

    if (type && val1) {
      params.append("filter_by", type);
      params.append("filter_value", val1);
    }
    if (val2) {
      params.append("nama", val2);
    }

    if ([...params].length > 0) {
      url += "?" + params.toString();
    }

    fetch(url)
      .then(response => response.json())
      .then(data => {
        const ctx = document.getElementById("myBarChart").getContext("2d");

        if (myBarChart) {
          myBarChart.destroy();
        }

        myBarChart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: data.labels,
            datasets: [
              {
                label: "Absen",
                backgroundColor: "#e74a3b",
                data: data.absen
              },
              {
                label: "Terlambat",
                backgroundColor: "#f6c23e",
                data: data.inlate
              },
              {
                label: "Hadir Tepat Waktu",
                backgroundColor: "#1cc88a",
                data: data.present
              }
            ]
          },
          options: {
            responsive: true,
            scales: {
              x: {
                title: {
                  display: true,
                  text: "Bulan"
                }
              },
              y: {
                beginAtZero: true,
                suggestedMax: 10,
                title: {
                  display: true,
                  text: "Jumlah Kehadiran"
                }
              }
            },
            plugins: {
              legend: {
                position: "top"
              },
              title: {
                display: true,
                text: "Grafik Kehadiran Bulanan"
              }
            }
          }
        });
      })
      .catch(err => console.error("Gagal ambil data grafik:", err));
  }

  // Load awal grafik
  updateAttendanceChart();
});
filterValue1.addEventListener("change", function () {
  const type = filterType.value;
  const value1 = this.value;
  filterValue2.innerHTML = '<option value="">Semua Nama</option>';

  if (type && value1) {
    let url = "../insertdata/Varbin_data/nama_option.php";
    url += `?filter_by=${type}&filter_value=${encodeURIComponent(value1)}`;

    fetch(url)
      .then(res => res.json())
      .then(namaList => {
        namaList.forEach(nama => {
          const opt = document.createElement("option");
          opt.value = nama;
          opt.textContent = nama;
          filterValue2.appendChild(opt);
        });
      });
  }

  updateAttendanceChart();
});
