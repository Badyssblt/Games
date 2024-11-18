<script setup>

const props = defineProps({
  state: {
    type: Boolean,
    default: false
  },
  time: {
    type: Number,
    default: 3000
  }
});


</script>
<template>
  <div role="alert" class="rounded border-s-4 border-red-500 bg-red-900 p-4" v-if="state">
    <strong class="block font-medium text-red-200"> Une erreur est survenue </strong>

    <p class="mt-2 text-sm text-white">
      <slot/>
    </p>
  </div>
</template>

<style scoped>

</style>