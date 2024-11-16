<script setup>
import {inject} from "vue";

const progress = inject('progress')
const game = inject('game');
</script>

<template>
  <aside class="bg-stone-950 p-8 flex flex-col justify-between">
    <div>
      <button>
        Tous les jeux
      </button>
    </div>


    <div v-if="game">
      <p>Téléchargement en cours de <span>{{ game.name }}</span></p>
      <p>Progression: {{ progress }}%</p>
      <p class="text-sm text-white/60">Le téléchargement peut rester à 0% pendant plusieurs minutes pendant que le serveur récupère votre fichier...</p>
    </div>
  </aside>
</template>

<style scoped>

</style>