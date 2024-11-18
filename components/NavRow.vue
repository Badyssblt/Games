<script setup>
defineProps(['navRows'])
</script>

<template>
  <!--
    Heads up! 👋

    This component comes with some `rtl` classes. Please remove them if they are not needed in your project.
  -->

  <nav aria-label="Breadcrumb">
    <ol class="flex items-center gap-1 text-sm text-white">
      <li v-for="(item, index) in navRows" :key="index" class="flex items-center">
        <NuxtLink :to="item.link" class="block transition hover:text-white/60">
          <svg
              v-if="item.icon"
              xmlns="http://www.w3.org/2000/svg"
              class="size-4"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
          >
            <path :d="item.icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
          </svg>
          {{ item.name }}
        </NuxtLink>
        <!-- Affichage de la flèche après chaque élément sauf le dernier -->
        <span v-if="index < navRows.length - 1" class="text-white mx-2">
          <svg
              xmlns="http://www.w3.org/2000/svg"
              class="size-4"
              viewBox="0 0 20 20"
              fill="currentColor"
          >
            <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
            />
          </svg>
        </span>
      </li>

      <li class="rtl:rotate-180">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="size-4"
            viewBox="0 0 20 20"
            fill="currentColor"
        >
          <path
              fill-rule="evenodd"
              d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
              clip-rule="evenodd"
          />
        </svg>
      </li>


    </ol>
  </nav>
</template>

<style scoped>

</style>