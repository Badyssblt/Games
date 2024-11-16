<script setup>
import Aside from "./components/Aside.vue";
import Games from "./components/Games.vue";
import {provide, ref} from "vue";

const globalProgress = ref(0);
const gameDownload = ref(null )

provide("progress", globalProgress);
provide("game", gameDownload);
</script>

<template>
  <div class="flex min-h-screen">
    <Aside class="w-[20%]"/>
    <Games/>
  </div>
</template>

<style scoped>

</style>
