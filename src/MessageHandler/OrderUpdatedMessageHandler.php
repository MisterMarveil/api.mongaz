<?php
namespace App\MessageHandler;

use App\Entity\Order;
use App\Message\OrderUpdatedMessage;
use App\Message\NotificationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler]
class OrderUpdatedMessageHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private MessageBusInterface $bus
    ) {}

    public function __invoke(OrderUpdatedMessage $message): void
    {
        $order = $this->em->getRepository(Order::class)->find($message->getOrderId());

        if (!$order) {
            return; // entity deleted or not found
        }

        // Serialize Order with read context
        $payload = $this->serializer->serialize(
            $order,
            'json',
            ['groups' => ['order:read']]
        );

        $topics = [
            'system:all',
            'system:role:ROLE_DRIVER',
            sprintf('system:entity:order:%d', $order->getId()),
        ];

        if ($order->getDriver()?->getUuid()) {
            $topics[] = sprintf('system:user:%s', $order->getDriver()->getUuid());
        }

        if ($order->getSubscription()?->getId()) {
            $topics[] = sprintf('system:entity:subscription:%d', $order->getSubscription()->getId());
        }

        // Instead of publishing directly → dispatch NotificationMessage
        $this->bus->dispatch(new NotificationMessage($topics, $payload,"order.updated"));
    }
}
