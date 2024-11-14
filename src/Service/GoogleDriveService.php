<?php

namespace App\Service;

use Google_Client;
use Google_Service_Drive;

class GoogleDriveService
{
    private $client;
    private $driveService;

    public function __construct()
    {
        // Créez un client Google pour le compte de service
        $this->client = new Google_Client();
        $this->client->setAuthConfig('/var/www/LauncherAPI/credentials.json'); // Le fichier JSON de la clé du compte de service
        $this->client->addScope(Google_Service_Drive::DRIVE);

        // Initialisez le service Google Drive avec le client
        $this->driveService = new Google_Service_Drive($this->client);
    }

    // Téléchargez un fichier depuis Google Drive en utilisant un ID de fichier
    public function downloadFile($fileId)
    {
        try {
            $response = $this->driveService->files->get($fileId, [
                'alt' => 'media', // Pour récupérer le contenu du fichier
            ]);

            // Récupérer le contenu du fichier
            $content = $response->getBody()->getContents();

            return $content;
        } catch (\Exception $e) {
            throw new \Exception("Error downloading file: " . $e->getMessage());
        }
    }

    public function downloadFileStreamByName($fileName)
    {
        try {
            // Rechercher le fichier par nom
            $parameters = [
                'q' => "name = '$fileName'",
                'fields' => 'files(id, name)',
            ];

            // Exécuter la requête de liste des fichiers
            $files = $this->driveService->files->listFiles($parameters);

            // Vérifier si un fichier correspondant est trouvé
            if (count($files->getFiles()) === 0) {
                throw new \Exception("File with name '$fileName' not found.");
            }

            // Récupérer l'ID du fichier trouvé
            $fileId = $files->getFiles()[0]->getId();

            // Télécharger le fichier via son ID en tant que flux
            $response = $this->driveService->files->get($fileId, ['alt' => 'media']);

            // Retourner le flux de données
            return $response->getBody();
        } catch (\Exception $e) {
            throw new \Exception("Error downloading file stream: " . $e->getMessage());
        }
    }


    public function listFiles($folderId = null)
    {
        try {
            $query = $folderId ? "'$folderId' in parents" : ''; // Filtrer par dossier si $folderId est défini

            $parameters = [
                'q' => $query,
                'fields' => 'files(id, name)', // Spécifiez les champs à récupérer
            ];

            // Récupérer la liste des fichiers
            $files = $this->driveService->files->listFiles($parameters);

            // Convertir en tableau de résultats simplifiés
            $fileList = [];
            foreach ($files->getFiles() as $file) {
                $fileList[] = [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                ];
            }

            return $fileList;
        } catch (\Exception $e) {
            throw new \Exception("Error listing files: " . $e->getMessage());
        }
    }
}
