export default defineNuxtPlugin ((nuxtApp) => {
    const constants = {
        GAMES: 'Jeux',
        USERS: 'Utilisateurs',
    };


    nuxtApp.provide('constants', constants);
});
