<?php

    namespace RF\Cookie;

    use Exception;

    /**
     * Class to handle encrypted cookies with enhanced functionality.
     */
    class Cookie
    {
        
        /**
         * @var string Cookie name.
         */
        protected string $cookieName;

        /**
         * @var int Cookie expiration time in hours.
         */
        protected int $cookieExpire;

        /**
         * @var string Cookie path.
         */
        protected string $cookiePath;

        /**
         * @var string|null Cookie domain.
         */
        protected ?string $cookieDomain;

        /**
         * @var bool Cookie secure flag.
         */
        protected bool $cookieSecure;

        /**
         * @var mixed|null Encryption handler or null if encryption is disabled.
         */
        protected mixed $crypto;

        /**
         * @var bool Whether encryption is enabled.
         */
        protected bool $cookieEncrypt;

        /**
         * Cookie constructor.
         *
         * @param mixed|null $db Encryption handler or null to disable encryption.
         */
        public function __construct(mixed $db = null)
        {
            $this->cookieName = $_SERVER['COOKIE_NAME'] ?? 'web_cookie';
            $this->cookieExpire = (int)($_SERVER['COOKIE_EXPIRES'] ?? 24);
            $this->cookiePath = $_SERVER['COOKIE_PATH'] ?? '/';
            $this->cookieDomain = $_SERVER['SERVER_NAME'] ?? null;
            $this->cookieSecure = filter_var($_SERVER['COOKIE_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $this->crypto = $db;
            $this->cookieEncrypt = $db !== null;
        }

        /**
         * Retrieve all cookies.
         *
         * @return array Parsed cookie data or an empty array.
         * @throws Exception If cookie data cannot be decrypted or unserialized.
         */
        public function all(): array
        {
            if (!isset($_COOKIE[$this->cookieName])) {
                return [];
            }

            $cookieData = $_COOKIE[$this->cookieName];

            try {
                if ($this->cookieEncrypt) {
                    return $this->crypto->decrypt($cookieData, 'array');
                }
                $unserializedData = @unserialize($cookieData);
                if ($unserializedData === false && $cookieData !== 'b:0;') {
                    throw new Exception('Failed to unserialize cookie data.');
                }
                return $unserializedData ?: [];
            } catch (Exception $e) {
                throw new Exception('Failed to retrieve cookies: ' . $e->getMessage(), 0, $e);
            }
        }

        /**
         * Get a specific cookie value by key.
         *
         * @param string $key The key of the cookie to retrieve.
         * @return mixed|null The value of the cookie or null if not found.
         */
        public function get(string $key): mixed
        {
            $allCookies = $this->all();
            return $allCookies[$key] ?? null;
        }

        /**
         * Check if a specific cookie key exists.
         *
         * @param string $key The key to check.
         * @return bool True if the key exists, false otherwise.
         */
        public function has(string $key): bool
        {
            return $this->get($key) !== null;
        }

        /**
         * Set a cookie value or values.
         *
         * @param string|array $key The key or an array of key-value pairs to set.
         * @param mixed|null $value The value to set if $key is a string.
         * @return bool True on success.
         * @throws Exception If the headers are already sent or setting the cookie fails.
         */
        public function set(string|array $key, mixed $value = null): bool
        {
            $allCookies = $this->all();

            if (is_array($key)) {
                foreach ($key as $idx => $val) {
                    $allCookies[$idx] = $val;
                }
            } else {
                $allCookies[$key] = $value;
            }

            $setCookie = $this->cookieEncrypt
                ? $this->crypto->encrypt($allCookies, 'array')
                : serialize($allCookies);

            if (headers_sent()) {
                throw new Exception('Cannot set cookie; headers already sent.');
            }

            if (!setcookie(
                $this->cookieName,
                $setCookie,
                [
                    'expires' => time() + ($this->cookieExpire * 3600),
                    'path' => $this->cookiePath,
                    'secure' => $this->cookieSecure,
                    'httponly' => true
                ]
            )) {
                throw new Exception('Failed to set cookie: ' . $this->cookieName);
            }

            return true;
        }

        /**
         * Unset a cookie by key.
         *
         * @param string|array $key The key or an array of keys to unset.
         * @return bool True on success.
         * @throws Exception If setting the cookie fails.
         */
        public function unset(string|array $key): bool
        {
            $allCookies = $this->all();

            if (is_array($key)) {
                foreach ($key as $idx) {
                    unset($allCookies[$idx]);
                }
            } else {
                unset($allCookies[$key]);
            }

            $setCookie = $this->cookieEncrypt
                ? $this->crypto->encrypt($allCookies, 'array')
                : serialize($allCookies);

            if (!setcookie(
                $this->cookieName,
                $setCookie,
                [
                    'expires' => time() + ($this->cookieExpire * 3600),
                    'path' => $this->cookiePath,
                    'secure' => $this->cookieSecure,
                    'httponly' => true
                ]
            )) {
                throw new Exception('Failed to unset cookie: ' . $this->cookieName);
            }

            return true;
        }

        /**
         * Destroy all cookies.
         *
         * @return bool True on success.
         */
        public function destroy(): bool
        {
            return setcookie(
                $this->cookieName,
                '',
                [
                    'expires' => time() - 3600,
                    'path' => $this->cookiePath,
                    'secure' => $this->cookieSecure,
                    'httponly' => true
                ]
            );
        }

    }
