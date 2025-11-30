
let myChart = null;

function fetchChartData(metric) {
  fetch(`Adminbackend/rata_rata.php?metric=${metric}`)
    .then(res => res.json())
    .then(response => {
      if (response.status === "success") {
        const ctx = document.getElementById("myAreaChart").getContext('2d');
        const labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        const chartData = {
          labels: labels,
          datasets: [{
            label: "Rata-rata " + formatLabel(metric),
            data: response.data,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2
          }]
        };

        const chartOptions = {
          maintainAspectRatio: false,
          layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
          scales: {
            xAxes: [{
              time: { unit: 'month' },
              gridLines: { display: false, drawBorder: false },
              ticks: { maxTicksLimit: 12 }
            }],
            yAxes: [{
              ticks: {
                maxTicksLimit: 6,
                padding: 10,
                callback: function(value) {
                  return value + ' jam';
                }
              },
              gridLines: {
                color: "rgb(234, 236, 244)",
                zeroLineColor: "rgb(234, 236, 244)",
                drawBorder: false,
                borderDash: [2],
                zeroLineBorderDash: [2]
              }
            }]
          },
          legend: { display: false },
          tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
              label: function(tooltipItem) {
                return 'Rata-rata: ' + tooltipItem.yLabel + ' jam';
              }
            }
          }
        };

        if (myChart) {
          myChart.data = chartData;
          myChart.options = chartOptions;
          myChart.update();
        } else {
          myChart = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: chartOptions
          });
        }
      } else {
        console.error("Gagal ambil data: ", response.message);
      }
    })
    .catch(err => console.error("Fetch error:", err));
}

// Utility buat label bagus
function formatLabel(metric) {
  if (metric === "jam_masuk") return "Jam Masuk";
  if (metric === "jam_pulang") return "Jam Pulang";
  if (metric === "durasi_jam") return "Durasi Jam";
  return metric;
}

// Panggil pertama kali default jam_masuk
fetchChartData("jam_masuk");

// Saat dropdown berubah
document.getElementById("metricSelect").addEventListener("change", function () {
  fetchChartData(this.value);
});

