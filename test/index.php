<?php

    use RF\Cookie\Cookie;

    require "../vendor/autoload.php";

    $cookie = new Cookie([
        'cookieName' => 'myCookie',
        'cookieExpires' => 24, // Cookie expiration in hours
        'cookiePath' => '/',
        'cookieDomain' => $_SERVER['SERVER_NAME'],
        'cookieSecure' => false, // Set to true if using HTTPS
        'cookieHttpOnly' => true,
        'cookieEncrypt' => [
            "encryptKey" => "your-secret-key",
            "encryptCipher" => "AES-256-CBC",
            "encryptStoreMethod" => "local",
            "encryptFile" => "/path/to/encrypt.txt"
        ]
    ]);
    
    // Set a cookie value
    $cookie->set('key', 'value');
    
    // Get a cookie value
    $value = $cookie->get('key');
    
    // Retrieve all cookie data
    $allData = $cookie->all();
    
    // Unset a cookie value
    $cookie->unset('key');
    
    // Destroy the cookie
    $cookie->destroy();