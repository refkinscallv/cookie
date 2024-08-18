<?php

    namespace RF\Cookie;

    use RF\Crypto\Crypto;
    use Exception;

    class Cookie {

        /**
         * @var string $cookieName
         */
        protected string $cookieName;

        /**
         * @var int $cookieExpire
         */
        protected int $cookieExpire;

        /**
         * @var string $cookiePath
         */
        protected string $cookiePath;

        /**
         * @var string $cookieDomain
         */
        protected string $cookieDomain;

        /**
         * @var bool $cookieSecure
         */
        protected bool $cookieSecure;

        /**
         * @var bool $cookieHttpOnly
         */
        protected bool $cookieHttpOnly;

        /**
         * @var Crypto|null $crypto
         */
        protected ?Crypto $crypto;

        /**
         * @var callable|null $cookieEncrypt
         */
        protected $cookieEncrypt;

        public function __construct(array $args = []) {
            $this->validateArgs($args);

            if ($this->cookieEncrypt) {
                $this->crypto = new Crypto($this->cookieEncrypt);
            }
        }

        private function validateArgs(array $args): void {
            if (!isset($args["cookieName"])) {
                throw new Exception("Invalid Cookie Config. Array index 'cookieName' not found.");
            }

            $this->cookieName = $args["cookieName"];
            $this->cookieExpire = $args["cookieExpires"] ?? 24;
            $this->cookiePath = $args["cookiePath"] ?? "/";
            $this->cookieDomain = $args["cookieDomain"] ?? $_SERVER["SERVER_NAME"];
            $this->cookieSecure = $args["cookieSecure"] ?? false;
            $this->cookieHttpOnly = $args["cookieHttpOnly"] ?? true;
            $this->cookieEncrypt = $args["cookieEncrypt"] ?? [];
        }

        /**
         * Retrieve all encrypted cookie data.
         *
         * @return array
         */
        public function all(): array {
            if (!isset($_COOKIE[$this->cookieName])) {
                return [];
            }

            $cookieData = $_COOKIE[$this->cookieName];

            if ($this->crypto) {
                $decryptedData = $this->crypto->decrypt($cookieData, "array");
                return $decryptedData;
            }

            return $cookieData ? @unserialize($cookieData) : [];
        }

        /**
         * Get specific value from decrypted cookie data.
         *
         * @param string $value
         * @return mixed|null
         */
        public function get(string $value) {
            $getCookie = $this->all();

            return $getCookie[$value] ?? null;
        }

        /**
         * Set encrypted cookie data.
         *
         * @param mixed $key
         * @param mixed $value
         * @return bool
         */
        public function set($key, $value = null): bool {
            $getCookie = $this->all();

            if (is_array($key)) {
                foreach ($key as $index => $val) {
                    $getCookie[$index] = $val;
                }
            } else {
                $getCookie[$key] = $value;
            }

            $setCookie = $this->crypto ? $this->crypto->encrypt($getCookie, "array") : serialize($getCookie);

            return setcookie(
                $this->cookieName,
                $setCookie,
                time() + ($this->cookieExpire * 3600),
                $this->cookiePath,
                $this->cookieDomain,
                $this->cookieSecure,
                $this->cookieHttpOnly
            );
        }

        /**
         * Unset encrypted cookie data.
         *
         * @param mixed $key
         * @return bool
         */
        public function unset($key = null): bool {
            $getCookie = $this->all();

            if (is_array($key)) {
                foreach ($key as $index) {
                    unset($getCookie[$index]);
                }
            } else {
                unset($getCookie[$key]);
            }

            $setCookie = $this->crypto ? $this->crypto->encrypt($getCookie, "array") : serialize($getCookie);

            return setcookie(
                $this->cookieName,
                $setCookie,
                time() + ($this->cookieExpire * 3600),
                $this->cookiePath,
                $this->cookieDomain,
                $this->cookieSecure,
                $this->cookieHttpOnly
            );
        }

        /**
         * Destroy cookie data.
         */
        public function destroy() {
            return setcookie($this->cookieName, "", time() - 3600, "/");
        }
    }
