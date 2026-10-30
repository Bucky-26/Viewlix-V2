<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Services\FirebaseService;
use Illuminate\Console\Command;

class SyncMobileAppConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:sync-app-config 
                            {--read : Read and display current config from Firebase RTDB}
                            {--player-url= : Override player URL}
                            {--force-update= : Set force update boolean (true/false)}
                            {--maintenance-mode= : Set maintenance mode boolean (true/false)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync mobile app configuration to Firebase Realtime Database (RTDB)';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseService $firebase): int
    {
        if ($this->option('read')) {
            $this->info('Fetching current mobile app config from Firebase RTDB...');
            $current = $firebase->getMobileAppConfigFromDatabase();
            if ($current) {
                $rows = [];
                foreach ($current as $key => $val) {
                    $rows[] = [$key, is_bool($val) ? ($val ? 'true' : 'false') : (is_array($val) ? json_encode($val) : (string)$val)];
                }
                $this->table(['Key', 'Firebase RTDB Value'], $rows);
            } else {
                $this->warn('No app_config found in Firebase RTDB.');
            }
            return Command::SUCCESS;
        }
        $this->info('Preparing mobile app configuration for Firebase RTDB...');

        if ($playerUrl = $this->option('player-url')) {
            AppSetting::setVal('player_url', $playerUrl);
            $this->line("Set player_url to: <comment>{$playerUrl}</comment>");
        }

        if ($forceUpdate = $this->option('force-update')) {
            $isForce = filter_var($forceUpdate, FILTER_VALIDATE_BOOLEAN);
            AppSetting::setVal('force_update', $isForce);
            $this->line("Set force_update to: <comment>" . ($isForce ? 'true' : 'false') . "</comment>");
        }

        if ($mMode = $this->option('maintenance-mode')) {
            $isMMode = filter_var($mMode, FILTER_VALIDATE_BOOLEAN);
            AppSetting::setVal('maintenance_mode', $isMMode);
            $this->line("Set maintenance_mode to: <comment>" . ($isMMode ? 'true' : 'false') . "</comment>");
        }

        $configData = $firebase->getMobileAppConfigData();

        $this->table(['Key', 'Value'], [
            ['app_name', $configData['app_name']],
            ['version', $configData['version']],
            ['download_url', $configData['download_url']],
            ['latest_apk', $configData['latest_apk']],
            ['player_url', $configData['player_url']],
            ['maintenance_mode', $configData['maintenance_mode'] ? 'true' : 'false'],
            ['force_update', $configData['force_update'] ? 'true' : 'false'],
            ['updated_at', date('Y-m-d H:i:s', $configData['updated_at'])],
        ]);

        $this->info('Saving to Firebase RTDB paths [app_config, mobile_app_config]...');
        $saved = $firebase->syncMobileAppConfig($configData);

        if ($saved) {
            $this->info('✅ Mobile app configuration successfully saved to Firebase RTDB!');
            return Command::SUCCESS;
        } else {
            $this->error('❌ Failed to save mobile app configuration to Firebase RTDB. Check Firebase credentials.');
            return Command::FAILURE;
        }
    }
}
