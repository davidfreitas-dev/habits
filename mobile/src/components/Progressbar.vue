<template>
  <div>
    <div
      :style="{ width: progress + '%' }"
      class="progress"
    />
  </div>
</template>

<script setup>
const props = defineProps({
  progress: {
    type: [ String, Number ],
    required: true,
    validator: (value) => value >= 0 && value <= 100
  }
});
</script>

<style scoped>
div {
  position: relative;
  background: #3f3f46;
  border-radius: .75rem;
  margin-top: 1rem;
  margin-bottom: 1.25rem;
  height: .75rem;
  width: 100%;
}

div.progress {
  height: .75rem;
  border-radius: .75rem;
  background: var(--primary);
  transition-property: width;
  transition-duration: 500ms;
}
</style>