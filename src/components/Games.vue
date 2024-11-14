<script setup>
import Game from "./Game.vue";
import axios from "axios";
import {onMounted, ref} from "vue";

const games = ref([]);

const getGames = async () => {
  try {
    console.log(import.meta)
    const response = await axios.get(import.meta.env.VITE_API_URL + "/api/games", {
      headers: {
        "Content-Type": "application/ld+json",
      }
    });
    games.value = response.data.member;
    console.log(response.data);
  }catch (error) {
    console.error(error);
  }
}

onMounted(() => {
  getGames();
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