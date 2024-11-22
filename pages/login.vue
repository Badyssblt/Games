<script setup lang="ts">
import {useAuth} from "~/store/auth";

const store = useAuth();

const config = useRuntimeConfig();

const { $api } = useNuxtApp();

const email = ref('');
const password = ref('');
const passwordInputType = ref("password");
const isWrong = ref(false);

const loading = ref(false);

const login = async () => {
  isWrong.value = false;
  loading.value = true;
  try {
    const response = await $api.post("/api/login_check", {
      username: email.value,
      password: password.value,
    });
    store.token = response.data.token;
    store.authenticate({
      token: response.data.token,
    })
    navigateTo('/dashboard')
  }catch(e) {
    if(e.status === 401){
      isWrong.value = true;
    }
  }finally {
    loading.value = false;
  }
}

const triggerChangeTypePassword = () => {
  if(passwordInputType.value === "password") {
    passwordInputType.value = "text";
  }else {
    passwordInputType.value = "password";
  }
}


</script>

<template>
  <div class="min-h-screen flex items-center justify-center">
    <form class="w-1/2 bg-surface py-8 px-4 rounded-md shadow-sm border border-white/20 shadow" @submit.prevent="login">
      <h2 class="text-xl font-bold text-center mb-4">Se connecter</h2>
      <Input :label="'Entrer votre email'" :placeholder="'johndoe@example.com'" v-model="email" class="mb-4"/>
      <div class="flex items-end gap-4 w-full">
        <Input label="Mot de passe" :placeholder="'********'" v-model="password" :type="passwordInputType" class="flex-1"/>
        <button type="button" class="flex items-center rounded-md border p-2 border-white/20" @click="triggerChangeTypePassword">
          <svg v-if="passwordInputType === 'password'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-white/60 hover:text-white transition-all">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-white/60 hover:text-white transition-all">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
          </svg>
        </button>
      </div>
      <Button :loading="loading" class="w-full mt-6">Se connecter</Button>
      <Error :state="isWrong" class="mt-4">Le mot de passe ou l'email sont incorrect...</Error>
    </form>
  </div>
</template>


<style scoped>

</style>