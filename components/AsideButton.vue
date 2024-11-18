<script setup>
const props = defineProps(['selectedContent', 'type', 'link'])
</script>

<template>
  <NuxtLink :to="link" :class="['w-48 py-3 rounded-md text-start px-6 transition-all', selectedContent === type ? 'bg-blue-600' : 'hover:bg-blue-600/10 ']">
    <slot/>
  </NuxtLink>
</template>

<style scoped>

</style>