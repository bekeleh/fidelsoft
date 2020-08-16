<?php

namespace App\Listeners;

use App\Events\SubdomainWasRemovedEvent;
use App\Events\SubdomainWasUpdatedEvent;
use App\Ninja\DNS\Cloudflare;

/**
 * Class DNSListener.
 */
class DNSListener
{
    public function addDNSRecord(SubdomainWasUpdatedEvent $event)
    {
        if (env("CLOUDFLARE_DNS_ENABLED"))
            Cloudflare::addDNSRecord($event->account);
    }

    public function removeDNSRecord(SubdomainWasRemovedEvent $event)
    {
        if (env("CLOUDFLARE_DNS_ENABLED"))
            Cloudflare::removeDNSRecord($event->account);
    }

}
