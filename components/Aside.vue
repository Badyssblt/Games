<script setup>
defineProps(['selected'])


</script>

<template>
<aside class="border-r border-white/20 px-4 py-8 flex flex-col gap-2">
  <AsideButton :selectedContent="selected" :type="'games'" :link="'/dashboard/games'">Jeux</AsideButton>
  <AsideButton :selectedContent="selected" :type="'users'">Utilisateurs</AsideButton>
</aside>
</template>

<style scoped>

</style>