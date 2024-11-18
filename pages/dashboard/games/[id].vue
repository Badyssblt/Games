<script setup>
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
const file = ref('');
const image = ref(null);
const version = ref('');
const size = ref('');

const loading = ref(false);

const getGame = async () => {
  try {
    const response = await $api.get(`/api/games/${id}`);
    game.value = response.data;
    name.value = game.value.name;
    description.value = game.value.description;
    file.value = game.value.file;
    version.value = game.value.version;
    size.value = game.value.size;
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
      file: file.value,
      version: version.value,
      size: size.value
    };

    await $api.patch(`/api/games/${id}`, gameData);

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

onMounted(() => {
  getGame()
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
          <Input label="Build" v-model="file"/>
          <Input type="file" label="Image du jeu" v-model="image" @change="handleImageChange"/>
          <Input label="Version" v-model="version" />
          <Input label="Taille" v-model="size" />
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