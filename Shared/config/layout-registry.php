<?php
declare(strict_types=1);

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

