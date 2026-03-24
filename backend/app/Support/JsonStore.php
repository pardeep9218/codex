<?php

declare(strict_types=1);

namespace App\Support;

final class JsonStore
{
    public function __construct(private readonly string $storageDir)
    {
    }

    public function read(string $file, array $default): array
    {
        $path = $this->storageDir . '/' . $file;
        if (!is_file($path)) {
            $this->write($file, $default);
            return $default;
        }

        $content = json_decode((string) file_get_contents($path), true);
        return is_array($content) ? $content : $default;
    }

    public function write(string $file, array $payload): void
    {
        $path = $this->storageDir . '/' . $file;
        file_put_contents($path, json_encode($payload, JSON_PRETTY_PRINT));
    }
}
