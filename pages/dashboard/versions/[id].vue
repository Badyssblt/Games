<script setup>

definePageMeta({
  middleware: "admin"
})


const route = useRoute();

const id = route.params.id;

const version = ref();

const navRows = [
  {
    "name": "Dashboard",
    "link": "/dashboard"
  },
  {
    "name": "Versions",
    "link": "/dashboard/versions"
  },
  {
    "name": "Modifier une versions",
    "link": "/dashboard/versions/" + id
  }
];

const { $api } = useNuxtApp();

const name = ref('');
const file = ref('');

const isVersionShow = ref(false);

const loading = ref(false);

const getVersion = async () => {
  try {
    const response = await $api.get(`/api/versions/${id}`);
    version.value = response.data;
    name.value = version.value.name;
    file.value = version.value.file;

    useHead({
      title: response.data.name
    })
  }catch (e) {

  }
}

const editVersion = async () => {
  loading.value = true;
  try {


    const versionData = {
      name: name.value,
      file: file.value,
    }

    await $api.patch(`/api/versions/${id}`, versionData);

    loading.value = false;

    navigateTo('/dashboard/versions');

  }catch (e) {
    loading.value = false;
    console.log(e)
  }
}


const errors = ref(false);
const message = ref('');

onMounted(async () => {
  await getVersion();
})

</script>

<template>
  <div class="flex">
    <Aside/>
    <div class="flex flex-col min-h-screen p-8 flex-1">
      <TopContentInfo :type="'versions'" :navRows="navRows" class="" :pageType="'Modifier un'"/>
      <div class="mt-6 border border-white/20 rounded-md pb-8">
        <form class="m-8 flex flex-col gap-4" @submit.prevent="editVersion">
          <Input :label="'Nom du jeu'" v-model="name" />
          <Input :label="'Fichier du jeu'" v-model="file" />
          <Button v-if="version" :loading="loading">Modifier {{ version.name }}</Button>
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