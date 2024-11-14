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
            // Créer une réponse en streaming
            $response = new StreamedResponse(function () use ($fileName) {
                // Télécharger le fichier depuis Google Drive avec le nom fourni
                $contentStream = $this->googleDriveService->downloadFileStreamByName($fileName);

                // Lire et envoyer le contenu en petits morceaux (par exemple, 1 Mo)
                while (!$contentStream->eof()) {
                    echo $contentStream->read(1024 * 1024); // Lire par morceaux de 1 Mo
                    flush(); // Forcer l'envoi du contenu au client
                }
            });

            // Définir les en-têtes pour le téléchargement de fichier
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'no-cache');
            $response->headers->set('Content-Transfer-Encoding', 'binary');

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
