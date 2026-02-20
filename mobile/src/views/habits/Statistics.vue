<script setup>
import { IonPage, IonContent, onIonViewWillEnter, onIonViewDidLeave } from '@ionic/vue';
import { ref, computed, watch } from 'vue';
import { useHabitStore } from '@/stores/habits';
import { useLoading } from '@/composables/useLoading';
import Heading from '@/components/layout/Heading.vue';
import Container from '@/components/layout/Container.vue';
import BarChart from '@/components/habits/BarChart.vue';
import PeriodSelector from '@/components/habits/PeriodSelector.vue';
import dayjs from 'dayjs';
import 'dayjs/locale/pt-br';

dayjs.locale('pt-br');

const activePeriod = ref('W');
const statsData = ref([]);
const habitStore = useHabitStore();
const { withLoading } = useLoading();

const chartLabels = computed(() => {
  return statsData.value.map(item => item.label);
});

const chartValues = computed(() => {
  return statsData.value.map(item => item.percentage || 0);
});

const averagePercentage = computed(() => {
  if (statsData.value.length === 0) return 0;
  
  const totalCompleted = statsData.value.reduce((acc, item) => acc + (item.completed || 0), 0);
  const totalHabits = statsData.value.reduce((acc, item) => acc + (item.total || 0), 0);
  
  if (totalHabits === 0) return 0;
  return Math.round((totalCompleted / totalHabits) * 100);
});

const displayedPercentage = ref(0);

watch(averagePercentage, (newVal) => {
  const duration = 1000;
  const startValue = displayedPercentage.value;
  const diff = newVal - startValue;
  const startTime = Date.now();

  const step = () => {
    const elapsed = Date.now() - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const ease = 1 - Math.pow(1 - progress, 4); // easeOutQuart
    
    displayedPercentage.value = Math.round(startValue + diff * ease);

    if (progress < 1) {
      requestAnimationFrame(step);
    }
  };

  requestAnimationFrame(step);
}, { immediate: true });

const handlePeriodChange = async (period) => {
  await withLoading(async () => {
    const result = await habitStore.getHabitStats(period.value);
    statsData.value = result.data;
  }, 'Erro ao carregar estatísticas');
};

onIonViewWillEnter(async () => {
  await withLoading(async () => {
    const result = await habitStore.getHabitStats('W');
    statsData.value = result.data;
  }, 'Erro ao carregar estatísticas');
});

onIonViewDidLeave(async () => {
  statsData.value = [];
});
</script>

<template>
  <ion-page>
    <ion-content :fullscreen="true" class="vertical-safe-area">
      <Container>
        <Heading title="Estatísticas" />

        <PeriodSelector 
          v-model="activePeriod"
          @change="handlePeriodChange"
        />

        <div class="stats-container">
          <!-- Chart Card -->
          <div class="chart-card">
            <div class="chart-header">
              <span class="chart-title">Total de Atividades</span>
              <h2 class="chart-percentage">
                {{ displayedPercentage }}%
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
.chart-card {
  background: var(--color-background-secondary);
  border-radius: 24px;
  padding: 24px;
  color: var(--color-text-primary);
  min-height: 340px;
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
