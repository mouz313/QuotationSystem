<?php
namespace App\Events;

use App\Models\Company;
use App\Models\Package;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PackageAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Company $company, public Package $package) {}

    public function broadcastOn(): array
    {
        return ['company.' . $this->company->id];
    }

    public function broadcastAs(): string
    {
        return 'package.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'company_id' => $this->company->id,
            'company_name' => $this->company->name,
            'package_name' => $this->package->name,
            'package_price' => $this->package->price,
        ];
    }
}
