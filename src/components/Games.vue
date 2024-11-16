<script setup>
import Game from "./Game.vue";
import axios from "axios";
import {inject, onMounted, ref} from "vue";

const games = ref([]);

const getGames = async () => {
  try {
    const response = await axios.get(import.meta.env.VITE_API_URL + "/api/games", {
      headers: {
        "Content-Type": "application/ld+json",
      }
    });
    console.log(response.data)
    games.value = response.data;
  }catch (error) {
    console.error(error);
  }
}

onMounted(async () => {
  await getGames();
})


</script>

<template>
  <div class="bg-stone-900 flex-1 p-8">
      <p class="font-bold text-lg">Les jeux</p>
      <div class="flex flex-wrap mt-8">
        <Game v-for="game in games" :key="game.name" :game="game"/>
      </div>
  </div>
</template>

<style scoped>

</style>