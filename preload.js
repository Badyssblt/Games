const { contextBridge, ipcRenderer } = require('electron');

// Expose l'API de sélection de dossier dans le processus de rendu
contextBridge.exposeInMainWorld('electron', {
    selectFolder: () => ipcRenderer.invoke('select-folder'),
    saveFile: (file, fileName, folderPath) => ipcRenderer.invoke('saveFile', file, fileName, folderPath)
});
