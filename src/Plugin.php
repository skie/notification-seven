<?php
declare(strict_types=1);

namespace Cake\SevenNotification;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\SevenNotification\Provider\SevenChannelProvider;

/**
 * Seven Notification Plugin
 *
 * Registers the Seven.io SMS notification channel with the CakePHP Notification plugin.
 */
class Plugin extends BasePlugin
{
    /**
     * Bootstrap hook
     *
     * Registers the Seven.io SMS channel with the notification registry.
     *
     * @param \Cake\Core\PluginApplicationInterface<\Cake\Core\PluginInterface> $app Application instance
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        EventManager::instance()->on(
            'Notification.Registry.discover',
            function ($event): void {
                $registry = $event->getSubject();
                (new SevenChannelProvider())->register($registry);
            },
        );
    }
}
