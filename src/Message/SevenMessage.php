<?php
declare(strict_types=1);

namespace Cake\SevenNotification\Message;

/**
 * Seven Message
 *
 * Fluent message builder for Seven.io SMS notifications.
 */
class SevenMessage
{
    /**
     * Message payload
     *
     * @var array<string, mixed>
     */
    protected array $payload = [];

    /**
     * Constructor
     *
     * @param string $text Message text
     */
    public function __construct(string $text = '')
    {
        if ($text !== '') {
            $this->payload['text'] = $text;
        }
    }

    /**
     * Create a new message instance
     *
     * @param string $text Message text
     * @return static
     */
    public static function create(string $text = ''): static
    {
        return new static($text); // @phpstan-ignore-line
    }

    /**
     * Set message content
     *
     * @param string $text Message text
     * @return static
     */
    public function content(string $text): static
    {
        $this->payload['text'] = $text;

        return $this;
    }

    /**
     * Set recipient phone number
     *
     * @param string $to Phone number (E.164 format recommended)
     * @return static
     */
    public function to(string $to): static
    {
        $this->payload['to'] = $to;

        return $this;
    }

    /**
     * Set sender name/number
     *
     * @param string $from Sender name or number
     * @return static
     */
    public function from(string $from): static
    {
        $this->payload['from'] = $from;

        return $this;
    }

    /**
     * Send as flash SMS (displayed immediately)
     *
     * @return static
     */
    public function flash(): static
    {
        $this->payload['flash'] = true;

        return $this;
    }

    /**
     * Set message delay (timestamp)
     *
     * @param int $timestamp Unix timestamp for delayed sending
     * @return static
     */
    public function delay(int $timestamp): static
    {
        $this->payload['delay'] = $timestamp;

        return $this;
    }

    /**
     * Set foreign ID for tracking
     *
     * @param string $foreignId Your tracking ID
     * @return static
     */
    public function foreignId(string $foreignId): static
    {
        $this->payload['foreign_id'] = $foreignId;

        return $this;
    }

    /**
     * Set label for analytics
     *
     * @param string $label Label for grouping messages
     * @return static
     */
    public function label(string $label): static
    {
        $this->payload['label'] = $label;

        return $this;
    }

    /**
     * Enable performance tracking
     *
     * @return static
     */
    public function performanceTracking(): static
    {
        $this->payload['performance_tracking'] = true;

        return $this;
    }

    /**
     * Check if recipient number is set
     *
     * @return bool
     */
    public function hasTo(): bool
    {
        return isset($this->payload['to']);
    }

    /**
     * Get payload value for a given key
     *
     * @param string $key Payload key
     * @return mixed
     */
    public function getPayloadValue(string $key): mixed
    {
        return $this->payload[$key] ?? null;
    }

    /**
     * Convert message to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->payload;
    }
}
