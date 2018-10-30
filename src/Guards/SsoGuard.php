<?php
namespace Revenuefm\Ssoguard\Guards;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

/**
 * Created by PhpStorm.
 * User: gorankrgovic
 * Date: 10/30/18
 * Time: 9:18 AM
 */

class SsoGuard
{

    /**
     * The user provider implementation.
     *
     * @var \Illuminate\Contracts\Auth\UserProvider
     */
    protected $provider;


    public function __construct(UserProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Get the user for the incoming request.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function user(Request $request)
    {
        if ($request->bearerToken()) {
            return $this->authenticateViaOauthServer($request->bearerToken());
        }
    }

    /**
     * Authenticate via oAuth server
     *
     * @param $token
     * @return mixed, \Illuminate\Contracts\Auth\Authenticatable|null|void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function authenticateViaOauthServer($token)
    {
        $client = new Client();

        try {
            $res = $client->request('GET', config('ssoguard.oauth_me'), [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            // Get the body as json decoded
            $response = json_decode($res->getBody());

            // Fetch the user by id
            $user = $this->provider->retrieveById(
                $response->id
            );

            if (!$user) {
                return;
            }

            return $user;

        } catch (Exception $e) {
            // Something is wrong
            return;
        }
    }
}
