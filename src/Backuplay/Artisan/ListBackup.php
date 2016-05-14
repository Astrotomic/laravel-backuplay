<?php

namespace Gummibeer\Backuplay\Artisan;

use Gummibeer\Backuplay\Contracts\ConfigContract;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class CreateBackup.
 */
class ListBackup extends Command
{
    /**
     * @var string
     */
    protected $name = 'backup:list';
    /**
     * @var string
     */
    protected $description = 'List all existing backups';

    /**
     * @var \Gummibeer\Backuplay\Contracts\ConfigContract
     */
    protected $config;

    /**
     * CreateBackup constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->config = app(ConfigContract::class);
    }

    /**
     * @return void
     */
    public function fire()
    {
        $this->info('start backuplay');

        $disk = $this->config->get('disk');
        if ($disk !== false) {
            $this->comment('list archives on disk: '.$disk);

            $headers = [
                'storage',
                'cycle',
                'filename',
                'size',
                'modified',
            ];
            $backups = [];

            $cycles = $this->getCycles($disk);
            if (!is_null($cycle = $this->option('cycle')) && in_array($cycle, $cycles)) {
                $cycles = [$cycle];
            }
            if (count($cycles) > 0) {
                foreach ($cycles as $cycle) {
                    $archives = $this->getArchivesByCycle($disk, $cycle);
                    if (count($archives) > 0) {
                        foreach ($archives as $archive) {
                            $backups[] = $this->getArchiveInfo($disk, $cycle, $archive);
                        }
                    }
                }
            }

            if (count($backups) > 0) {
                $this->table($headers, $backups);
            } else {
                $this->warn('no backups found');
            }
        } else {
            $this->warn('storage is disabled');
        }

        $this->info('end backuplay');
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['cycle', null, InputOption::VALUE_OPTIONAL, 'The cycle where you want to search for backups.'],
        ];
    }

    /**
     * @param string $disk
     * @return array
     */
    protected function getCycles($disk)
    {
        return Storage::disk($disk)->directories($this->config->get('storage_path'));
    }

    /**
     * @param string $disk
     * @param string $cycle
     * @return array
     */
    protected function getArchivesByCycle($disk, $cycle)
    {
        return Storage::disk($disk)->files($this->config->get('storage_path').DIRECTORY_SEPARATOR.$cycle);
    }

    /**
     * @param string $disk
     * @param string $cycle
     * @param string $archive
     * @return array
     */
    protected function getArchiveInfo($disk, $cycle, $archive)
    {
        $size = Storage::disk($disk)->size($archive);
        $modified = Storage::disk($disk)->lastModified($archive);

        return [
            'storage' => $disk,
            'cycle' => $cycle,
            'archive' => basename($archive),
            'size' => $this->formatBytes($size),
            'modified' => date('Y-m-d H:i:s T', $modified),
        ];
    }

    /**
     * @param int|string $bytes
     * @param int $precision
     * @return string
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max(intval($bytes), 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
