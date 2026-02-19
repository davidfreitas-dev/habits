<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const periods = [
  { label: 'S', value: 'W' },
  { label: 'M', value: 'M' },
  { label: '3M', value: '3M' },
  { label: '6M', value: '6M' },
  { label: 'A', value: 'Y' }
];

const activeIndex = computed(() => periods.findIndex(p => p.value === props.modelValue));

const selectPeriod = (period) => {
  emit('update:modelValue', period.value);
  emit('change', period);
};
</script>

<template>
  <div class="period-selector">
    <div 
      class="active-indicator" 
      :style="{ transform: `translateX(${activeIndex * 100}%)` }"
    />
    <button 
      v-for="period in periods" 
      :key="period.value"
      :class="['period-btn', { active: modelValue === period.value }]"
      @click="selectPeriod(period)"
    >
      {{ period.label }}
    </button>
  </div>
</template>

<style scoped>
.period-selector {
  position: relative;
  display: flex;
  background: var(--color-background-secondary);
  padding: 4px;
  border-radius: 100px;
  margin-bottom: 24px;
  margin-top: 16px;
}

.active-indicator {
  position: absolute;
  top: 4px;
  left: 4px;
  bottom: 4px;
  width: calc((100% - 8px) / 5);
  background: var(--color-primary);
  border-radius: 100px;
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 0;
}

.period-btn {
  position: relative;
  background: transparent;
  border: none;
  color: var(--color-text-secondary);
  font-weight: 600;
  font-size: 14px;
  flex: 1;
  height: 44px;
  border-radius: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: color 0.3s ease;
  z-index: 1;
}

.period-btn.active {
  color: var(--color-text-primary);
}
</style>
