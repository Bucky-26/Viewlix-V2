<?php
// Simple BOM / leading output detector for PHP files
$dirs = [
    __DIR__ . '/..' . DIRECTORY_SEPARATOR . 'app',
    __DIR__ . '/..' . DIRECTORY_SEPARATOR . 'bootstrap',
    __DIR__ . '/..' . DIRECTORY_SEPARATOR . 'routes',
    __DIR__ . '/..' . DIRECTORY_SEPARATOR . 'resources',
    __DIR__ . '/..' . DIRECTORY_SEPARATOR . 'vendor',
];

$results = [];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if ($file->isDir()) continue;
        $path = $file->getPathname();
        if (substr($path, -4) !== '.php') continue;
        $contents = @file_get_contents($path);
        if ($contents === false) continue;

        // BOM check
        if (substr($contents, 0, 3) === "\xEF\xBB\xBF") {
            $results[] = ["BOM", $path];
            continue;
        }

        // Any non-whitespace before <?php
        $leading = preg_replace('/^[\r\n\t \x00\x0B]*/', '', $contents);
        if (strpos($leading, '<?php') !== 0) {
            $prefix = substr($contents, 0, 60);
            $results[] = ["LEADING", $path, bin2hex(substr($contents,0,8)), $prefix];
            continue;
        }

        // Trailing output after closing tag
        $pos = strrpos($contents, '?>');
        if ($pos !== false) {
            $after = substr($contents, $pos + 2);
            if (strlen(trim($after)) > 0) {
                $results[] = ["TRAILING", $path, strlen($after)];
            }
        }
    }
}

if (empty($results)) {
    echo "No BOM or leading output detected in scanned dirs\n";
    exit(0);
}

foreach ($results as $r) {
    echo implode(' | ', $r) . "\n";
}
exit(0);
