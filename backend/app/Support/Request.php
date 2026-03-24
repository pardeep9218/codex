<?php

declare(strict_types=1);

namespace App\Support;

final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query,
        public readonly array $headers,
        public readonly array $body,
    ) {}

    public static function capture(): self
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', substr($key, 5));
                $headers[strtoupper($name)] = (string) $value;
            }
        }

        $body = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($body)) {
            $body = [];
        }

        return new self($method, $path, $_GET, $headers, $body);
    }

    public function header(string $name, string $default = ''): string
    {
        return $this->headers[strtoupper($name)] ?? $default;
    }
}
