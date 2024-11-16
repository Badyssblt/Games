const { app, BrowserWindow, dialog, ipcMain } = require('electron');
const path = require('path');
const fs = require('fs');
const AdmZip = require('adm-zip');
const { execFile } = require('child_process');


let mainWindow;

const userDataPath = app.getPath('userData');
const dataFilePath = path.join(userDataPath, 'userData.json');

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

ipcMain.handle('select-folder', async () => {
    const result = await dialog.showOpenDialog({
        properties: ['openDirectory']  // Sélectionner un dossier
    });
    return result.filePaths;  // Retourner le chemin du dossier
});

ipcMain.handle('saveFile', async (event, arrayBuffer, fileName, folderPath) => {
    const savePath = path.join(folderPath, fileName);

    const buffer = Buffer.from(arrayBuffer);

    fs.writeFileSync(savePath, buffer);

    return savePath; // Retourner le chemin du fichier sauvegardé
});

ipcMain.handle('unzip-file', async (event, zipPath, destPath) => {
    try {
        const zip = new AdmZip(zipPath);
        zip.extractAllTo(destPath, true);
        return { success: true };
    } catch (error) {
        console.error('Erreur pendant le dézippage:', error);
        return { success: false, error: error.message };
    }
});

ipcMain.handle('delete-file', async (event, filePath) => {
    try {
        if (fs.existsSync(filePath)) {
            fs.unlinkSync(filePath);
            return { success: true };
        } else {
            return { success: false, error: "Fichier non trouvé." };
        }
    } catch (error) {
        console.error('Erreur pendant la suppression:', error);
        return { success: false, error: error.message };
    }
});

ipcMain.handle('save-user-data', async (event, data) => {
    saveUserData(data);
    return { success: true };
});

ipcMain.handle('load-user-data', async () => {
    const data = loadUserData();
    return data;
});

ipcMain.handle('update-game-status', async (event, gameName, isDownloaded, installPath) => {
    updateGameStatus(gameName, isDownloaded, installPath);
    return { success: true };
});

ipcMain.handle('launch-executable', async (event, executablePath) => {
    try {
        launchExecutable(executablePath);
        return { success: true };
    } catch (error) {
        console.error('Erreur lors du lancement de l\'exécutable:', error);
        return { success: false, error: error.message };
    }
});

function loadUserData() {
    try {
        if (fs.existsSync(dataFilePath)) {
            const data = fs.readFileSync(dataFilePath, 'utf-8');
            return JSON.parse(data);
        }
        return { games: [] };
    } catch (error) {
        console.error('Erreur lors de la lecture des données utilisateur :', error);
        return { games: [] };
    }
}

function saveUserData(data) {
    try {
        fs.writeFileSync(dataFilePath, JSON.stringify(data, null, 2), 'utf-8');
        console.log('Données utilisateur sauvegardées avec succès.');
    } catch (error) {
        console.error('Erreur lors de la sauvegarde des données utilisateur :', error);
    }
}

function updateGameStatus(gameName, isDownloaded, installPath) {
    const userData = loadUserData();
    const gameIndex = userData.games.findIndex(game => game.name === gameName);

    if (gameIndex !== -1) {
        userData.games[gameIndex].isDownloaded = isDownloaded;
        userData.games[gameIndex].installPath = installPath; // Mise à jour du chemin d'installation
    } else {
        userData.games.push({ name: gameName, isDownloaded, installPath });
    }

    saveUserData(userData);
}


function launchExecutable(executablePath) {
    execFile(executablePath, (error, stdout, stderr) => {
        if (error) {
            console.error(`Erreur lors de l'exécution de l'exécutable : ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`Erreur de l'exécutable : ${stderr}`);
            return;
        }
        console.log(`Résultat : ${stdout}`);
    });
}
