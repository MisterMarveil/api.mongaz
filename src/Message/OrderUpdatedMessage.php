<?php
namespace App\Message;

class OrderUpdatedMessage
{
    public function __construct(
        private int $orderId
    ) {}

    public function getOrderId(): int
    {
        return $this->orderId;
    }
}
