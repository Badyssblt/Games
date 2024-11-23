// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],
  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },
  runtimeConfig: {
    public: {
      // API_URL: 'http://localhost:8215',
      API_URL: "https://orbital.badyssblilita.fr/v1"
    }
  },


  modules: ['@pinia/nuxt', '@pinia-plugin-persistedstate']
})