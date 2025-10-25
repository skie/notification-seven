<?php
declare(strict_types=1);

namespace Cake\SevenNotification\Provider;

use Cake\Core\Configure;
use Cake\Notification\Extension\ChannelProviderInterface;
use Cake\Notification\Registry\ChannelRegistry;
use Cake\SevenNotification\Channel\SevenChannel;

/**
 * Seven Channel Provider
 *
 * Registers the Seven.io SMS channel with the notification system.
 */
class SevenChannelProvider implements ChannelProviderInterface
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return ['seven'];
    }

    /**
     * @inheritDoc
     */
    public function register(ChannelRegistry $registry): void
    {
        $config = array_merge(
            $this->getDefaultConfig(),
            (array)Configure::read('Notification.channels.seven', []),
        );

        $registry->load('seven', [
            'className' => SevenChannel::class,
        ] + $config);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultConfig(): array
    {
        $apiKey = getenv('SEVEN_API_KEY');

        return [
            'api_key' => $apiKey !== false ? $apiKey : null,
        ];
    }
}
