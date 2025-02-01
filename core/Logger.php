<?php
class Logger {
    public static function log($message, $type = 'INFO') {
        $logFile = __DIR__ . '/../logs/' . date('Y-m-d') . '.log';
        $logMessage = date('Y-m-d H:i:s') . " [$type] " . $message . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
} 