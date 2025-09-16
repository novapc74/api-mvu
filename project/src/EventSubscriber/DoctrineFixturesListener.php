<?php

namespace App\EventSubscriber;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DoctrineFixturesListener implements EventSubscriberInterface
{
    private Connection $connection;
    private KernelInterface $kernel;

    public function __construct(Connection $connection, KernelInterface $kernel)
    {
        $this->connection = $connection;
        $this->kernel = $kernel;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onCommand',
            ConsoleEvents::TERMINATE => 'onTerminate',
        ];
    }

    /**
     * @throws Exception
     */
    public function onCommand(ConsoleCommandEvent $event): void
    {
        if (!in_array($this->kernel->getEnvironment(), ['dev', 'test'])) {
            return;
        }

        if ($event->getCommand()->getName() === 'doctrine:fixtures:load') {
            $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        }
    }

    /**
     * @throws Exception
     */
    public function onTerminate(ConsoleTerminateEvent $event): void
    {
        if (!in_array($this->kernel->getEnvironment(), ['dev', 'test'])) {
            return;
        }

        if ($event->getCommand()->getName() === 'doctrine:fixtures:load') {
            $this->connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
        }
    }
}
