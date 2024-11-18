<script setup>
defineProps({
  loading: {
    type: Boolean,
    default: false
  }
})
</script>

<template>
  <button class="flex items-center justify-center gap-4 px-6 py-2 rounded text-white bg-blue-600 border border-transparent hover:bg-transparent hover:border-blue-600 hover:text-blue-600 hover:cursor-pointer transition-all">
    <slot/>
    <Loader v-if="loading"/>
  </button>
</template>

<style scoped>

</style>