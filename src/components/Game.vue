<template>
  <div>
    <div class="border border-white/20 p-4 w-96">
      <div class="w-full h-64 overflow-hidden">
        <img class="w-full h-full object-cover" :src="imageUrl" alt=""/>
      </div>
      <p class="font-bold my-4">{{ game.name }}</p>
      <p class="text-wrap break-words text-white/60">{{ game.description }}</p>
      <button class="bg-blue-800 px-6 py-2 w-full rounded-md mt-4" @click="playGame" v-if="isDownloaded">Jouer à {{ props.game.name }}</button>
      <button class="bg-blue-800 px-6 py-2 w-full rounded-md mt-4" @click="showInfo" v-else>Installer</button>
    </div>
    <div v-if="isShow" class="fixed w-full h-screen bg-white/40 top-0 left-0 flex justify-center items-center">
      <div class="bg-stone-900 w-[40%] p-4 rounded-lg min-h-96 relative">
        <div class="absolute right-4">
          <button @click="showInfo">Fermer</button>
        </div>
        <p class="font-bold text-xl" v-if="!isDownloaded">Installer {{ game.name }}</p>
        <p class="font-bold text-xl" v-else-if="isDownloaded">Options</p>
        <button @click="chooseFolder" class="bg-blue-700 px-6 py-2 w-full rounded-md mt-4">Sélectionner un dossier</button>
        <p v-if="folderPath" class="mt-4">Dossier sélectionné : {{ folderPath[0] }}</p>
        <button @click="startDownload" class="bg-blue-700 px-6 py-2 w-full rounded-md mt-4" :disabled="isDownloading">Démarrer l'installation</button>

        <!-- Progress Bar -->
        <div v-if="isDownloading" class="mt-4">
          <p>Progression du téléchargement: {{ progressBar }}%</p>
          <div class="bg-gray-300 h-2 w-full">
            <div class="bg-blue-500 h-full" :style="{ width: progressBar + '%' }"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {computed, inject, onMounted, ref} from 'vue';
import axios from 'axios';

const props = defineProps(['game', 'globalProgress']);
const isShow = ref(false);
const folderPath = ref(null);
const progressBar = ref(0);
const isDownloading = ref(false);

const globalProgress = inject("progress");
const gameDownload = inject('game');

const isDownloaded = ref(false);
const installPath = ref("");



onMounted(async () => {
  const data = await window.electron.loadUserData();
  const downloadedGame = data.games.find(game => game.name === props.game.name);


  if (downloadedGame && downloadedGame.isDownloaded) {
    isDownloaded.value = true;
    installPath.value = downloadedGame.installPath;
  }


})

const imageUrl = computed(() => {
  return `${import.meta.env.VITE_API_URL}/images/games/${props.game.imageName}`;
});


const showInfo = () => {
  isShow.value = !isShow.value;
}

// Lancer l'executable du jeux
const playGame = async () => {
  const executablePath = `${installPath.value}/Windows/${props.game.name}.exe`;
  await window.electron.launchExecutable(executablePath);
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
  progressBar.value = 0;
  const fileUrl = `${import.meta.env.VITE_API_URL}/api/game/${props.game.version[props.game.version.length - 1].file}/download`;
  gameDownload.value = props.game;

  try {
    showInfo();
    const response = await axios({
      url: fileUrl,
      method: 'GET',
      responseType: 'arraybuffer',
      onDownloadProgress: (progressEvent) => {
        console.log(progressEvent)
        const { loaded, total, progress, bytes, rate, estimated, download } = progressEvent;
        progressBar.value = total ? Math.floor((loaded / total) * 100) : null;

        globalProgress.value = Math.min(progressBar.value, 100);

      }
    });


    const arrayBuffer = response.data;  // Nous avons directement l'ArrayBuffer

    const fileName = props.game.version[props.game.version.length - 1].file;

    const fullPath = `${folderPath.value[0]}/${fileName}`;



    await window.electron.saveFile(arrayBuffer, fileName, folderPath.value[0]);

    const result = await window.electron.unzipFile(fullPath, folderPath.value[0]);


    if (result.success) {
      const deleteResult = await window.electron.deleteFile(fullPath);
      if (deleteResult.success) {
        await window.electron.updateGameStatus(props.game.name, true, folderPath.value[0]);
        alert('Téléchargement, décompression et suppression du ZIP terminés !');
      } else {
        alert('Erreur pendant la suppression du fichier ZIP: ' + deleteResult.error);
      }
    } else {
      alert('Erreur pendant la décompression: ' + result.error);
    }
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
