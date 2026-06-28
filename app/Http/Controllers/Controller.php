<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use App\Models\User;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;
use LaravelFCM\Facades\FCM;
use PHPUnit\Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function jsonError($statusCode = 500, $message = "Unexpected Error"): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "success" => false,
            "message" => $message
        ], $statusCode);
    }

    public function jsonSuccess($statusCode = 200, $message = "Request Successful", $data = [], $key): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "success" => true,
            "message" => $message,
            $key => $data
        ], $statusCode);
    }


    public function saveDeviceToken(User $user, $device_token)
    {
        $deviceToken = new DeviceToken();
        $deviceToken->user_id = $user->id;
        $deviceToken->device_token = $device_token;
        $deviceToken->save();
        return $deviceToken;
    }

    public function generateAccessToken($serviceAccountFile){
        //firebase messaging v1 access token
        $credentials = new ServiceAccountCredentials(null, $serviceAccountFile);
        $authToken = $credentials->fetchAuthToken();
        $accessToken = $authToken['access_token'] ?? null;

        return $accessToken;
    }

    function sendNotification($body, $title, $firebaseToken, $type, $notificationType)
    {

        $messaging = app('firebase.messaging');
        try{
            $message = [
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],

            'data' => [
                "type" => $notificationType,
                "model" => $type,
            ]
            ];
            $messaging->sendMulticast($message, $firebaseToken);

            return true;
        }catch (\Exception $e){
            return false;

    }


    }


    public function tokens($userId): array
    {
        $databaseStored = DeviceToken::where('user_id', '=', $userId)->get();
        $tokens = [];
        foreach ($databaseStored as $token) {
            array_push($tokens, $token->device_token);
        }
        return $tokens;
    }

}
