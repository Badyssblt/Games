<script setup>

const games = ref([]);
const allGames = ref([]);
const searchQuery = ref("");

const { $api } = useNuxtApp();
const config = useRuntimeConfig();

const getGames = async () => {
  try {
    const response = await $api.get('/api/games');
    games.value = response.data;
    allGames.value = response.data;
  } catch (error) {
    console.log(error);
  }
};

const deleteGame = async (id) => {
  try {
    await $api.delete(`/api/games/${id}`);
    await getGames();
  }catch (e) {
  }
}

// Fonction pour implémenter une meilleure recherche
const levenshteinDistance = (a, b) => {
  const matrix = [];

  for (let i = 0; i <= b.length; i++) {
    matrix[i] = [i];
  }
  for (let j = 0; j <= a.length; j++) {
    matrix[0][j] = j;
  }

  for (let i = 1; i <= b.length; i++) {
    for (let j = 1; j <= a.length; j++) {
      if (b.charAt(i - 1) === a.charAt(j - 1)) {
        matrix[i][j] = matrix[i - 1][j - 1];
      } else {
        matrix[i][j] = Math.min(
            matrix[i - 1][j - 1] + 1,
            matrix[i][j - 1] + 1,
            matrix[i - 1][j] + 1
        );
      }
    }
  }

  return matrix[b.length][a.length];
};

const isSimilar = (a, b, threshold = 3) => {
  const distance = levenshteinDistance(a.toLowerCase(), b.toLowerCase());
  return distance <= threshold;
};

const search = () => {
  if (searchQuery.value === "") {
    games.value = allGames.value;
  } else {
    games.value = allGames.value.filter((game) =>
        isSimilar(game.name, searchQuery.value)
    );
  }
};

watch(searchQuery, () => {
  search();
});

onMounted(async () => {
  await getGames();
});

const navRows = [
  {
    "name": "Dashboard",
    "link": "/dashboard",
  },
  {
    "name": "Jeux",
    "link": "/games",
  }
];
</script>

<template>
  <div class="flex flex-col min-h-screen">
    <TopContentInfo :type="'games'" :navRows="navRows" class="" :pageType="'Liste des'"/>
    <div class="mt-6 border border-white/20 rounded-md pb-8">
      <div class="flex justify-between items-end m-6">
        <form class="w-96">
          <Input :label="'Rechercher un jeux'" v-model="searchQuery"/>
        </form>
        <div>
          <NuxtLink to="/dashboard/games/create"
                    class="px-6 py-2 bg-blue-600 border border-transparent transition-all rounded-md hover:bg-transparent hover:border-blue-600 cursor-pointer hover:text-blue-400">
            Créer un jeux
          </NuxtLink>
        </div>
      </div>

      <div class="overflow-y-auto max-h-96 px-6">
        <table class="min-w-full table-auto mt-4 h-full">
          <thead>
          <tr class="h-full">
            <th class="py-2 text-left">ID</th>
            <th class="px-4 py-2 text-left">Image</th>
            <th class="px-4 py-2 text-left">Nom</th>
            <th class="px-4 py-2 text-left">Description</th>
            <th class="px-4 py-2 text-left">Fichier</th>
            <th class="px-4 py-2 text-left">Version</th>
            <th class="px-4 py-2 text-left">Taille</th>
            <th class="px-4 py-2 text-left">Action</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="game in games" :key="game.id" class="border-b border-white/20 w-fit h-full">
            <td class="py-2">{{ game.id }}</td>
            <td><img class="w-24 py-4" :src="`${config.public.API_URL}/images/games/${game.imageName}`" alt=""></td>
            <td class="px-4 py-2">{{ game.name }}</td>
            <td class="px-4 py-2">{{ game.description }}</td>
            <td class="px-4 py-2">{{ game.file }}</td>
            <td class="px-4 py-2">{{ game.version || 'Non spécifiée' }}</td>
            <td class="px-4 py-2">{{ game.size || 'Non spécifiée' }}</td>
            <td class="flex h-full items-center gap-4">
              <button class="bg-green-900 p-2 rounded-md flex justify-center items-center"
                      @click="navigateTo(`/dashboard/games/${game.id}`)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/>
                </svg>
              </button>
              <button class="bg-red-900 p-2 rounded-md flex justify-center items-center"
                      @click="deleteGame(game.id)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round"
                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                </svg>
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>


<style scoped>
</style>
