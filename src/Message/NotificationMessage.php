<?php
namespace App\Message;

final class NotificationMessage
{
    /**
     * @param string[] $topics  Mercure topics to publish to
     * @param array    $data    Arbitrary payload (serializable)
     * @param string|null $event A short event name like "order.assigned"
     */
    public function __construct(
        public array $topics,
        public array $data,
        public ?string $event = null
    ) {}
}
