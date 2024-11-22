<script setup>
definePageMeta({
  middleware: "admin"
})


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
const image = ref(null);
const size = ref('');

const isVersionShow = ref(false);
const versionName = ref("");
const versionFile = ref("");

const isFormValid = () => {
  return (
      name.value &&
      description.value &&
      image.value &&
      size.value
  );
};

const errors = ref(false);
const message = ref('');

const createGames = async () => {
  errors.value = false;
  try {
    if(!isFormValid()) {
      errors.value = true;
      message.value = "Veuillez remplir tous les champs !"
      return;
    }
    const form = new FormData();
    form.append('name', name.value);
    form.append('description', description.value);
    form.append('imageFile', image.value)
    form.append('size', size.value);

    const response = await $api.post('/api/games', form);

    console.log(versionName.value, versionFile.value);
    if(versionName.value && versionFile.value) {
      console.log("test")
      const versionResponse = await $api.post('/api/versions', {
        name: versionName.value,
        file: versionFile.value,
        game: '/api/games/' + response.data.id
      })
      console.log(response)
    }

    navigateTo('/dashboard/games')


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
          <Input type="file" label="Image du jeu" @change="handleImageChange"/>
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
          <Button>Créer un jeu</Button>
          <Error :state="errors">
            {{ message }}
          </Error>
        </form>
      </div>
    </div>
  </div>


</template>

<style scoped>

</style>