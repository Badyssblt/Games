<script setup>

definePageMeta({
  middleware: "admin"
})

const route = useRoute();

const id = route.params.id;

const game = ref();

const navRows = [
  {
    "name": "Dashboard",
    "link": "/dashboard"
  },
  {
    "name": "Jeux",
    "link": "/dashboard/games"
  },
  {
    "name": "Modifier un jeu",
    "link": "/dashboard/games/" + id
  }
];

const { $api } = useNuxtApp();

const name = ref('');
const description = ref('');
const versionFile = ref('');
const image = ref(null);
const versionName = ref('');
const size = ref('');

const isVersionShow = ref(false);

const loading = ref(false);

const getGame = async () => {
  try {
    const response = await $api.get(`/api/games/${id}`);
    game.value = response.data;
    name.value = game.value.name;
    description.value = game.value.description;
    size.value = game.value.size;

    useHead({
      title: response.data.name
    })
  }catch (e) {

  }
}

const editGame = async () => {
  loading.value = true;
  try {
    if(image.value) {
      const form = new FormData();
      form.append('imageFile', image.value)
      await $api.post(`/api/games/${id}/image`, form);
    }

    const gameData = {
      name: name.value,
      description: description.value,
      size: size.value
    };

    const versionData = {
      name: versionName.value,
      file: versionFile.value,
      game: "/api/games/" + game.value.id
    }

    await $api.patch(`/api/games/${id}`, gameData);

    if(versionName.value && versionFile.value) {
      await $api.post('/api/versions', versionData);
    }

    loading.value = false;

    navigateTo('/dashboard/games');

  }catch (e) {
    loading.value = false;
    console.log(e)
  }
}

const handleImageChange = (event) => {
  console.log(event)
  image.value = event.target.files[0];
};

const errors = ref(false);
const message = ref('');

onMounted(async () => {
  await getGame();
})

</script>

<template>
  <div class="flex">
    <Aside/>
    <div class="flex flex-col min-h-screen p-8 flex-1">
      <TopContentInfo :type="'games'" :navRows="navRows" class="" :pageType="'Modifier un'"/>
      <div class="mt-6 border border-white/20 rounded-md pb-8">
        <form class="m-8 flex flex-col gap-4" @submit.prevent="editGame">
          <Input :label="'Nom du jeu'" v-model="name" />
          <label for="description">Description du jeu
            <textarea class="bg-transparent border border-white/20 w-full rounded-md focus:outline-none p-4" id="description" v-model="description"></textarea>
          </label>
          <Input type="file" label="Image du jeu" v-model="image" @change="handleImageChange"/>
          <Input label="Taille" v-model="size" />
          <div>
            <Button type="button" @click="isVersionShow = true" v-if="!isVersionShow">Ajouter une version</Button>
            <Button type="button" @click="isVersionShow = false" v-if="isVersionShow">Supprimer une version</Button>

            <transition
                name="slide-fade"
                enter-active-class="transition ease-out duration-300"
                enter-from-class="opacity-0 max-h-0"
                enter-to-class="opacity-100 max-h-screen"
                leave-active-class="transition ease-in duration-300"
                leave-from-class="opacity-100 max-h-screen"
                leave-to-class="opacity-0 max-h-0"
            >
              <div v-if="isVersionShow" class="overflow-hidden">
                <Input label="Nom de la version" class="mt-4" v-model="versionName" />
                <Input label="Nom du fichier" class="mt-4" v-model="versionFile" />
              </div>
            </transition>
          </div>
          <Button v-if="game" :loading="loading">Modifier {{ game.name }}</Button>
          <Error :state="errors">
            {{ errors }}
          </Error>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>