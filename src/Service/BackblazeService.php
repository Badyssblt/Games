<?php

namespace App\Service;

use obregonco\B2\Client;
use obregonco\B2\Bucket;
use Symfony\Component\HttpFoundation\StreamedResponse;


class BackblazeService
{
    private Client $client;
    private string $bucketName;

    public function __construct()
    {
        $this->initClient();
    }

    private function initClient()
    {
        $this->bucketName = "ae85836733c62d549235061a";

        $this->client = new Client('e53736d4256a', [
            'applicationKey' => 'K003fpW5t5W4DDtMh+WKvVSGGajypPo',
            'keyId' => '003e53736d4256a0000000001'
        ]);
    }

    /**
     * Télécharger un fichier depuis Backblaze en streaming.
     */
    public function downloadFile(string $filePath): StreamedResponse
    {
        try {
            // Télécharger le fichier depuis Backblaze (cela renvoie un objet GuzzleHttp\Psr7\Stream)
            $stream = $this->client->download([
                'BucketName' => $this->bucketName,
                'FileName' => $filePath
            ]);

            $response = new StreamedResponse(function () use ($stream) {
                // Lire et envoyer le contenu du flux par petits morceaux
                while (!$stream->eof()) {
                    echo $stream->read(1024 * 8); // Lire par morceaux de 8 Ko
                    flush(); // Forcer le flush du tampon pour envoyer immédiatement
                }
                $stream->close(); // Fermer le flux après utilisation
            });

            // Définir les headers pour le téléchargement du fichier
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filePath) . '"');

            return $response;
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors du téléchargement du fichier : ' . $e->getMessage());
        }
    }
    public function uploadFile(string $fileName, string $filePath): ?string
    {
        try {
            $stream = fopen($filePath, 'r');

            if (!$stream) {
                throw new \RuntimeException('Erreur lors de l\'ouverture du fichier pour l\'envoi.');
            }

            $file = $this->client->upload([
                'BucketName' => $this->bucketName,
                'FileName' => $fileName,
                'Body' => $stream
            ]);

            if ($file instanceof \obregonco\B2\File) {
                return $file->getFileId();
            } else {
                throw new \RuntimeException('Erreur lors de la récupération des informations du fichier.');
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de l\'envoi du fichier : ' . $e->getMessage());
        } finally {
            // Toujours fermer le flux si ouvert
            if (isset($stream) && is_resource($stream)) {
                fclose($stream);
            }
        }
    }



    /**
     * Lister les fichiers dans le bucket.
     */
    public function listFiles(): array
    {
        try {
            $bucket = $this->client->createBucket(['BucketName' => $this->bucketName]);
            return $bucket->listFiles();
        } catch (\Exception $e) {
            throw new \RuntimeException('Erreur lors de la récupération des fichiers : ' . $e->getMessage());
        }
    }
}
