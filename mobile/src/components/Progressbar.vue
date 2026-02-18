<script setup>
const props = defineProps({
  progress: {
    type: [ String, Number ],
    required: true,
    validator: (value) => value >= 0 && value <= 100
  }
});
</script>

<template>
  <div class="progress-container">
    <div
      :style="{ width: progress + '%' }"
      class="progress"
    />
  </div>
</template>

<style scoped>
div.progress-container {
  position: relative;
  background: #3f3f46;
  border-radius: .75rem;
  margin-top: 1rem;
  margin-bottom: 1.25rem;
  height: .75rem;
  width: 100%;
}

div.progress {
  position: absolute;
  top: 0;
  left: 0;
  height: .75rem;
  border-radius: .75rem;
  background: var(--color-primary);
  transition-property: width;
  transition-duration: 500ms;
}
</style>