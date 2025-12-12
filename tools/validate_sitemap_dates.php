<?php
// Strict sitemap <lastmod> date validator
// Usage: php tools/validate_sitemap_dates.php <sitemap-url-or-path>

if ($argc < 2) {
    echo "Usage: php tools/validate_sitemap_dates.php <sitemap-url-or-path>\n";
    exit(2);
}

$source = $argv[1];

$content = @file_get_contents($source);
if ($content === false) {
    fwrite(STDERR, "Failed to read source: $source\n");
    exit(3);
}

// Ensure UTF-8 and strip BOM if present
if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
    $content = substr($content, 3);
}

libxml_use_internal_errors(true);
$xml = simplexml_load_string($content);
if ($xml === false) {
    fwrite(STDERR, "Failed to parse XML. libxml errors:\n");
    foreach (libxml_get_errors() as $err) {
        fwrite(STDERR, trim($err->message) . "\n");
    }
    exit(4);
}

$lines = preg_split("/\r?\n/", $content);

$lastmods = $xml->xpath('//lastmod');
if ($lastmods === false) {
    echo "No <lastmod> elements found.\n";
    exit(0);
}

$invalid = [];
$indexOffset = 0; // position offset used when searching lines for occurrences

foreach ($lastmods as $lm) {
    $d = trim((string)$lm);
    // Strictly accept YYYY-MM-DD only
    $dt = DateTime::createFromFormat('Y-m-d', $d);
    $ok = $dt && $dt->format('Y-m-d') === $d;

    // Find the nearest line number for reporting
    $needle = '<lastmod>' . $d . '</lastmod>';
    $pos = strpos($content, $needle, $indexOffset);
    if ($pos !== false) {
        // compute 1-based line number
        $prefix = substr($content, 0, $pos);
        $lineNo = substr_count($prefix, "\n") + 1;
        $indexOffset = $pos + strlen($needle);
    } else {
        $lineNo = -1;
    }

    if (! $ok) {
        $invalid[] = ['date' => $d, 'line' => $lineNo];
    }
}

if (count($invalid) === 0) {
    echo "All dates valid (Y-m-d)\n";
    exit(0);
}

echo "Invalid <lastmod> entries:\n";
foreach ($invalid as $item) {
    echo "Line {$item['line']}: {$item['date']}\n";
}

exit(1);
