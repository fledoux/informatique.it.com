<?php
// src/Service/AssetVersionStrategy.php

namespace App\Service;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

final class AssetVersionStrategy implements VersionStrategyInterface
{
    private string $publicDir;

    public function __construct(?string $publicDir = null)
    {
        // Si non injecté via services.yaml, on retombe sur /public
        $this->publicDir = rtrim($publicDir ?? dirname(__DIR__, 2) . '/public', '/');
    }

    public function getVersion(string $path): string
    {
        $file = $this->publicDir . $this->normalize($path);
        return is_file($file) ? (string) filemtime($file) : (string) time();
    }

    public function applyVersion(string $path): string
    {
        $v = $this->getVersion($path);
        $sep = str_contains($path, '?') ? '&' : '?';
        return $path . $sep . 'v=' . $v;
    }

    private function normalize(string $path): string
    {
        if ($path === '') return '';
        return $path[0] === '/' ? $path : '/' . $path;
    }
}