<?php
declare(strict_types=1);

namespace Cake\SevenNotification\Channel;

use Cake\Datasource\EntityInterface;
use Cake\Notification\AnonymousNotifiable;
use Cake\Notification\Channel\ChannelInterface;
use Cake\Notification\Exception\CouldNotSendNotification;
use Cake\Notification\Notification;
use Cake\SevenNotification\Message\SevenMessage;
use Exception;
use Seven\Api\Client;
use Seven\Api\Resource\Sms\SmsParams;
use Seven\Api\Resource\Sms\SmsResource;

/**
 * Seven Channel
 *
 * Sends SMS notifications via Seven.io API.
 */
class SevenChannel implements ChannelInterface
{
    /**
     * Seven.io API client
     *
     * @var \Seven\Api\Client
     */
    protected Client $client;

    /**
     * SMS resource for sending messages
     *
     * @var \Seven\Api\Resource\Sms\SmsResource
     */
    protected SmsResource $smsResource;

    /**
     * Constructor
     *
     * @param array<string, mixed> $config Channel configuration
     */
    public function __construct(protected array $config = [])
    {
        if (empty($config['api_key'])) {
            throw CouldNotSendNotification::missingCredentials('seven', 'api_key');
        }

        $this->client = new Client($config['api_key']);
        $this->smsResource = new SmsResource($this->client);
    }

    /**
     * @inheritDoc
     */
    public function send(EntityInterface|AnonymousNotifiable $notifiable, Notification $notification): mixed
    {
        if (!method_exists($notification, 'toSeven')) {
            return null;
        }

        $message = $notification->toSeven($notifiable);
        if ($message === null) {
            return null;
        }

        if (is_string($message)) {
            $message = SevenMessage::create($message);
        }

        if (!$message->hasTo()) {
            $to = $this->getRecipient($notifiable, $notification);
            if ($to === null) {
                throw CouldNotSendNotification::missingRoutingInformation('seven');
            }
            $message->to($to);
        }

        try {
            $params = $this->buildParams($message);
            $response = $this->smsResource->dispatch($params);

            return $response;
        } catch (Exception $e) {
            throw CouldNotSendNotification::serviceRespondedWithError(
                'seven',
                $e->getMessage(),
                "Failed to send Seven.io SMS: {$e->getMessage()}",
            );
        }
    }

    /**
     * Get recipient phone number
     *
     * @param \Cake\Datasource\EntityInterface|\Cake\Notification\AnonymousNotifiable $notifiable Notifiable entity
     * @param \Cake\Notification\Notification $notification Notification instance
     * @return string|null
     */
    protected function getRecipient(
        EntityInterface|AnonymousNotifiable $notifiable,
        Notification $notification,
    ): ?string {
        if ($notifiable instanceof AnonymousNotifiable) {
            return $notifiable->routeNotificationFor('seven', $notification);
        }

        if (method_exists($notifiable, 'routeNotificationForSeven')) {
            return $notifiable->routeNotificationForSeven($notification);
        }

        if (isset($notifiable->phone)) {
            return $notifiable->phone;
        }

        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }

        return null;
    }

    /**
     * Build SMS parameters from message
     *
     * @param \Cake\SevenNotification\Message\SevenMessage $message Message instance
     * @return \Seven\Api\Resource\Sms\SmsParams
     */
    protected function buildParams(SevenMessage $message): SmsParams
    {
        $payload = $message->toArray();
        $text = $payload['text'] ?? '';
        $to = $payload['to'] ?? '';

        $params = new SmsParams($text, $to);

        if (isset($payload['from'])) {
            $params->setFrom($payload['from']);
        }

        if (isset($payload['flash']) && $payload['flash']) {
            $params->setFlash(true);
        }

        if (isset($payload['delay'])) {
            $params->setDelay($payload['delay']);
        }

        if (isset($payload['foreign_id'])) {
            $params->setForeignId($payload['foreign_id']);
        }

        if (isset($payload['label'])) {
            $params->setLabel($payload['label']);
        }

        return $params;
    }
}
