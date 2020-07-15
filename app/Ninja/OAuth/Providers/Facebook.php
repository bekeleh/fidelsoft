<?php 

namespace App\Ninja\OAuth\Providers;

use Facebook_Client;

class Facebook implements ProviderInterface
{

    public function getTokenResponse($token)
    {

        $client = new Facebook_Client();
        return $client->verifyIdToken($token);
    }

    public function harvestEmail($payload)
    {
        return $payload['email'];
    }

    public function harvestSubField($payload)
    {
        return $payload['sub']; // user ID
    }
}
