export default defineNuxtPlugin ((nuxtApp) => {
    const constants = {
        GAMES: 'Jeux',
        USERS: 'Utilisateurs',
        VERSIONS: "Versions"
    };


    nuxtApp.provide('constants', constants);
});
