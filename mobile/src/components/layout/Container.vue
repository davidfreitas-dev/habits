<template>
  <div id="container">
    <slot />
  </div>
</template>

<style scoped>
#container {
  display: flex;
  flex-direction: column;
  padding: 1rem;
}
</style>