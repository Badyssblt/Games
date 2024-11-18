<script setup>
defineProps(['title', 'count'])
</script>

<template>
  <div class="bg-surface px-8 py-4 flex flex-col items-center gap-4 rounded-md border border-white/20 hover:bg-white/20 transition-all ">
    <div>
      <slot/>
    </div>
    <p>{{ title }}</p>
    <p class="text-xl font-bold">{{ count }}</p>
  </div>
</template>

<style scoped>

</style>