<?php

namespace App\Console\Commands;

use App\Services\CloudflareDynDNS;
use Illuminate\Console\Command;

class RefreshDynDns extends Command
{
    protected $signature = 'dyndns:refresh';

    protected $description = 'Refresh dyndns entry by public IP';

    public function handle(CloudflareDynDNS $cloudflareDynDNS)
    {
        $cloudflareDynDNS->handle();
    }
}
