<?php

require __DIR__ . '/../vendor/autoload.php';

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;

$credentials = __DIR__ . '/../storage/app/firebase/service-account.json';
$propertyId = getenv('GOOGLE_ANALYTICS_PROPERTY_ID') ?: trim(@file_get_contents(__DIR__ . '/../.env') ? preg_match('/GOOGLE_ANALYTICS_PROPERTY_ID=(\d+)/', file_get_contents(__DIR__ . '/../.env'), $m) ? $m[1] : null : null);

echo "Credentials file: " . ($credentials) . PHP_EOL;
echo "Credentials exists: " . (file_exists($credentials) ? 'YES' : 'NO') . PHP_EOL;
echo "Property ID: " . ($propertyId ?: 'NULL') . PHP_EOL;

if (!file_exists($credentials) || !$propertyId) {
    echo "Missing credentials file or property id. Exiting.\n";
    exit(1);
}

try {
    $client = new BetaAnalyticsDataClient([
        'credentials' => $credentials,
    ]);

    $request = new RunReportRequest([
        'property' => "properties/{$propertyId}",
        'date_ranges' => [new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today'])],
        'metrics' => [new Metric(['name' => 'activeUsers']), new Metric(['name' => 'screenPageViews'])],
        'limit' => 5,
    ]);

    $response = $client->runReport($request);

    echo "--- Report rows ---\n";
    foreach ($response->getRows() as $row) {
        $metricValues = $row->getMetricValues();
        $dims = $row->getDimensionValues();
        $vals = [];
        foreach ($metricValues as $mv) { $vals[] = $mv->getValue(); }
        echo json_encode($vals) . PHP_EOL;
    }

    echo "Done.\n";

} catch (\Throwable $e) {
    echo "ERROR TYPE: " . get_class($e) . PHP_EOL;
    echo "Message: " . $e->getMessage() . PHP_EOL;
    if (method_exists($e, 'getTrace')) {
        echo "Trace first line: " . substr(json_encode($e->getTrace()[0] ?? []), 0, 400) . PHP_EOL;
    }
}
