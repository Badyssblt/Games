const { app, BrowserWindow, dialog, ipcMain } = require('electron');
const path = require('path');
const fs = require('fs');

// Création de la fenêtre principale
let mainWindow;

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 800,
        height: 600,
        webPreferences: {
            nodeIntegration: false,
            preload: path.join(__dirname, 'preload.js'),
            sandbox: false
        }
    });

    mainWindow.loadURL('http://localhost:5173');

    mainWindow.on('closed', function () {
        mainWindow = null;
    });
}

app.whenReady().then(createWindow);

app.on('window-all-closed', function () {
    if (process.platform !== 'darwin') app.quit();
});

// Gestion de la sélection de dossier
ipcMain.handle('select-folder', async () => {
    const result = await dialog.showOpenDialog({
        properties: ['openDirectory']  // Sélectionner un dossier
    });
    return result.filePaths;  // Retourner le chemin du dossier
});

// Gestion du téléchargement et de la sauvegarde du fichier
ipcMain.handle('saveFile', async (event, arrayBuffer, fileName, folderPath) => {
    const savePath = path.join(folderPath, fileName);

    // Convertir l'ArrayBuffer en Buffer
    const buffer = Buffer.from(arrayBuffer);

    // Sauvegarder le fichier dans le dossier spécifié
    fs.writeFileSync(savePath, buffer);

    return savePath; // Retourner le chemin du fichier sauvegardé
});
