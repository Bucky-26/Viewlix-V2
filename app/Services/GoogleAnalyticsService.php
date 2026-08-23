<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\RunRealtimeReportRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleAnalyticsService
{
    protected ?BetaAnalyticsDataClient $client = null;
    protected ?string $propertyId = null;

    public function __construct()
    {
        $credentials = FirebaseService::getCredentials();
        
        // Set property ID from config
        $this->propertyId = config('services.google_analytics.property_id');
        
        if ($credentials && $this->propertyId) {
            try {
                $this->client = new BetaAnalyticsDataClient([
                    'credentials' => $credentials,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to initialize Google Analytics client: ' . $e->getMessage());
            }
        }
    }

    /**
     * Check if the service is configured
     */
    public function isConfigured(): bool
    {
        return $this->client !== null && $this->propertyId !== null;
    }

    /**
     * Get the property ID
     */
    public function getPropertyId(): ?string
    {
        return $this->propertyId;
    }

    /**
     * Get the property resource name
     */
    protected function getPropertyName(): string
    {
        return "properties/{$this->propertyId}";
    }

    /**
     * Get active users for a specific period
     */
    public function getActiveUsers(string $period = '1day'): array
    {
        $cacheKey = "analytics_active_users_{$period}";
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($period) {
            if (!$this->isConfigured()) {
                return $this->getDefaultStats();
            }

            try {
                $dateRange = $this->getDateRange($period);
                
                $request = new RunReportRequest([
                    'property' => $this->getPropertyName(),
                    'date_ranges' => [$dateRange],
                    'metrics' => [
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'newUsers']),
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'screenPageViews']),
                        new Metric(['name' => 'averageSessionDuration']),
                        new Metric(['name' => 'bounceRate']),
                    ],
                ]);
                
                $response = $this->client->runReport($request);

                $row = $response->getRows()[0] ?? null;
                
                if ($row) {
                    $metrics = $row->getMetricValues();
                    return [
                        'active_users' => (int) ($metrics[0]?->getValue() ?? 0),
                        'new_users' => (int) ($metrics[1]?->getValue() ?? 0),
                        'sessions' => (int) ($metrics[2]?->getValue() ?? 0),
                        'page_views' => (int) ($metrics[3]?->getValue() ?? 0),
                        'avg_session_duration' => round((float) ($metrics[4]?->getValue() ?? 0), 2),
                        'bounce_rate' => round((float) ($metrics[5]?->getValue() ?? 0) * 100, 1),
                        'period' => $period,
                        'configured' => true,
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch active users: ' . $e->getMessage());
            }

            return $this->getDefaultStats();
        });
    }

    /**
     * Get visitor trend data for charts
     */
    public function getVisitorTrend(string $period = '30days'): array
    {
        $cacheKey = "analytics_visitor_trend_{$period}";
        
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($period) {
            if (!$this->isConfigured()) {
                return $this->getDefaultTrend($period);
            }

            try {
                $dateRange = $this->getDateRange($period);
                
                $request = new RunReportRequest([
                    'property' => $this->getPropertyName(),
                    'date_ranges' => [$dateRange],
                    'dimensions' => [
                        new Dimension(['name' => 'date']),
                    ],
                    'metrics' => [
                        new Metric(['name' => 'activeUsers']),
                        new Metric(['name' => 'sessions']),
                        new Metric(['name' => 'screenPageViews']),
                    ],
                    'order_bys' => [
                        new OrderBy([
                            'dimension' => new DimensionOrderBy([
                                'dimension_name' => 'date',
                            ]),
                        ]),
                    ],
                ]);
                
                $response = $this->client->runReport($request);

                $data = [
                    'labels' => [],
                    'active_users' => [],
                    'sessions' => [],
                    'page_views' => [],
                ];

                foreach ($response->getRows() as $row) {
                    $dimensions = $row->getDimensionValues();
                    $metrics = $row->getMetricValues();
                    
                    // Format date from YYYYMMDD to MM/DD
                    $dateStr = $dimensions[0]?->getValue() ?? '';
                    if (strlen($dateStr) === 8) {
                        $month = substr($dateStr, 4, 2);
                        $day = substr($dateStr, 6, 2);
                        $data['labels'][] = "{$month}/{$day}";
                    } else {
                        $data['labels'][] = $dateStr;
                    }
                    
                    $data['active_users'][] = (int) ($metrics[0]?->getValue() ?? 0);
                    $data['sessions'][] = (int) ($metrics[1]?->getValue() ?? 0);
                    $data['page_views'][] = (int) ($metrics[2]?->getValue() ?? 0);
                }

                $data['configured'] = true;
                return $data;
            } catch (\Exception $e) {
                Log::error('Failed to fetch visitor trend: ' . $e->getMessage());
            }

            return $this->getDefaultTrend($period);
        });
    }

    /**
     * Get top pages
     */
    public function getTopPages(int $limit = 10, string $period = '7days'): array
    {
        $cacheKey = "analytics_top_pages_{$period}_{$limit}";
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($limit, $period) {
            if (!$this->isConfigured()) {
                return ['pages' => [], 'configured' => false];
            }

            try {
                $dateRange = $this->getDateRange($period);
                
                $request = new RunReportRequest([
                    'property' => $this->getPropertyName(),
                    'date_ranges' => [$dateRange],
                    'dimensions' => [
                        new Dimension(['name' => 'pagePath']),
                    ],
                    'metrics' => [
                        new Metric(['name' => 'screenPageViews']),
                    ],
                    'order_bys' => [
                        new OrderBy([
                            'metric' => new MetricOrderBy([
                                'metric_name' => 'screenPageViews',
                            ]),
                            'desc' => true,
                        ]),
                    ],
                    'limit' => $limit,
                ]);
                
                $response = $this->client->runReport($request);

                $pages = [];
                foreach ($response->getRows() as $row) {
                    $dimensions = $row->getDimensionValues();
                    $metrics = $row->getMetricValues();
                    
                    $pages[] = [
                        'path' => $dimensions[0]?->getValue() ?? '',
                        'views' => (int) ($metrics[0]?->getValue() ?? 0),
                    ];
                }

                return ['pages' => $pages, 'configured' => true];
            } catch (\Exception $e) {
                Log::error('Failed to fetch top pages: ' . $e->getMessage());
            }

            return ['pages' => [], 'configured' => false];
        });
    }

    /**
     * Get traffic sources
     */
    public function getTrafficSources(string $period = '7days'): array
    {
        $cacheKey = "analytics_traffic_sources_{$period}";
        
        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($period) {
            if (!$this->isConfigured()) {
                return ['sources' => [], 'configured' => false];
            }

            try {
                $dateRange = $this->getDateRange($period);
                
                $request = new RunReportRequest([
                    'property' => $this->getPropertyName(),
                    'date_ranges' => [$dateRange],
                    'dimensions' => [
                        new Dimension(['name' => 'sessionDefaultChannelGroup']),
                    ],
                    'metrics' => [
                        new Metric(['name' => 'sessions']),
                    ],
                    'order_bys' => [
                        new OrderBy([
                            'metric' => new MetricOrderBy([
                                'metric_name' => 'sessions',
                            ]),
                            'desc' => true,
                        ]),
                    ],
                    'limit' => 6,
                ]);
                
                $response = $this->client->runReport($request);

                $sources = [];
                foreach ($response->getRows() as $row) {
                    $dimensions = $row->getDimensionValues();
                    $metrics = $row->getMetricValues();
                    
                    $sources[] = [
                        'channel' => $dimensions[0]?->getValue() ?? '',
                        'sessions' => (int) ($metrics[0]?->getValue() ?? 0),
                    ];
                }

                return ['sources' => $sources, 'configured' => true];
            } catch (\Exception $e) {
                Log::error('Failed to fetch traffic sources: ' . $e->getMessage());
            }

            return ['sources' => [], 'configured' => false];
        });
    }

    /**
     * Get realtime users count
     */
    public function getRealTimeUsers(): array
    {
        $cacheKey = 'analytics_realtime_users';
        
        return Cache::remember($cacheKey, now()->addSeconds(60), function () {
            if (!$this->isConfigured()) {
                return ['active_users' => 0, 'configured' => false];
            }

            try {
                $request = new RunRealtimeReportRequest([
                    'property' => $this->getPropertyName(),
                    'metrics' => [
                        new Metric(['name' => 'activeUsers']),
                    ],
                ]);
                
                $response = $this->client->runRealtimeReport($request);

                $activeUsers = 0;
                foreach ($response->getRows() as $row) {
                    $metrics = $row->getMetricValues();
                    $activeUsers += (int) ($metrics[0]?->getValue() ?? 0);
                }

                return ['active_users' => $activeUsers, 'configured' => true];
            } catch (\Exception $e) {
                Log::error('Failed to fetch realtime users: ' . $e->getMessage());
            }

            return ['active_users' => 0, 'configured' => false];
        });
    }

    /**
     * Get all dashboard stats
     */
    public function getDashboardStats(): array
    {
        return [
            'realtime' => $this->getRealTimeUsers(),
            'today' => $this->getActiveUsers('1day'),
            'yesterday' => $this->getActiveUsers('yesterday'),
            'week' => $this->getActiveUsers('7days'),
            'month' => $this->getActiveUsers('30days'),
            'trend' => $this->getVisitorTrend('30days'),
            'top_pages' => $this->getTopPages(5, '7days'),
            'traffic_sources' => $this->getTrafficSources('7days'),
            'configured' => $this->isConfigured(),
            'property_id' => $this->propertyId,
        ];
    }

    /**
     * Clear analytics cache
     */
    public function clearCache(): void
    {
        $keys = [
            'analytics_active_users_1day',
            'analytics_active_users_yesterday',
            'analytics_active_users_7days',
            'analytics_active_users_30days',
            'analytics_visitor_trend_30days',
            'analytics_visitor_trend_7days',
            'analytics_top_pages_7days_5',
            'analytics_traffic_sources_7days',
            'analytics_realtime_users',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get date range based on period
     */
    protected function getDateRange(string $period): DateRange
    {
        $endDate = 'today';
        
        switch ($period) {
            case '1day':
            case 'today':
                $startDate = 'today';
                break;
            case 'yesterday':
                $startDate = 'yesterday';
                $endDate = 'yesterday';
                break;
            case '7days':
                $startDate = '7daysAgo';
                break;
            case '30days':
            default:
                $startDate = '30daysAgo';
                break;
        }

        return new DateRange([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    /**
     * Get default stats when API is not available
     */
    protected function getDefaultStats(): array
    {
        return [
            'active_users' => 0,
            'new_users' => 0,
            'sessions' => 0,
            'page_views' => 0,
            'avg_session_duration' => 0,
            'bounce_rate' => 0,
            'configured' => false,
        ];
    }

    /**
     * Get default trend data
     */
    protected function getDefaultTrend(string $period): array
    {
        $days = $period === '7days' ? 7 : 30;
        $labels = [];
        $zeros = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('m/d');
            $zeros[] = 0;
        }

        return [
            'labels' => $labels,
            'active_users' => $zeros,
            'sessions' => $zeros,
            'page_views' => $zeros,
            'configured' => false,
        ];
    }
}
