# How to use Oauth2 access token of auth server to authorize users in resource/app server?

This package is inspired by the issue founded on [on the StackOverflow](https://stackoverflow.com/questions/45389511/how-to-use-oauth2-access-token-of-auth-server-to-authorize-users-in-resource-app). 

Package is aimed towards those who wants to implement the following situations

1. Auth server on Laravel (central user directory, OAuth2 using laravel/passport)
2. Resource servers on Laravel or Lumen (web apps, no user tables)
3. Client side JS app (Nuxt, React...)

*Note* worth noting that the User model must exists. You just need to change the connection to the database where the Auth server is.

## What is the workflow?

1. Login button on Client takes to auth server, oauth2 client is authorized by user and get auth code and redirect back to client.
2. Client then uses request to send data to resource server, with also providing the resource server the credentials.
3. Resource server with this guard authorizes the request so you can keep your API secure.

## Instalation

Via composer

``` bash
$ composer require revenuefm/ssoguard
```

After you installed the package publish the config

```bash
php artisan vendor:publish --tag=ssoguard-config
```

Open the config file and add the route to the `me` object where you are fetching the user object on Auth server. 
For example `https://your-domain.com/api/user`.

## Using the guard

Simple as is, change the API guard driver in your config `auth` to:

```php

'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'ssoguard',
            'provider' => 'users',
        ],
    ],
```

After that you can use the standard API middleware

```php
Route::get('my/secure/api-url', 'MyController@index')->middleware('auth:api');
```
The user object will be available from

```php
$request->user()
```

as standard one. 

If somebody wants to grow this package and make it better, please involve.

Best!
