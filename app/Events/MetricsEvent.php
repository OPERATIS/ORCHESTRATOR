<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MetricsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $metrics;
    protected $userId;

    /**
     * @param array $metrics
     * @param int $userId
     */
    public function __construct(array $metrics, int $userId)
    {
        $this->metrics = $metrics;
        $this->userId = $userId;

    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('metrics.' . $this->userId);
    }
}
