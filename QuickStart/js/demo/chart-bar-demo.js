let myBarChart;

document.addEventListener("DOMContentLoaded", function () {
  const filterType = document.getElementById("filterType");
  const filterValue = document.getElementById("filterValue");
  const filterValue2 = document.getElementById("filterValue2");

  // Fetch opsi filter
  function updateFilterOptions() {
    const selectedType = filterType.value;
    filterValue.innerHTML = '<option value="">Semua</option>';

    let url = "";
    if (selectedType === "PBL") {
      url = "insertdata/Varbin_data/pbl_option.php";
    } else if (selectedType === "Angkatan") {
      url = "insertdata/Varbin_data/angkatan_option.php";
    }

    if (url) {
      fetch(url)
        .then(res => res.json())
        .then(options => {
          options.forEach(opt => {
            const option = document.createElement("option");
            option.value = opt;
            option.textContent = opt;
            filterValue.appendChild(option);
          });
        });
    }

    // Reset nama juga
    updateNamaOptions();
  }

  function updateNamaOptions() {
    const selectedType = filterType.value;
    const selectedVal = filterValue.value;

    let url = `insertdata/Varbin_data/nama_option.php?filter_by=${selectedType}&filter_value=${selectedVal}`;
    filterValue2.innerHTML = '<option value="">Semua Nama</option>';

    fetch(url)
      .then(res => res.json())
      .then(options => {
        options.forEach(opt => {
          const option = document.createElement("option");
          option.value = opt;
          option.textContent = opt;
          filterValue2.appendChild(option);
        });
      });
  }

  function loadChart() {
    const selectedType = filterType.value;
    const selectedVal = filterValue.value;
    const selectedNama = filterValue2.value;

    let url = "insertdata/Varbin_data/get_chart_data.php";
    let params = [];

    if (selectedType && selectedVal) {
      params.push(`filter_by=${selectedType}`);
      params.push(`filter_value=${encodeURIComponent(selectedVal)}`);
    }
    if (selectedNama) {
      params.push(`nama=${encodeURIComponent(selectedNama)}`);
    }

    if (params.length > 0) {
      url += "?" + params.join("&");
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
                title: { display: true, text: "Bulan" }
              },
              y: {
                beginAtZero: true,
                suggestedMax: 10,
                title: { display: true, text: "Jumlah Kehadiran" }
              }
            },
            plugins: {
              legend: { position: "top" },
              title: {
                display: true,
                text: "Grafik Kehadiran Bulanan"
              }
            }
          }
        });
      });
  }

  filterType.addEventListener("change", () => {
    updateFilterOptions();
    loadChart();
  });

  filterValue.addEventListener("change", () => {
    updateNamaOptions();
    loadChart();
  });

  filterValue2.addEventListener("change", loadChart);

  updateFilterOptions();
  loadChart();
});
