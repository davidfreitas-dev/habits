<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

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

const getCSSVar = (name, fallback) => {
  return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || fallback;
};

const chartData = computed(() => {
  const primaryColor = getCSSVar('--color-primary', '#7c3aed');
  const neutral700 = getCSSVar('--color-neutral-700', '#27272a');

  return {
    labels: props.labels,
    datasets: [
      {
        // Main Data
        data: props.data,
        backgroundColor: primaryColor,
        borderRadius: 20,
        borderSkipped: false,
        barThickness: 28,
        categoryPercentage: 0.8,
        barPercentage: 0.9,
        grouped: false,
        order: 1,
        animation: {
          duration: 1000,
          easing: 'easeOutQuart'
        }
      },
      {
        // Background Tracks
        data: props.data.map(() => 100),
        backgroundColor: neutral700,
        borderRadius: 20,
        borderSkipped: false,
        barThickness: 28,
        categoryPercentage: 0.8,
        barPercentage: 0.9,
        grouped: false,
        order: 2,
        animation: {
          duration: 0
        }
      }
    ]
  };
});

const chartOptions = computed(() => {
  const neutral500 = getCSSVar('--color-neutral-500', '#a1a1aa');

  return {
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
          display: false
        },
        border: {
          display: false
        },
        ticks: {
          color: neutral500,
          font: {
            size: 16,
            weight: '600'
          }
        }
      }
    }
  };
});
</script>

<template>
  <div class="chart-container">
    <Bar
      :options="chartOptions"
      :data="chartData"
    />
  </div>
</template>

<style scoped>
.chart-container {
  width: 100%;
  height: 200px;
  position: relative;
}
</style>
