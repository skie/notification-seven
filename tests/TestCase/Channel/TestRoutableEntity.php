<?php
declare(strict_types=1);

namespace Cake\SevenNotification\Test\TestCase\Channel;

use Cake\Datasource\EntityInterface;
use Cake\Datasource\EntityTrait;
use Cake\Notification\Notification;

/**
 * Test Routable Entity
 *
 * Helper entity for testing routing
 */
class TestRoutableEntity implements EntityInterface
{
    use EntityTrait;

    /**
     * Route notification for Seven channel
     *
     * @param \Cake\Notification\Notification $notification Notification instance
     * @return string|null
     */
    public function routeNotificationForSeven(Notification $notification): ?string
    {
        return null;
    }
}
