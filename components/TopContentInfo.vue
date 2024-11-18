<script setup>
import NavRow from "~/components/NavRow.vue";

const props = defineProps(['type', 'navRows', 'pageType']);

const { $constants } = useNuxtApp();

const localizedType = computed(() => {
  return $constants[props.type.toUpperCase()] || props.type;
});
</script>

<template>

  <div class="bg-blue-600/40 rounded-md border border-white/10 p-8">
    <h3 class="text-lg font-medium">{{ pageType }} {{ localizedType }}</h3>
    <div class="text-white/60 flex items-center">
      <NavRow :navRows="navRows"/>
    </div>
  </div>

</template>

<style scoped>

</style>