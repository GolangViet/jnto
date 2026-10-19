<?php

declare(strict_types=1);

namespace Core;

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use RuntimeException;

final class Application
{
    private static ?self $instance = null;
    private array $config = [];
    private Router $router;
    private Request $request;
    private Response $response;
    private Session $session;
    private Logger $logger;

    /**
     * Application constructor.
     *
     * @param string $basePath The base path of the application.
     */
    public function __construct(private readonly string $basePath)
    {
        self::$instance = $this;

        $this->loadEnvironment();
        $this->loadConfig();

        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->logger = new Logger('app');
        $this->logger->pushHandler(new StreamHandler($this->basePath . '/storage/logs/app.log'));
    }

    /**
     * Get the singleton instance of the Application.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            throw new RuntimeException('Application has not been initialized.');
        }

        return self::$instance;
    }

    /**
     * Load environment variables from the .env file if it exists.
     *
     * @return void
     */
    private function loadEnvironment(): void
    {
        $envFile = $this->basePath . '/.env';
        if (is_file($envFile)) {
            Dotenv::createImmutable($this->basePath)->safeLoad();
        }
    }

    /**
     * Load configuration files from the config directory.
     *
     * @return void
     */
    private function loadConfig(): void
    {
        foreach (glob($this->basePath . '/config/*.php') ?: [] as $file) {
            $this->config[pathinfo($file, PATHINFO_FILENAME)] = require $file;
        }
    }

    /**
     * Get a configuration value using dot notation.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function config(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function router(): Router { return $this->router; }
    public function request(): Request { return $this->request; }
    public function response(): Response { return $this->response; }
    public function session(): Session { return $this->session; }
    public function logger(): Logger { return $this->logger; }
    public function basePath(string $path = ''): string { return $this->basePath . ($path ? '/' . ltrim($path, '/') : ''); }

    public function run(): void
    {
        try {
            $this->router->dispatch();
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            if ((bool) $this->config('app.debug', false)) {
                http_response_code(500);
                echo '<pre>' . e((string) $e) . '</pre>';
                return;
            }

            http_response_code(500);
            echo 'Internal Server Error';
        } finally {
            $this->session->flashOldInput([]);
        }
    }
}
