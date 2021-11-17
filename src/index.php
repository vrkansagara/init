<?php
declare(strict_types=1);

namespace Vrkansagara\Init;

use Carbon\Carbon;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(
        __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
    );
    if (is_string($path) && __FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
require __DIR__ . '/../vendor/autoload.php';
try {
    $now = Carbon::now()->setTimezone('Asia/Kolkata');
    // $process = new Process(['ls', '-lhtra', '/tmp']);

    $notificationBin = 'notify-send';
    if (Shell::isBinaryAvailable($notificationBin)) {
        $process = new Process(
            [
            'notify-send',
            '-i',
            'face-wink',
            sprintf('Hey !, %s', $now),
            ]
        );

        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }
} catch (Exception $exception) {
    throw $exception;
}
