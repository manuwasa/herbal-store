<?php

/**
 * Downloads the Tailwind CSS Standalone CLI binary for the current OS/architecture
 * into bin/, so it never has to be committed to git (it bundles a JS runtime, so it's
 * 90-140MB depending on platform — well over GitHub's 100MB per-file limit).
 *
 * Usage: php bin/download-tailwindcss.php (also wired into `composer run setup`).
 */

$destination = PHP_OS_FAMILY === 'Windows'
    ? __DIR__ . '/tailwindcss.exe'
    : __DIR__ . '/tailwindcss';

if (file_exists($destination)) {
    fwrite(STDOUT, "Tailwind CLI already present at {$destination}, skipping download.\n");
    exit(0);
}

function isArm64(): bool
{
    $machine = strtolower(php_uname('m'));

    return str_contains($machine, 'arm64') || str_contains($machine, 'aarch64');
}

$asset = match (PHP_OS_FAMILY) {
    'Windows' => 'tailwindcss-windows-' . (isArm64() ? 'arm64' : 'x64') . '.exe',
    'Darwin' => 'tailwindcss-macos-' . (isArm64() ? 'arm64' : 'x64'),
    'Linux' => 'tailwindcss-linux-' . (isArm64() ? 'arm64' : 'x64'),
    default => null,
};

if ($asset === null) {
    fwrite(STDERR, 'Unsupported OS (' . PHP_OS_FAMILY . "). Download the Tailwind Standalone CLI\n"
        . "manually from https://github.com/tailwindlabs/tailwindcss/releases/latest\n"
        . "and save it as {$destination}.\n");
    exit(1);
}

$url = "https://github.com/tailwindlabs/tailwindcss/releases/latest/download/{$asset}";

fwrite(STDOUT, "Downloading Tailwind CLI ({$asset})...\n");

$context = stream_context_create(['http' => ['follow_location' => 1, 'timeout' => 120]]);

if (!@copy($url, $destination, $context)) {
    fwrite(STDERR, "Download failed. Check your internet connection, or download manually from\n"
        . "{$url}\nand save it as {$destination}.\n");
    exit(1);
}

if (PHP_OS_FAMILY !== 'Windows') {
    chmod($destination, 0755);
}

fwrite(STDOUT, "Saved to {$destination}\n");
