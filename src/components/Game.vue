<template>
  <div>
    <div class="border border-white/20 p-4 w-96">
      <div class="w-full h-64 overflow-hidden">
        <img class="w-full h-full object-cover" :src="'./public/images/' + game.image" alt=""/>
      </div>
      <p class="font-bold my-4">{{ game.name }}</p>
      <p class="text-wrap break-words text-white/60">{{ game.description }}</p>
      <button class="bg-blue-800 px-6 py-2 w-full rounded-md mt-4" @click="showInfo">Installer</button>
    </div>
    <div v-if="isShow" class="fixed w-full h-screen bg-white/40 top-0 left-0 flex justify-center items-center">
      <div class="bg-stone-900 w-[40%] p-4 rounded-lg min-h-96 relative">
        <div class="absolute right-4">
          <button @click="showInfo">Fermer</button>
        </div>
        <p class="font-bold text-xl">Installer {{ game.name }}</p>
        <button @click="chooseFolder" class="bg-blue-700 px-6 py-2 w-full rounded-md mt-4">Sélectionner un dossier</button>
        <p v-if="folderPath" class="mt-4">Dossier sélectionné : {{ folderPath[0] }}</p>
        <button @click="startDownload" class="bg-blue-700 px-6 py-2 w-full rounded-md mt-4" :disabled="isDownloading">Démarrer l'installation</button>

        <!-- Progress Bar -->
        <div v-if="isDownloading" class="mt-4">
          <p>Progression du téléchargement: {{ progress }}%</p>
          <div class="bg-gray-300 h-2 w-full">
            <div class="bg-blue-500 h-full" :style="{ width: progress + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps(['game']);
const isShow = ref(false);
const folderPath = ref(null);
const progress = ref(0);
const isDownloading = ref(false);

const showInfo = () => {
  isShow.value = !isShow.value;
}

const chooseFolder = async () => {
  const path = await window.electron.selectFolder();
  folderPath.value = path;
}

const startDownload = async () => {
  if (!folderPath.value) {
    alert('Veuillez sélectionner un dossier où enregistrer le fichier.');
    return;
  }

  isDownloading.value = true;
  progress.value = 0;

  const fileUrl = `http://localhost:8215/api/game/${props.game.file}/download`;

  try {
    const response = await axios({
      url: fileUrl,
      method: 'GET',
      responseType: 'arraybuffer',
      onDownloadProgress: (progressEvent) => {
        const { loaded, total, progress, bytes, rate, estimated, download } = progressEvent;
        const percentage = total ? Math.floor((loaded / total) * 100) : null;
      }
    });

    const arrayBuffer = response.data;  // Nous avons directement l'ArrayBuffer

    const fileName = props.game.file;

    // Envoyer directement l'ArrayBuffer à Electron sans conversion en Base64
    await window.electron.saveFile(arrayBuffer, fileName, folderPath.value[0]);

    alert('Téléchargement terminé !');
  } catch (error) {
    console.error('Erreur pendant le téléchargement:', error);
    alert('Erreur pendant le téléchargement.' + error);
  } finally {
    isDownloading.value = false;
  }
};
</script>

<style scoped>
/* Vous pouvez personnaliser les styles pour la barre de progression ici */
</style>
