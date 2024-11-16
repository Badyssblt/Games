const { contextBridge, ipcRenderer } = require('electron');

// Expose l'API de sélection de dossier dans le processus de rendu
contextBridge.exposeInMainWorld('electron', {
    selectFolder: () => ipcRenderer.invoke('select-folder'),
    saveFile: (file, fileName, folderPath) => ipcRenderer.invoke('saveFile', file, fileName, folderPath),
    unzipFile: (zipPath, destPath) => ipcRenderer.invoke('unzip-file', zipPath, destPath),
    deleteFile: (filePath) => ipcRenderer.invoke('delete-file', filePath),
    saveUserData: (data) => ipcRenderer.invoke('save-user-data', data),
    loadUserData: () => ipcRenderer.invoke('load-user-data'),
    updateGameStatus: (gameName, isDownloaded, installPath) =>
        ipcRenderer.invoke('update-game-status', gameName, isDownloaded, installPath),
    launchExecutable: (executablePath) => ipcRenderer.invoke('launch-executable', executablePath)

});
