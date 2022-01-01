<?php

namespace App\Services;

use App\Contracts\DiscoversIpAddress;
use App\Events\DynDNSUpdated;
use App\Events\UpdateDnyDNSIPError;
use App\Notifications\PublicIPChangedNotification;
use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\Zones;
use Illuminate\Support\Facades\Notification;

class CloudflareDynDNS
{
    private Adapter $adapter;

    public function __construct(public string $apiToken, public string $subdomain, public DiscoversIpAddress $ipAddress, public string $zoneId = "", public string $recordId = "")
    {
        $this->adapter = new Guzzle(new APIToken($this->apiToken));
    }

    public function handle(): void
    {
        if (empty($this->zoneId)) {
            $this->retrieveZoneId();
        }

        if (empty($this->recordId)) {
            $this->retrieveRecordId();
        }

        $recordIp = $this->getRecordIpAddress();
        $publicIP = $this->ipAddress->getIp();

        if ($recordIp != $publicIP) {
            $result = $this->getDnsApi()->updateRecordDetails($this->zoneId, $this->recordId, [
                'name' => $this->subdomain,
                'type' => 'A',
                'content' => $publicIP,
                'proxied' => false,
                'ttl' => 1
            ]);
            if ($result->success === true) {
                event($event = new DynDNSUpdated(newIp: $publicIP, oldIp: $recordIp));
                if (!empty(config('dyndns.notification_email'))) {
                    Notification::route('mail', 'tim@partysturmevents.de')->notify(new PublicIPChangedNotification($event));
                }
            } else {
                event($event = new UpdateDnyDNSIPError(result: $result));
            }
        }

    }

    private function retrieveZoneId(): void
    {
        $this->zoneId = (new Zones($this->adapter))->getZoneID($this->getMainDomain());
    }

    private function getMainDomain(): string
    {
        $myhost = strtolower(trim($this->subdomain));
        $count = substr_count($myhost, '.');
        if ($count === 2) {
            if (strlen(explode('.', $myhost)[1]) > 3) $myhost = explode('.', $myhost, 2)[1];
        } else if ($count > 2) {
            $myhost = $this->getMainDomain(explode('.', $myhost, 2)[1]);
        }
        return $myhost;
    }

    private function retrieveRecordId(): void
    {
        $this->recordId = $this->getDnsApi()->getRecordID($this->zoneId, "A", $this->subdomain);
    }

    private function getDnsApi(): DNS
    {
        return new DNS($this->adapter);
    }

    private function getRecordIpAddress(): string
    {
        return ($this->getDnsApi()->getRecordDetails($this->zoneId, $this->recordId))->content;
    }
}
