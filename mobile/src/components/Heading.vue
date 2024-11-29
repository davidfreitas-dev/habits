<template>
  <h1>{{ title }}</h1>
</template>

<script setup>
const props = defineProps({
  title: {
    type: String,
    default: 'Heading'
  }
});
</script>

<style scoped>
h1 {
  color: var(--font);
  font-weight: 800;
  font-size: 1.875rem;
  margin: 0;
}
</style>
