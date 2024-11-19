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
            $fileData = $this->googleDriveService->downloadFileStreamByName($fileName);
            $stream = $fileData['stream'];
            $fileSize = $fileData['size'];

            $response = new StreamedResponse(function () use ($stream, $fileSize) {
                $chunkSize = 1024 * 1024 * 10; // Taille du morceau (1 Mo)
                $bytesSent = 0;

                flush();
                while (!$stream->eof()) {
                    $data = $stream->read($chunkSize);
                    echo $data;
                    flush();

                    $bytesSent += strlen($data);
                }
            });

            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            $response->headers->set('Content-Length', (string)$fileSize);

            return $response;
        } catch (\Exception $e) {
            return new Response('Error downloading file: ' . $e->getMessage(), 500);
        }
    }



    #[Route('/api/game/upload', name: "file_upload")]
    public function upload(Request $request)
    {
        $file = $request->files->get('file');
        if (!$file) {
            return new JsonResponse(['error' => 'No file uploaded'], 400);
        }

        $refreshToken = $request->get('refresh_token');
        if (!$refreshToken) {
            return new JsonResponse(['error' => 'Refresh token is required'], 400);
        }

        $this->googleDriveService->setRefreshToken($refreshToken);

        $uploadResult = $this->googleDriveService->uploadFile($file->getPathname());

        // Si une erreur est survenue lors de l'upload
        if (isset($uploadResult['error'])) {
            return new JsonResponse(['error' => $uploadResult['error']], 500);
        }

        return new JsonResponse(['message' => 'File uploaded', 'fileId' => $uploadResult['fileId']]);
    }


    #[Route('/api/gamesLists')]
    public function listFiles(): JsonResponse
    {
        try {
            $fileList = $this->googleDriveService->listFiles();

            return new JsonResponse(['files' => $fileList]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error listing files: ' . $e->getMessage()], 500);
        }
    }
}
