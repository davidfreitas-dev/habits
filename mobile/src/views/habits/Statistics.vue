<script setup>
import { IonPage, IonContent } from '@ionic/vue';
import { ref, onMounted, computed } from 'vue';
import { useHabitStore } from '@/stores/habits';
import { useLoading } from '@/composables/useLoading';
import Heading from '@/components/layout/Heading.vue';
import Container from '@/components/layout/Container.vue';
import BarChart from '@/components/habits/BarChart.vue';
import dayjs from 'dayjs';
import 'dayjs/locale/pt-br';

dayjs.locale('pt-br');

const periods = [
  { label: 'W', value: 'W' },
  { label: 'M', value: 'M' },
  { label: '3M', value: '3M' },
  { label: '6M', value: '6M' },
  { label: 'Y', value: 'Y' }
];

const activeLabel = ref('W');
const statsData = ref([]);
const habitStore = useHabitStore();
const { withLoading } = useLoading();

const chartLabels = computed(() => {
  if (statsData.value.length === 0) return [];
  
  if (activeLabel.value === 'W' || activeLabel.value === 'D') {
    return statsData.value.map(day => dayjs(day.date).format('dd').charAt(0).toUpperCase());
  }
  
  if (statsData.value.length > 30) {
    // Show fewer labels for longer periods
    return statsData.value.map((day, index) => {
      return index % Math.floor(statsData.value.length / 5) === 0 ? dayjs(day.date).format('MMM') : '';
    });
  }

  return statsData.value.map(day => dayjs(day.date).format('DD/MM'));
});

const chartValues = computed(() => {
  return statsData.value.map(day => day.percentage || 0);
});

const averagePercentage = computed(() => {
  if (statsData.value.length === 0) return 0;
  const sum = statsData.value.reduce((acc, day) => acc + (day.percentage || 0), 0);
  return Math.round(sum / statsData.value.length);
});

const handlePeriodChange = async (period) => {
  activeLabel.value = period.label;
  await withLoading(async () => {
    const result = await habitStore.getHabitStats(period.value);
    statsData.value = result.data;
  }, 'Erro ao carregar estatísticas');
};

onMounted(async () => {
  await withLoading(async () => {
    const result = await habitStore.getHabitStats('W');
    statsData.value = result.data;
  }, 'Erro ao carregar estatísticas');
});
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" class="vertical-safe-area">
      <Container>
        <Heading title="Estatísticas" />

        <!-- Period Selector -->
        <div class="period-selector">
          <button 
            v-for="period in periods" 
            :key="period.label"
            :class="['period-btn', { active: activeLabel === period.label }]"
            @click="handlePeriodChange(period)"
          >
            {{ period.label }}
          </button>
        </div>

        <div class="stats-container">
          <!-- Chart Card -->
          <div class="chart-card">
            <div class="chart-header">
              <span class="chart-title">Total Activities</span>
              <h2 class="chart-percentage">
                {{ averagePercentage }}%
              </h2>
            </div>
            
            <BarChart 
              v-if="statsData.length > 0"
              :data="chartValues" 
              :labels="chartLabels" 
            />
          </div>
        </div>
      </Container>
    </ion-content>
  </ion-page>
</template>

<style scoped>
.period-selector {
  display: flex;
  justify-content: space-between;
  background: var(--color-background-secondary);
  padding: 0;
  border-radius: 100px;
  margin-bottom: 24px;
  margin-top: 16px;
}

.period-btn {
  background: transparent;
  border: none;
  color: var(--color-text-secondary);
  font-weight: 600;
  font-size: 14px;
  width: 52px;
  height: 52px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.period-btn.active {
  background: var(--color-primary);
  color: #ffffff;
}

.chart-card {
  background: var(--color-background-secondary);
  border-radius: 24px;
  padding: 24px;
  color: var(--color-text-primary);
}

.chart-header {
  margin-bottom: 24px;
}

.chart-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text-secondary);
}

.chart-percentage {
  font-size: 32px;
  font-weight: 800;
  margin: 4px 0 0 0;
  color: var(--color-text-primary);
}

.stats-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
</style>
