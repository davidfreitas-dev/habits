<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

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

const themeVersion = ref(0);

const getCSSVar = (name, fallback) => getComputedStyle(document.body).getPropertyValue(name).trim() || fallback;

const BAR_DEFAULTS = {
  borderRadius: 20,
  borderSkipped: false,
  barThickness: 28,
  categoryPercentage: 0.8,
  barPercentage: 0.9,
  grouped: false,
};

let observer;

onMounted(() => {
  observer = new MutationObserver(() => themeVersion.value++);
  observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
});

onUnmounted(() => observer?.disconnect());

const chartData = computed(() => {
  themeVersion.value;

  return {
    labels: props.labels,
    datasets: [
      {
        ...BAR_DEFAULTS,
        data: props.data,
        backgroundColor: getCSSVar('--color-primary'),
        order: 1,
        animation: { duration: 1000, easing: 'easeOutQuart' }
      },
      {
        ...BAR_DEFAULTS,
        data: props.data.map(() => 100),
        backgroundColor: getCSSVar('--color-border-default'),
        order: 2,
        animation: { duration: 0 }
      }
    ]
  };
});

const chartOptions = computed(() => {
  themeVersion.value;

  return {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        enabled: true,
        filter: (item) => item.datasetIndex === 0,
        callbacks: { label: (ctx) => `${ctx.raw}%` }
      }
    },
    scales: {
      y: { display: false, beginAtZero: true, max: 100 },
      x: {
        grid: { display: false },
        border: { display: false },
        ticks: {
          color: getCSSVar('--color-neutral-500', '#a1a1aa'),
          font: { size: 16, weight: '600' }
        }
      }
    }
  };
});
</script>

<template>
  <div class="chart-container">
    <Bar
      :key="themeVersion"
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