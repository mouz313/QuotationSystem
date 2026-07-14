<?php
namespace App\Events;

use App\Models\Quotation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuotationStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Quotation $quotation) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('company.' . $this->quotation->user->company_id)];
    }

    public function broadcastAs(): string
    {
        return 'quotation.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->quotation->id,
            'quote_number' => $this->quotation->quote_number,
            'status' => $this->quotation->status,
            'grand_total' => $this->quotation->grand_total,
        ];
    }
}
