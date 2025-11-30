
  // Set default font SB Admin 2
  Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
  Chart.defaults.global.defaultFontColor = '#858796';

  // Ambil data dari PHP
  fetch("User/attendance_data.php")
    .then(response => response.json())
    .then(data => {
      const labels = [];
      const values = [];
      const backgroundColors = [];
      const hoverColors = [];

      if (data.present > 0) {
        labels.push("Present");
        values.push(data.present);
        backgroundColors.push('#28a745');
        hoverColors.push('#218838');
      }
      if (data.in_late > 0) {
        labels.push("In Late");
        values.push(data.in_late);
        backgroundColors.push('#ffc107');
        hoverColors.push('#e0a800');
      }
      if (data.absent > 0) {
        labels.push("Absent");
        values.push(data.absent);
        backgroundColors.push('#dc3545');
        hoverColors.push('#c82333');
      }

      var ctx = document.getElementById("myPieChart");
      var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: values,
            backgroundColor: backgroundColors,
            hoverBackgroundColor: hoverColors,
            hoverBorderColor: "rgba(234, 236, 244, 1)",
          }],
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
            display: false
          },
          cutoutPercentage: 80,
        },
      });

      // Tampilkan total hari di bawah chart
      document.getElementById("totalDays").innerText = `Total: ${data.total} hari`;
    });

