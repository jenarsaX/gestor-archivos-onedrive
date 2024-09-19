<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

class FileController extends Controller
{
    public function download(Request $request)
    {
        $fileId = $request->query('file_id');
        $accessToken = Session::get('access_token');

        if (!$fileId || !$accessToken) {
            return response()->json(['error' => 'File ID not provided or access token not available.'], 400);
        }

        $client = new Client();

        try {
            $response = $client->get("https://graph.microsoft.com/v1.0/me/drive/items/$fileId/content", [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                ],
                // Obtén el contenido del archivo en la respuesta
                'stream' => true,
            ]);

            // Configura el tipo de contenido y el nombre del archivo
            return response()->stream(
                function () use ($response) {
                    // Escribe el cuerpo de la respuesta directamente en la respuesta de Laravel
                    echo $response->getBody()->getContents();
                },
                $response->getStatusCode(),
                [
                    'Content-Type' => $response->getHeaderLine('Content-Type'),
                    'Content-Disposition' => 'attachment; filename="' . basename($response->getHeaderLine('Content-Disposition')) . '"',
                    'Content-Length' => $response->getHeaderLine('Content-Length'),
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Download error: ' . htmlspecialchars($e->getMessage())], 500);
        }
    }
}
