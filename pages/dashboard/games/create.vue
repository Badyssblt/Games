<script setup>
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
    "name": "Créer un jeu",
    "link": "/dashboard/games/create"
  }
];

const handleImageChange = (event) => {
  console.log(event)
  image.value = event.target.files[0];
};

const { $api } = useNuxtApp();

const name = ref('');
const description = ref('');
const file = ref('');
const image = ref(null);
const version = ref('');
const size = ref('');

const isFormValid = () => {
  return (
      name.value &&
      description.value &&
      file.value &&
      image.value &&
      version.value &&
      size.value
  );
};

const errors = ref(false);
const message = ref('');

const createGames = async () => {
  errors.value = false;
  message.value = "Veuillez remplir tous les champs !"
  try {
    if(!isFormValid()) {
      errors.value = true;
      return;
    }
    const form = new FormData();
    form.append('name', name.value);
    form.append('description', description.value);
    form.append('file', file.value);
    form.append('imageFile', image.value)
    form.append('version', version.value);
    form.append('size', size.value);

    const response = await $api.post('/api/games', form);

    navigateTo('/dashboard/games');

  }catch (e) {
    message.value = "Une erreur est survenue lors de la création du jeux"
  }
}
</script>

<template>
  <div class="flex">
    <Aside/>
    <div class="flex flex-col min-h-screen p-8 flex-1">
      <TopContentInfo :type="'games'" :navRows="navRows" class="" :pageType="'Créer un'"/>
      <div class="mt-6 border border-white/20 rounded-md pb-8">
        <form class="m-8 flex flex-col gap-4" @submit.prevent="createGames">
          <Input :label="'Nom du jeu'" v-model="name" />
          <label for="description">Description du jeu
            <textarea class="bg-transparent border border-white/20 w-full rounded-md focus:outline-none p-4" id="description" v-model="description"></textarea>
          </label>
          <Input label="Build" v-model="file"/>
          <Input type="file" label="Image du jeu" v-model="image" @change="handleImageChange"/>
          <Input label="Version" v-model="version" />
          <Input label="Taille" v-model="size" />
          <Button>Créer un jeu</Button>
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