<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MicrosoftGraphController extends Controller
{
    protected $clientId;
    protected $clientSecret;
    protected $tenantId;
    protected $redirectUri;
    protected $scopes;
    protected $client;

    public function __construct()
    {
        $this->clientId = config('services.microsoft.client_id');
        $this->clientSecret = config('services.microsoft.client_secret');
        $this->tenantId = config('services.microsoft.tenant_id');
        $this->redirectUri = config('services.microsoft.redirect_uri');
        $this->scopes = config('services.microsoft.scopes');
        $this->client = new Client();
    }

    public function redirectToAuth()
    {
        $authorizationUrl = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/authorize?' . http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
            'response_mode' => 'query',
            'scope' => $this->scopes,
            'state' => 'random_string',
        ]);

        return redirect()->away($authorizationUrl);
    }



    public function handleCallback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return response()->json(['error' => 'Authorization code is missing.'], 400);
        }

        try {
            $response = $this->client->post('https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/v2.0/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                    'scope' => $this->scopes,
                ],
                'verify' => false,
            ]);

            $token = json_decode($response->getBody()->getContents(), true);
            session(['access_token' => $token['access_token']]);

            return redirect()->route('list.files');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function listFiles(Request $request)
    {
        $accessToken = session('access_token');

        if (!$accessToken) {
            return redirect()->route('auth.redirect');
        }

        $folderId = $request->query('folderId', 'root');
        $searchQuery = $request->query('search', '');

        try {
            // Obtener el nombre del usuario
            $userResponse = $this->client->get('https://graph.microsoft.com/v1.0/me', [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                ],
                'verify' => false,
            ]);

            $user = json_decode($userResponse->getBody(), true);
            $name = $user['displayName'] ?? 'No Name Found';

            // Obtener la lista de archivos
            $apiUrl = $searchQuery
                ? "https://graph.microsoft.com/v1.0/me/drive/root/search(q='$searchQuery')"
                : ($folderId === 'shared'
                    ? 'https://graph.microsoft.com/v1.0/me/drive/sharedWithMe'
                    : ($folderId === 'root'
                        ? 'https://graph.microsoft.com/v1.0/me/drive/root/children'
                        : "https://graph.microsoft.com/v1.0/me/drive/items/$folderId/children"));

            $filesResponse = $this->client->get($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
                'verify' => false,
            ]);

            $files = json_decode($filesResponse->getBody()->getContents(), true);

            // Determinar el parentFolderId
            $parentFolderId = $folderId === 'root' ? null : $folderId; // Asignar correctamente

            // Si estamos en un folder específico, obtén el parent
            if ($folderId !== 'root' && $folderId !== 'shared') {
                $folderDetails = $this->client->get("https://graph.microsoft.com/v1.0/me/drive/items/$folderId", [
                    'headers' => [
                        'Authorization' => "Bearer $accessToken",
                    ],
                    'verify' => false,
                ]);
                $folderData = json_decode($folderDetails->getBody()->getContents(), true);
                $parentFolderId = $folderData['parentReference']['id'] ?? null; // Obtener el ID de la carpeta padre
            }

            return view('files.list', [
                'files' => $files['value'],
                'currentFolderId' => $folderId,
                'parentFolderId' => $parentFolderId,
                'searchQuery' => $searchQuery,
                'name' => $name, // Pasar el nombre del usuario a la vista
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function listSharedFiles(Request $request)
    {
        $accessToken = session('access_token');

        if (!$accessToken) {
            return redirect()->route('auth.redirect');
        }

        $folderId = $request->query('folderId', 'root');
        $apiUrl = $folderId === 'root'
            ? 'https://graph.microsoft.com/v1.0/me/drive/sharedWithMe'
            : "https://graph.microsoft.com/v1.0/me/drive/items/$folderId/children";

        try {
            $response = $this->client->get($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
                'verify' => false, // Desactiva la verificación SSL si es necesario
            ]);

            $files = json_decode($response->getBody()->getContents(), true);

            return view('files.list', [
                'files' => $files['value'],
                'currentFolderId' => $folderId,
                'parentFolderId' => $folderId === 'root' ? null : $folderId
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
}
