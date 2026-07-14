<?php
namespace App\Events;

use App\Models\Company;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompanyStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Company $company) {}

    public function broadcastOn(): array
    {
        return ['admin'];
    }

    public function broadcastAs(): string
    {
        return 'company.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->company->id,
            'name' => $this->company->name,
            'status' => $this->company->status,
        ];
    }
}
