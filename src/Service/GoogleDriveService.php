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
        $this->client = new Google_Client();
        $this->client->setAuthConfig($_ENV['DIRECTORY_CREDENTIALS']);
        $this->client->addScope(Google_Service_Drive::DRIVE);

        $this->driveService = new Google_Service_Drive($this->client);
    }

    public function downloadFile($fileId)
    {
        try {
            $response = $this->driveService->files->get($fileId, [
                'alt' => 'media',
            ]);

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
                'fields' => 'files(id, name, size)', // Inclure la taille du fichier dans la réponse
            ];

            // Exécuter la requête de liste des fichiers
            $files = $this->driveService->files->listFiles($parameters);

            // Vérifier si un fichier correspondant est trouvé
            if (count($files->getFiles()) === 0) {
                throw new \Exception("File with name '$fileName' not found.");
            }

            // Récupérer l'ID du fichier trouvé
            $file = $files->getFiles()[0];
            $fileId = $file->getId();
            $fileSize = $file->getSize(); // Taille du fichier

            // Télécharger le fichier via son ID en tant que flux
            $response = $this->driveService->files->get($fileId, ['alt' => 'media']);

            // Créer un flux de lecture pour retourner les données
            $stream = $response->getBody();

            // Retourner un tableau contenant le flux et la taille du fichier
            return ['stream' => $stream, 'size' => $fileSize];
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
