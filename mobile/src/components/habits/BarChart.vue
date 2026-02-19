<script setup>
import { onMounted, ref, watch, onUnmounted } from 'vue';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const props = defineProps({
  data: {
    type: Array,
    required: true
  },
  labels: {
    type: Array,
    required: true
  }
});

const chartCanvas = ref(null);
let chartInstance = null;

const renderChart = () => {
  if (chartInstance) {
    chartInstance.destroy();
  }

  const ctx = chartCanvas.value.getContext('2d');
  
  // Create background tracks data
  const backgroundData = props.data.map(() => 100);
  
  // Get CSS variable value for neutral-700
  const neutral700 = getComputedStyle(document.documentElement).getPropertyValue('--color-neutral-700').trim() || '#27272a';

  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: props.labels,
      datasets: [
        {
          // Main Data (Lima Accent)
          data: props.data,
          backgroundColor: '#7c3aed',
          borderRadius: 20,
          borderSkipped: false,
          barThickness: 28,
          categoryPercentage: 0.8,
          barPercentage: 0.9,
          grouped: false,
          order: 1
        },
        {
          // Background Tracks (Neutral-700)
          data: backgroundData,
          backgroundColor: neutral700,
          borderRadius: 20,
          borderSkipped: false,
          barThickness: 28,
          categoryPercentage: 0.8,
          barPercentage: 0.9,
          grouped: false,
          order: 2
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          enabled: true,
          filter: (tooltipItem) => tooltipItem.datasetIndex === 0,
          callbacks: {
            label: (context) => `${context.raw}%`
          }
        }
      },
      scales: {
        y: {
          display: false,
          beginAtZero: true,
          max: 100
        },
        x: {
          grid: {
            display: false,
            drawBorder: false
          },
          ticks: {
            color: '#a1a1aa', // var(--color-neutral-500)
            font: {
              size: 16,
              weight: '600'
            }
          }
        }
      }
    }
  });
};

onMounted(() => {
  renderChart();
});

watch(() => [props.data, props.labels], () => {
  renderChart();
}, { deep: true });

onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy();
  }
});
</script>

<template>
  <div class="chart-container">
    <canvas ref="chartCanvas" />
  </div>
</template>

<style scoped>
.chart-container {
  width: 100%;
  height: 200px;
  position: relative;
}
</style>
