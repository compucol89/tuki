'use strict';

/* ─────────────────────────────────────────────────────────────────────
   Brand palette — coherente con admin-skin.css
   chart1: orange  #f97316  — ingresos eventos
   chart2: indigo  #6366f1  — bookings eventos
   chart3: emerald #10b981  — ingresos productos
   chart4: blue    #3b82f6  — pedidos productos
───────────────────────────────────────────────────────────────────── */

const chartOne = document.getElementById('incomeChart').getContext('2d');
const myIncomeChart = new Chart(chartOne, {
  type: 'line',
  data: {
    labels: monthArr,
    datasets: [{
      label: 'Monthly Income',
      data: incomeArr,
      borderColor: '#f97316',
      pointBorderColor: '#fff',
      pointBackgroundColor: '#f97316',
      pointBorderWidth: 2,
      pointHoverRadius: 5,
      pointHoverBorderWidth: 1,
      pointRadius: 4,
      backgroundColor: 'rgba(249,115,22,.08)',
      fill: true,
      borderWidth: 2.5
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    legend: {
      position: 'bottom',
      labels: { padding: 10, fontColor: '#6b7280', fontSize: 12 }
    },
    tooltips: {
      bodySpacing: 4, mode: 'nearest', intersect: 0,
      position: 'nearest', xPadding: 10, yPadding: 10, caretPadding: 10
    },
    layout: { padding: { left: 15, right: 15, top: 15, bottom: 15 } },
    scales: {
      xAxes: [{ gridLines: { color: 'rgba(0,0,0,.04)' }, ticks: { fontColor: '#9ca3af', fontSize: 11 } }],
      yAxes: [{ gridLines: { color: 'rgba(0,0,0,.04)' }, ticks: { fontColor: '#9ca3af', fontSize: 11 } }]
    }
  }
});

const chartTwo = document.getElementById('TotalEventBookingChart').getContext('2d');
const myEventBookingChart = new Chart(chartTwo, {
  type: 'line',
  data: {
    labels: monthArr,
    datasets: [{
      label: 'Monthly Event Bookings',
      data: totalBookings,
      borderColor: '#6366f1',
      pointBorderColor: '#fff',
      pointBackgroundColor: '#6366f1',
      pointBorderWidth: 2,
      pointHoverRadius: 5,
      pointHoverBorderWidth: 1,
      pointRadius: 4,
      backgroundColor: 'rgba(99,102,241,.08)',
      fill: true,
      borderWidth: 2.5
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    legend: {
      position: 'bottom',
      labels: { padding: 10, fontColor: '#6b7280', fontSize: 12 }
    },
    tooltips: {
      bodySpacing: 4, mode: 'nearest', intersect: 0,
      position: 'nearest', xPadding: 10, yPadding: 10, caretPadding: 10
    },
    layout: { padding: { left: 15, right: 15, top: 15, bottom: 15 } },
    scales: {
      xAxes: [{ gridLines: { color: 'rgba(0,0,0,.04)' }, ticks: { fontColor: '#9ca3af', fontSize: 11 } }],
      yAxes: [{ gridLines: { color: 'rgba(0,0,0,.04)' }, ticks: { stepSize: 1, fontColor: '#9ca3af', fontSize: 11 } }]
    }
  }
});

const chartThree = document.getElementById('ProductOrderChart').getContext('2d');
const ProductOrderChart = new Chart(chartThree, {
  type: 'line',
  data: {
    labels: monthArr,
    datasets: [{
      label: 'Monthly Income',
      data: productIncome,
      borderColor: '#10b981',
      pointBorderColor: '#fff',
      pointBackgroundColor: '#10b981',
      pointBorderWidth: 2,
      pointHoverRadius: 5,
      pointHoverBorderWidth: 1,
      pointRadius: 4,
      backgroundColor: 'rgba(16,185,129,.08)',
      fill: true,
      borderWidth: 2.5
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    legend: {
      position: 'bottom',
      labels: { padding: 10, fontColor: '#6b7280', fontSize: 12 }
    },
    tooltips: {
      bodySpacing: 4, mode: 'nearest', intersect: 0,
      position: 'nearest', xPadding: 10, yPadding: 10, caretPadding: 10
    },
    layout: { padding: { left: 15, right: 15, top: 15, bottom: 15 } },
    scales: {
      xAxes: [{ gridLines: { color: 'rgba(0,0,0,.04)' }, ticks: { fontColor: '#9ca3af', fontSize: 11 } }],
      yAxes: [{ gridLines: { color: 'rgba(0,0,0,.04)' }, ticks: { fontColor: '#9ca3af', fontSize: 11 } }]
    }
  }
});

const chartFour = document.getElementById('TotalProductOrderChart').getContext('2d');
const TotalProductOrderChart = new Chart(chartFour, {
  type: 'line',
  data: {
    labels: monthArr,
    datasets: [{
      label: 'Monthly Product Order',
      data: totalOders,
      borderColor: '#3b82f6',
      pointBorderColor: '#fff',
      pointBackgroundColor: '#3b82f6',
      pointBorderWidth: 2,
      pointHoverRadius: 5,
      pointHoverBorderWidth: 1,
      pointRadius: 4,
      backgroundColor: 'rgba(59,130,246,.08)',
      fill: true,
      borderWidth: 2.5
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    legend: {
      position: 'bottom',
      labels: { padding: 10, fontColor: '#6b7280', fontSize: 12 }
    },
    tooltips: {
      bodySpacing: 4, mode: 'nearest', intersect: 0,
      position: 'nearest', xPadding: 10, yPadding: 10, caretPadding: 10
    },
    layout: { padding: { left: 15, right: 15, top: 15, bottom: 15 } },
    scales: {
      xAxes: [{ gridLines: { color: 'rgba(0,0,0,.04)' }, ticks: { fontColor: '#9ca3af', fontSize: 11 } }],
      yAxes: [{ gridLines: { color: 'rgba(0,0,0,.04)' }, ticks: { stepSize: 1, fontColor: '#9ca3af', fontSize: 11 } }]
    }
  }
});
