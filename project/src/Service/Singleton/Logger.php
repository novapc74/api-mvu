<?php

namespace App\Service\Singleton;


use DateTimeImmutable;

class Logger extends BaseSingleton
{
    private const INFO_TYPE = 'INFO';
    private const ERROR_TYPE = 'ERROR';

    private array $logs = [];

    public function info(string $message): void
    {
        $this->formatLog(self::INFO_TYPE, $message);
    }

    public function error(string $message): void
    {
        $this->formatLog(self::ERROR_TYPE, $message);
    }

    private function formatLog(string $type, string $message): void
    {
        $message = sprintf('%s: %s. Message: %s', $type, self::dateTime(), $message);
        $this->writeLog($message);
    }

    private function writeLog(string $message): void
    {
        $this->logs[] = $message;
    }

    private function dateTime(): string
    {
        return (new DateTimeImmutable('now'))->format('d.m.Y H:i:s');
    }

    public function toArray(): array
    {
        return $this->logs;
    }

    public function toString(): string
    {
        return implode(PHP_EOL, $this->logs);
    }
}
