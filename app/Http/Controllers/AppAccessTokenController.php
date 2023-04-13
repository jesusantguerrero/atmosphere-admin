<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Bridge\User as UserEntity;
use Laravel\Passport\Client;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\ResponseTypes\JsonResponse;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;

class AppAccessTokenController extends AccessTokenController
{
    public function issueToken(ServerRequestInterface $request)
    {
        $server = app(AuthorizationServer::class);
        $client = Client::where('password_client', true)->first();

        $request = $this->getServerRequest($request);

        $response = $server->respondToAccessTokenRequest($request, new \League\OAuth2\Server\ResponseTypes\JsonResponseBuilder());

        $psr17Factory = new Psr17Factory();

        $psrResponse = $psr17Factory->createResponse($response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $psrResponse = $psrResponse->withHeader($name, $value);
            }
        }

        $psrResponse = $psrResponse->withBody($psr17Factory->createStream($response->getBody()->__toString()));

        $content = json_decode($response->getBody(), true);
        $user = $request->user();

        if (isset($content['access_token'])) {
            $content['user'] = $user;
        }

        $laravelResponse = Passport::response($psrResponse);
        $laravelResponse->setContent(json_encode($content));

        return $laravelResponse;
    }

    private function getServerRequest(Request $request): ServerRequestInterface
    {
        $psr17Factory = new Psr17Factory();

        $psrRequest = $psr17Factory->createServerRequest($request->getMethod(), $request->url());

        foreach ($request->header() as $name => $value) {
            $psrRequest = $psrRequest->withHeader($name, $value);
        }

        if ($request->isMethod('post')) {
            $parsedBody = $request->all();
            $psrRequest = $psrRequest->withParsedBody($parsedBody);
        }

        return $psrRequest;
    }
}
