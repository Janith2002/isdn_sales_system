/* ================= ORDER STATUS (PIE) ================= */
const orderStatusChart = new Chart(
  document.getElementById('orderStatusChart'),
  {
    type: 'pie',
    data: {
      labels: ['Pending', 'Delivered'],
      datasets: [{
        data: [12, 30],
        backgroundColor: ['#ffb703', '#ff7a45'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  }
);

/* ================= ORDER TREND (LINE) ================= */
const orderTrendChart = new Chart(
  document.getElementById('orderTrendChart'),
  {
    type: 'line',
    data: {
      labels: ['Mon','Tue','Wed','Thu','Fri'],
      datasets: [{
        label: 'Orders',
        data: [5,10,7,12,9],
        borderColor: '#ff7a45',
        backgroundColor: 'rgba(255,122,69,0.2)',
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointBackgroundColor: '#ff7a45'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { precision: 0 }
        }
      }
    }
  }
);

/* ================= REVENUE (BAR) ================= */
const revenueChart = new Chart(
  document.getElementById('revenueChart'),
  {
    type: 'bar',
    data: {
      labels: ['Jan','Feb','Mar','Apr'],
      datasets: [{
        label: 'Revenue',
        data: [120000,150000,170000,200000],
        backgroundColor: '#ff7a45',
        borderRadius: 10
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: value => 'LKR ' + value.toLocaleString()
          }
        }
      }
    }
  }
);

/* ================= FORCE RESIZE (SIDEBAR / MOBILE) ================= */
window.addEventListener('resize', () => {
  orderStatusChart.resize();
  orderTrendChart.resize();
  revenueChart.resize();
});
