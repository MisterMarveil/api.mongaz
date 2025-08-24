<?php
namespace App\EventSubscriber;

use App\Entity\Order;
use App\Message\OrderUpdatedMessage;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Messenger\MessageBusInterface;

class OrderUpdatedEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $bus
    ) {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::postUpdate,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Order) {
            // Dispatch message after an Order is updated
            $this->bus->dispatch(new OrderUpdatedMessage($entity->getId()));    
        }        
    }
}
