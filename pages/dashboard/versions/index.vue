<script setup >
useHead({
  title: 'Toutes les versions',
})

definePageMeta({
  middleware: "admin"
})

</script>

<template>
  <div class="flex">
    <Aside :selected="'versions'" />
    <VersionContent class="flex-1 p-8"/>
  </div>
</template>

<style scoped>

</style>