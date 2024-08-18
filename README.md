# refkinscallv/cookie

`refkinscallv/cookie` is a PHP package for managing cookies with optional encryption support. It integrates with the `refkinscallv/crypto` package to provide encrypted cookie handling.

## Installation

To install the package, use Composer:

```bash
composer require refkinscallv/cookie
```

## Configuration

Before using the `Cookie` class, ensure you have the `refkinscallv/crypto` package installed as it provides the encryption functionality. For details on configuring `cookieEncrypt`, refer to the [crypto documentation](https://github.com/refkinscallv/crypto).

## Usage

### Basic Example

```php
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
        /* Optional */
        // 'cookieEncrypt' => [
        //     "encryptKey" => "your-secret-key",
        //     "encryptCipher" => "AES-256-CBC",
        //     "encryptStoreMethod" => "local",
        //     "encryptFile" => "/path/to/encrypt.txt"
        // ]
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
```

### Class Methods

#### `__construct(array $args = [])`

Initializes the `Cookie` instance with configuration options. 

- `cookieName` (string, required): The name of the cookie.
- `cookieExpires` (int, optional): Expiration time in hours (default: 24).
- `cookiePath` (string, optional): Path on the server where the cookie will be available (default: `/`).
- `cookieDomain` (string, optional): Domain of the cookie (default: current server name).
- `cookieSecure` (bool, optional): Indicates if the cookie should only be sent over HTTPS (default: false).
- `cookieHttpOnly` (bool, optional): Indicates if the cookie is accessible only through the HTTP protocol (default: true).
- `cookieEncrypt` (array, optional): Encryption settings for the cookie data. [crypto documentation](https://github.com/refkinscallv/crypto).

#### `all(): array`

Retrieves all encrypted cookie data as an array.

#### `get(string $value)`

Retrieves a specific value from the decrypted cookie data.

#### `set($key, $value = null): bool`

Sets encrypted cookie data. Accepts an associative array or a single key-value pair.

#### `unset($key = null): bool`

Unsets encrypted cookie data. Accepts a single key or an array of keys.

#### `destroy()`

Destroys the cookie by setting its expiration time to the past.

## Notes

- Ensure you handle encryption keys and settings securely.
- This package requires the `refkinscallv/crypto` package for encryption functionality. Refer to its documentation for configuration details.

For further assistance, please refer to the [crypto documentation](https://github.com/refkinscallv/crypto) or open an issue on the package's repository.