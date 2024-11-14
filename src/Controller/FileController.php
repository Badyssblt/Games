<?php


namespace App\Controller;

use App\Service\BackblazeService;
use App\Service\GoogleDriveService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

class FileController extends AbstractController
{
    private BackblazeService $backblazeService;
    private GoogleDriveService $googleDriveService;

    public function __construct(BackblazeService $backblazeService)
    {
        // Injection des services via le constructeur
        $this->backblazeService = $backblazeService;
        $this->googleDriveService = new GoogleDriveService;
    }

    #[Route("/api/game/{fileName}/download", name: "file_download")]
    public function downloadFile(Request $request, string $fileName): Response
    {
        try {
            // Appeler la méthode du service pour obtenir le flux du fichier et sa taille
            $fileData = $this->googleDriveService->downloadFileStreamByName($fileName);
            $stream = $fileData['stream'];
            $fileSize = $fileData['size'];

            // Créer une réponse en streaming
            $response = new StreamedResponse(function () use ($stream, $fileSize) {
                $chunkSize = 1024 * 1024 * 10; // Taille du morceau (1 Mo)
                $bytesSent = 0;

                flush();
                // Lire le fichier par morceaux et envoyer chaque morceau
                while (!$stream->eof()) {
                    // Lire le prochain morceau
                    $data = $stream->read($chunkSize);
                    echo $data;
                    flush();

                    // Mettre à jour la progression
                    $bytesSent += strlen($data);
                    // Cette partie calcule la progression, mais elle ne fait rien de plus ici.
                    // Le calcul est essentiel mais le client Axios le gérera lui-même.
                }
            });

            // Définir les en-têtes pour le téléchargement
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            $response->headers->set('Content-Length', (string)$fileSize); // Indispensable pour que Axios suive la progression

            return $response;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une réponse d'erreur
            return new Response('Error downloading file: ' . $e->getMessage(), 500);
        }
    }



    #[Route('/api/game/upload', name: "file_upload")]
    public function upload(Request $request)
    {
        // Récupération du fichier de la requête
        $file = $request->files->get('file');
        if (!$file) {
            return new JsonResponse(['error' => 'No file uploaded'], 400);
        }

        // Récupérer le refresh token depuis la session ou autre source sécurisée
        $refreshToken = $request->get('refresh_token'); // Par exemple, depuis les paramètres de la requête
        if (!$refreshToken) {
            return new JsonResponse(['error' => 'Refresh token is required'], 400);
        }

        // Définir le refresh token dans le service GoogleDriveService
        $this->googleDriveService->setRefreshToken($refreshToken);

        // Appeler la méthode d'upload du fichier dans Google Drive
        $uploadResult = $this->googleDriveService->uploadFile($file->getPathname());

        // Si une erreur est survenue lors de l'upload
        if (isset($uploadResult['error'])) {
            return new JsonResponse(['error' => $uploadResult['error']], 500);
        }

        // Si l'upload est réussi, retourner l'ID du fichier
        return new JsonResponse(['message' => 'File uploaded', 'fileId' => $uploadResult['fileId']]);
    }


    #[Route('/api/gamesLists')]
    public function listFiles(): JsonResponse
    {
        try {
            // Appeler la méthode pour lister les fichiers
            $fileList = $this->googleDriveService->listFiles();

            // Retourner la liste des fichiers sous forme de JSON
            return new JsonResponse(['files' => $fileList]);
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une réponse d'erreur
            return new JsonResponse(['error' => 'Error listing files: ' . $e->getMessage()], 500);
        }
    }
}
