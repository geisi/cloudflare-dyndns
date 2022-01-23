<?php

return [
    /*
     * List of all domains which should be synchronized with the systems public IP address
     */
    'domains' => [
        [
            /*
             * Your full domain name e.g. dyndns.foo-bar.com
             */
            'domain_name' => env('DYNDNS_DOMAIN_NAME', ''),
            'dns_service' => [
                /*
                 * The dns service which you want to use to serve your Dns records. By default, this package runs with the Cloudflare Dns service.
                 * If you don't want to use Cloudflare you can create your own implementation.
                 * Your class has to implement the Geisi\DynDns\Contracts\HandlesDynDnsRecords interface.
                 */
                'adapter' => \Geisi\DynDns\Services\CloudflareDynDNS::class,

                /*
                 * The Cloudflare zone id this can be left empty. For performance reasons it is recommended to configure a zone id.
                 */
                'zone_id' => env('DYNDNS_ZONE_ID', ''),

                /*
                 * The Cloudflare dns record id this can be left empty. For performance reasons it is recommended to configure a dns record id.
                 */
                'record_id' => env('DYNDNS_RECORD_ID', ''),

                /*
                 * The Cloudflare DNS record details look Cloudflare API documentation for reference.
                 */
                'record_details' => [
                    'type' => 'A',
                    'proxied' => false,
                    'ttl' => 1
                ],
            ],
            /*
             * The public IP Address Resolver Service. By default, we are using the opendns.com resolver to query the current public IP address.
             * If you don't want to use opendns.com you can create your own implementation.
             * Your class has to implement the Geisi\DynDns\Contracts\DiscoversIpAddress interface.
             */
            'resolver_service' => Geisi\DynDns\Services\OpenDNSPublicIPResolver::class,
            /*
             * The email address which should be notified when the public IP address changes
             * leave empty if you want to disable mail notifications
             */
            'notification_email' => env('DYNDNS_NOTIFICATION_EMAIL', '')
        ]
    ],
    /*
    * The Cloudflare API token.
    */
    'cloudflare_api_token' => env('CLOUDFLARE_TOKEN', '')
];
