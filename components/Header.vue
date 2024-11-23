<script setup>

import {useAuth} from "~/store/auth.js";

const { $api } = useNuxtApp();
const store = useAuth();
const logout = async () => {
  try {
    const response = await $api.get('/api/logout');
    // Force re-render after logout
    store.token = null;
    navigateTo("/")
  }catch (e) {

  }
}

</script>

<template>
<header>
  <div>
    <button @click="logout">Se déconnecter</button>
  </div>
</header>
</template>

<style scoped>

</style>