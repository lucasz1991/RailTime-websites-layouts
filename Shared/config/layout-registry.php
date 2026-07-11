<?php
declare(strict_types=1);

/**
 * URL path of the repository, e.g. "", "/RailTime" or "/projects/railtime".
 * It is derived from the request so the same checkout works in Plesk and XAMPP.
 */
function rt_project_base_url(): string {
    static $base;
    if ($base !== null) {
        return $base;
    }

    $requestPath = rawurldecode((string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
    $requestPath = str_replace('\\', '/', $requestPath);

    if (preg_match('~^(.*?)/layouts(?:/|$)~i', $requestPath, $match)) {
        $base = rtrim($match[1], '/');
        return $base;
    }

    $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    $base = rtrim(str_replace('%2F', '/', rawurlencode(dirname($scriptName))), '/.');
    return $base === '/' ? '' : $base;
}

function rt_project_url(string $path = ''): string {
    $encoded = implode('/', array_map('rawurlencode', explode('/', ltrim($path, '/'))));
    return rt_project_base_url() . ($encoded === '' ? '/' : '/' . $encoded);
}

function rt_layout_registry(): array {
    static $layouts;
    if ($layouts !== null) {
        return $layouts;
    }

    $root = dirname(__DIR__, 2);
    $groups = [
        ['base' => $root . '/Codex', 'folders' => ['Layout Entwurf 1', 'Layout Entwurf 2', 'Layout Entwurf 3', 'Layout Entwurf 4', 'Layout Entwurf 5']],
        ['base' => $root . '/Claude', 'folders' => ['L1', 'L2']],
    ];

    $layouts = [];
    $id = 1;
    foreach ($groups as $group) {
        foreach ($group['folders'] as $folder) {
            $dir = $group['base'] . '/' . $folder;
            if (!is_dir($dir)) {
                continue;
            }
            $layouts[$id] = [
                'id' => $id,
                'dir' => $dir,
                'folder' => $folder,
                'public_path' => 'layouts/' . $id,
            ];
            $id++;
        }
    }

    return $layouts;
}

function rt_layout_public_by_id(int $id): ?array {
    $layouts = rt_layout_registry();
    return $layouts[$id] ?? null;
}
