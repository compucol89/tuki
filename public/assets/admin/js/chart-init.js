'use strict';

/* ─────────────────────────────────────────────────────────────────────
   Brand palette — coherente con admin-skin.css
   chart1: orange  #f97316  — ingresos eventos
   chart2: indigo  #6366f1  — bookings eventos
   chart3: emerald #10b981  — ingresos productos
   chart4: blue    #3b82f6  — pedidos productos

   Colores de texto/grid adaptados al tema (data-theme) para cumplir
   WCAG 1.4.3 / 1.4.11 en dark y light.
   ───────────────────────────────────────────────────────────────────── */

function tukiChartPalette() {
  var dark = document.documentElement.dataset.theme === 'dark';
  return {
    dark: dark,
    tick: dark ? '#c8cdd6' : '#6b7280',
    legend: dark ? '#c8cdd6' : '#6b7280',
    grid: dark ? 'rgba(255,255,255,.10)' : 'rgba(0,0,0,.08)',
    zeroGrid: dark ? 'rgba(255,255,255,.10)' : 'rgba(0,0,0,.08)',
    pointBorder: dark ? '#171e2b' : '#ffffff'
  };
}

function tukiInitLineChart(id, label, getData, border, fill, opts) {
  var canvas = document.getElementById(id);
  if (!canvas) return null;
  var data = typeof getData === 'function' ? getData() : getData;
  var p = tukiChartPalette();
  var options = {
    responsive: true,
    maintainAspectRatio: false,
    legend: {
      position: 'bottom',
      labels: { padding: 10, fontColor: p.legend, fontSize: 12, fontFamily: 'Inter' }
    },
    tooltips: {
      bodySpacing: 4, mode: 'nearest', intersect: 0,
      position: 'nearest', xPadding: 10, yPadding: 10, caretPadding: 10,
      titleFontFamily: 'Inter', bodyFontFamily: 'IBM Plex Mono', bodyFontStyle: 'normal'
    },
    layout: { padding: { left: 15, right: 15, top: 15, bottom: 15 } },
    scales: {
      xAxes: [{ gridLines: { color: p.grid }, ticks: { fontColor: p.tick, fontSize: 11, fontFamily: 'Inter' } }],
      yAxes: [{ gridLines: { color: p.grid }, ticks: { fontColor: p.tick, fontSize: 11, fontFamily: 'IBM Plex Mono' } }]
    }
  };
  if (opts && opts.stepSize) {
    options.scales.yAxes[0].ticks.stepSize = opts.stepSize;
  }
  var chart = new Chart(canvas.getContext('2d'), {
    type: 'line',
    data: {
      labels: monthArr,
      datasets: [{
        label: label,
        data: data,
        borderColor: border,
        pointBorderColor: p.pointBorder,
        pointBackgroundColor: border,
        pointBorderWidth: 2,
        pointHoverRadius: 5,
        pointHoverBorderWidth: 1,
        pointRadius: 4,
        backgroundColor: fill,
        fill: true,
        borderWidth: 2.5
      }]
    },
    options: options
  });

  // Guard anti-reinit: el mismo canvas no debe recibir dos instancias.
  // Reutilizable para re-theme en caliente (tukiRethemeCharts).
  if (!window.__tukiCharts) window.__tukiCharts = {};
  window.__tukiCharts[id] = chart;

  return chart;
}

/* Re-theme en caliente: actualiza colores de texto/grid/puntos según el
   tema actual SIN recrear el chart (evita duplicar instancias). */
function tukiRethemeCharts() {
  var p = tukiChartPalette();
  Object.keys(window.__tukiCharts || {}).forEach(function (id) {
    var chart = window.__tukiCharts[id];
    if (!chart || !chart.options) return;
    var ds = chart.data.datasets[0];
    if (ds) ds.pointBorderColor = p.pointBorder;
    if (chart.options.legend && chart.options.legend.labels) {
      chart.options.legend.labels.fontColor = p.legend;
    }
    if (chart.options.scales) {
      (chart.options.scales.xAxes || []).forEach(function (axis) {
        axis.ticks.fontColor = p.tick;
        axis.gridLines.color = p.grid;
      });
      (chart.options.scales.yAxes || []).forEach(function (axis) {
        axis.ticks.fontColor = p.tick;
        axis.gridLines.color = p.grid;
      });
    }
    chart.update();
  });
}

/* Reaccionar al toggle de theme del panel (sin duplicar instancias) */
document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('[data-theme-toggle-panel]');
  if (toggle) {
    toggle.addEventListener('click', function () {
      setTimeout(tukiRethemeCharts, 50);
    });
  }
});

tukiInitLineChart('incomeChart', 'Ingresos mensuales', function () { return incomeArr; }, '#f97316', 'rgba(249,115,22,.08)');
tukiInitLineChart('TotalEventBookingChart', 'Reservas mensuales', function () { return totalBookings; }, '#6366f1', 'rgba(99,102,241,.08)', { stepSize: 1 });
tukiInitLineChart('ProductOrderChart', 'Ingresos mensuales', function () { return productIncome; }, '#10b981', 'rgba(16,185,129,.08)');
tukiInitLineChart('TotalProductOrderChart', 'Pedidos mensuales', function () { return totalOders; }, '#3b82f6', 'rgba(59,130,246,.08)', { stepSize: 1 });
