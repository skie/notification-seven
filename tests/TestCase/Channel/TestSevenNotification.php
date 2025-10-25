<?php
declare(strict_types=1);

namespace Cake\SevenNotification\Test\TestCase\Channel;

use Cake\Datasource\EntityInterface;
use Cake\Notification\AnonymousNotifiable;
use Cake\Notification\Notification;

/**
 * Test Seven Notification
 *
 * Helper notification for testing
 */
class TestSevenNotification extends Notification
{
    /**
     * @inheritDoc
     */
    public function via(EntityInterface|AnonymousNotifiable $notifiable): array
    {
        return ['seven'];
    }

    /**
     * Get Seven message
     *
     * @param \Cake\Datasource\EntityInterface|\Cake\Notification\AnonymousNotifiable $notifiable Notifiable entity
     * @return mixed
     */
    public function toSeven(EntityInterface|AnonymousNotifiable $notifiable): mixed
    {
        return null;
    }
}
