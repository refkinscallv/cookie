Here's how you can modify the documentation to include the installation guide and a sample `.env` file, while keeping the existing content intact.

---

# Cookie Library with Encryption Support

The `Cookie` class provides an advanced solution for managing cookies, with optional encryption support using the `Crypto` class. This library allows you to securely store and retrieve cookie data in both encrypted and plain formats.

---

## Features

- **Encrypted Cookies**: Use the `Crypto` library for secure cookie encryption and decryption.
- **Key-Value Storage**: Manage cookies as associative arrays for ease of use.
- **Customizable**: Configure expiration time, domain, path, and secure options.
- **Easy Integration**: Works seamlessly with both encrypted and non-encrypted cookie management.

---

## Installation

1. **Install the Package via Composer**

   To install the `refkinscallv/cookie` package, run the following command:

   ```bash
   composer require refkinscallv/cookie
   ```

   This command will download and install the library along with its dependencies.

2. **Include the Composer Autoloader**

   Make sure to include the Composer autoloader at the top of your PHP scripts to automatically load the classes:

   ```php
   require_once 'vendor/autoload.php';
   ```

3. **Environment Configuration**

   Make sure that your environment variables are properly configured for cookie management. You can do this by creating a `.env` file in the root directory of your project (if not already present) with the following contents:

   **Sample `.env` File**:

   ```env
   COOKIE_NAME=web_cookie
   COOKIE_EXPIRES=24
   COOKIE_PATH=/
   COOKIE_SECURE=false
   ```

   This file defines:
   - `COOKIE_NAME`: The name of the cookie.
   - `COOKIE_EXPIRES`: The cookie expiration time in hours (default is 24 hours).
   - `COOKIE_PATH`: The path on the server where the cookie is available.
   - `COOKIE_SECURE`: Whether the cookie should be marked as secure (use HTTPS only). Set to `false` by default.

4. **Using the Library**

   After installing the package and configuring your environment, you can now begin using the `Cookie` class in your project.

---

## Usage

### Basic Setup

#### Without Encryption

```php
use RF\Cookie\Cookie;

// Initialize Cookie class without encryption
$cookie = new Cookie();

// Set a cookie
$cookie->set('user', 'John Doe');

// Get a cookie value
echo $cookie->get('user'); // Outputs: John Doe

// Check if a cookie exists
if ($cookie->has('user')) {
    echo 'User cookie is set.';
}

// Unset a cookie
$cookie->unset('user');

// Destroy all cookies
$cookie->destroy();
```

#### With Encryption

```php
use RF\Crypto\Crypto;
use RF\Cookie\Cookie;

// Instantiate the Crypto class
$crypto = new Crypto([
    "encryptKey" => "your-secret-key",
    "encryptCipher" => "AES-256-CBC",
    "encryptStoreMethod" => "local",
    "encryptFile" => "/path/to/encrypt.txt"
]);

// Initialize Cookie class with encryption
$cookie = new Cookie($crypto);

// Set an encrypted cookie
$cookie->set('user', 'John Doe');

// Get an encrypted cookie value
echo $cookie->get('user'); // Outputs: John Doe

// Check if a cookie exists
if ($cookie->has('user')) {
    echo 'User cookie is set.';
}

// Unset an encrypted cookie
$cookie->unset('user');

// Destroy all encrypted cookies
$cookie->destroy();
```

---

## Configuration

### Constructor Parameters

```php
public function __construct(mixed $db = null)
```

- **`$db`** *(mixed)*: Pass a `Crypto` instance to enable encryption. Use `null` for plain cookie management.

### Environment Variables

| Variable         | Description                                  | Default Value      |
|-------------------|----------------------------------------------|--------------------|
| `COOKIE_NAME`     | The name of the cookie.                     | `web_cookie`       |
| `COOKIE_EXPIRES`  | Expiration time in hours.                   | `24` (1 day)       |
| `COOKIE_PATH`     | The path on the server where the cookie is available. | `/` |
| `COOKIE_SECURE`   | Use secure cookies (HTTPS only).             | `false`            |

---

## Methods

### `all()`
Retrieve all cookie data as an associative array.

```php
$cookies = $cookie->all();
```

### `get(string $key)`
Retrieve a specific cookie value.

```php
$value = $cookie->get('key');
```

### `has(string $key)`
Check if a cookie key exists.

```php
$exists = $cookie->has('key');
```

### `set(string|array $key, mixed $value = null)`
Set a cookie value. Supports setting multiple key-value pairs.

```php
$cookie->set('key', 'value');
$cookie->set(['key1' => 'value1', 'key2' => 'value2']);
```

### `unset(string|array $key)`
Remove a specific cookie or multiple cookies.

```php
$cookie->unset('key');
$cookie->unset(['key1', 'key2']);
```

### `destroy()`
Remove all cookies.

```php
$cookie->destroy();
```

---

## Notes

- **Encryption Dependency**: To enable encryption, ensure the `Crypto` class is correctly configured and passed to the `Cookie` constructor.
- **Headers Sent**: Ensure that cookies are set before any output is sent to the browser to avoid `headers already sent` errors.

---

## Example with Database Encryption

To use database-based encryption with cookies, you can leverage the [Crypto Library](https://github.com/refkinscallv/crypto). This library supports multiple storage methods, including databases.

### Example

```php
use RF\Crypto\Crypto;
use RF\Cookie\Cookie;

// Configure Crypto with database storage
$crypto = new Crypto([
    "encryptKey" => "your-secret-key",
    "encryptCipher" => "AES-256-CBC",
    "encryptStoreMethod" => "database",
    "encryptDBHandler" => function($data, $mode) {
        // Database logic for storing and retrieving encrypted data
        if ($mode === 'save') {
            // Save $data to the database
        } elseif ($mode === 'load') {
            // Retrieve and return encrypted data from the database
        }
    }
]);

// Initialize Cookie class with database encryption
$cookie = new Cookie($crypto);

// Set and retrieve an encrypted cookie
$cookie->set('user', 'Encrypted User');
echo $cookie->get('user'); // Outputs: Encrypted User
```

For a full tutorial and detailed documentation on configuring the Crypto library, visit the [Crypto Library GitHub Repository](https://github.com/refkinscallv/crypto).