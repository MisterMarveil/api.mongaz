<?php
namespace App\MessageHandler;

use App\Message\NotificationMessage;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class NotificationMessageHandler
{
    public function __construct(private HubInterface $hub) {}

    public function __invoke(NotificationMessage $message): void
    {
        foreach ($message->topics as $topic) {
            $this->hub->publish(new Update(
                $topic,
                json_encode([
                    'topic' => $topic,
                    'event' => $message->event,
                    'data'  => $message->data,
                    'ts'    => (new \DateTimeImmutable())->format(DATE_ATOM),
                ], JSON_THROW_ON_ERROR)
            ));
        }
    }
}
