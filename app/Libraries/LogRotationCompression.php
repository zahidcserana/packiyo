<?php

namespace App\Libraries;
use Illuminate\Filesystem\Filesystem;
use Carbon\Carbon;

class LogRotationCompression
{
	public function __construct()
	{
		$this->today = Carbon::today();
		$this->localPath = '';
		$this->disk = '';
	}

	public function process()
	{
		$this->processLogs();
	}

	private function processLogs()
	{
        $this->logPath = storage_path('logs');
        $this->disk = new Filesystem();
		$allLogs = $this->disk->files($this->logPath);

        $logs = $this->getFilteredLogs($allLogs);
        $logs = $this->mapBasename($logs);
        
        foreach ($logs as $log) {
        	$days = $this->diffInDays($log, $this->today);
        	
        	if ($days > 30){  		
	        	$compressedName = "compressed-logs/{$log}.tar.bz2";

	        	$this->compress($log, $compressedName);
        	}
        }
	}

    public function getContent($log)
    {
        $path = "{$this->logPath}/{$log}";
        return $this->disk->get($path);
    }

    private function compress($log, $compressedName)
    {
        $command = "cd {$this->logPath}; tar cjf {$compressedName} {$log}";
        exec($command, $output, $exit);

        if ($exit) {
            throw new Exception("Something went wrong when compressing {$log}");
        }

        $this->disk->delete("{$this->logPath}/{$log}");
    }

	private function getFilteredLogs($logs, $keepIndex = false)
    {
        $logs = array_filter($logs, function ($item) {
            return (bool) preg_match('/^.*?\d{4}-\d{2}-\d{2}\.log$/', $item);
        });

        if (!$keepIndex) {
            $logs = array_values($logs);
        }

        return $logs;
    }

    private function mapBasename($logs)
    {
        $logs = array_map(function ($item) {
            return basename($item);
        }, $logs);

        return $logs;
    }

    private function getDate($log)
    {
        if (preg_match('/(?<date>\d{4}-\d{2}-\d{2})\.log/', $log, $matches)) {
            return new Carbon($matches['date']);
        }

        throw new InvalidArgumentException('The provided log is not in the daily format');
    }

    private function diffInDays($log, Carbon $date)
    {
        $logDate = $this->getDate($log);
        $days = $logDate->diffInDays($date);

        return $days;
    }
}