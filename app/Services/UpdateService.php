<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use ZipArchive;
use Exception;

class UpdateService
{
    protected $updateServer;
    protected $currentVersion;
    protected $tempPath;

    public function __construct()
    {
        $this->updateServer = config('services.update_server.url', 'https://updates.yourdomain.com');
        $this->currentVersion = config('app.version', '1.0.0');
        $this->tempPath = storage_path('app/temp/updates');
    }

    public function checkForUpdates()
    {
        try {
            $response = Http::timeout(10)->get($this->updateServer . '/api/check-update', [
                'current_version' => $this->currentVersion,
                'app_key' => config('app.key')
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['update_available']) && $data['update_available']) {
                    Cache::put('update_available', $data, now()->addDay());
                    return $data;
                }
            }
            Cache::forget('update_available');
            return null;
        } catch (Exception $e) {
            Log::error('Update check failed: ' . $e->getMessage());
            return null;
        }
    }

    public function downloadAndInstall($version, $downloadUrl)
    {
        try {
            if (!File::exists($this->tempPath)) {
                File::makeDirectory($this->tempPath, 0755, true);
            }

            $zipPath = $this->tempPath . '/update_' . $version . '.zip';
            $this->downloadFile($downloadUrl, $zipPath);

            if (!File::exists($zipPath)) {
                throw new Exception('Download failed - file not found');
            }

            $extractPath = $this->tempPath . '/extracted_' . $version;
            $this->extractZip($zipPath, $extractPath);
            $backupPath = $this->backupApplication();
            $this->applyUpdate($extractPath);
            $this->runMigrations();
            $this->clearCache();
            $this->cleanup($zipPath, $extractPath);
            $this->updateVersion($version);

            Log::info('Update installed successfully: ' . $version);
            return ['success' => true, 'version' => $version, 'message' => 'Update installed successfully!'];
        } catch (Exception $e) {
            Log::error('Update installation failed: ' . $e->getMessage());
            if (isset($backupPath) && File::exists($backupPath)) {
                $this->rollback($backupPath);
            }
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }

    protected function downloadFile($url, $path)
    {
        $client = new \GuzzleHttp\Client([
            'verify' => false,
            'timeout' => 300,
            'headers' => ['User-Agent' => 'SchoolManagementSystem/' . $this->currentVersion]
        ]);
        $client->get($url, ['sink' => $path]);
    }

    protected function extractZip($zipPath, $extractPath)
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === true) {
            $zip->extractTo($extractPath);
            $zip->close();
            return true;
        }
        throw new Exception('Failed to extract ZIP file');
    }

    protected function backupApplication()
    {
        $backupPath = storage_path('app/backups/app_backup_' . date('Y-m-d_H-i-s'));
        if (!File::exists(dirname($backupPath))) {
            File::makeDirectory(dirname($backupPath), 0755, true);
        }
        $folders = ['app', 'config', 'database', 'resources', 'routes', 'public'];
        foreach ($folders as $folder) {
            $source = base_path($folder);
            if (File::exists($source)) {
                File::copyDirectory($source, $backupPath . '/' . $folder);
            }
        }
        return $backupPath;
    }

    protected function applyUpdate($extractPath)
    {
        $folders = ['app', 'config', 'database', 'resources', 'routes', 'public'];
        foreach ($folders as $folder) {
            $source = $extractPath . '/' . $folder;
            $destination = base_path($folder);
            if (File::exists($source)) {
                File::copyDirectory($source, $destination);
            }
        }
    }

    protected function runMigrations()
    {
        Artisan::call('migrate', ['--force' => true]);
    }

    protected function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('optimize:clear');
    }

    protected function cleanup($zipPath, $extractPath)
    {
        if (File::exists($zipPath)) File::delete($zipPath);
        if (File::exists($extractPath)) File::deleteDirectory($extractPath);
    }

    protected function updateVersion($version)
    {
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $content = File::get($envPath);
            $content = preg_replace('/APP_VERSION=.*/', 'APP_VERSION=' . $version, $content);
            File::put($envPath, $content);
        }
    }

    protected function rollback($backupPath)
    {
        if (File::exists($backupPath)) {
            $folders = ['app', 'config', 'database', 'resources', 'routes', 'public'];
            foreach ($folders as $folder) {
                $source = $backupPath . '/' . $folder;
                $destination = base_path($folder);
                if (File::exists($source)) {
                    File::copyDirectory($source, $destination);
                }
            }
            File::deleteDirectory($backupPath);
            Log::info('Rollback completed successfully');
        }
    }

    public function getUpdateStatus()
    {
        $updateInfo = Cache::get('update_available');
        return [
            'current_version' => $this->currentVersion,
            'update_available' => !is_null($updateInfo),
            'update_info' => $updateInfo,
            'last_check' => Cache::get('last_update_check', now()->toDateTimeString())
        ];
    }

    public function cleanOldBackups($keep = 5)
    {
        $backupPath = storage_path('app/backups');
        if (File::exists($backupPath)) {
            $backups = File::directories($backupPath);
            usort($backups, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            foreach (array_slice($backups, $keep) as $backup) {
                File::deleteDirectory($backup);
            }
        }
    }
}