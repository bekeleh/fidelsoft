<?php

namespace App\Listeners;

use App\Libraries\Utils;
use App\Models\EntityModel;
use App\Ninja\Serializers\ArraySerializer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class EntityListener
{
    /**
     * @param $eventId
     * @param $entity
     * @param $transformer
     * @param string $include
     * @return bool|void
     */
    protected function checkSubscriptions($eventId, $entity, $transformer, $include = '')
    {
        if (!EntityModel::$notifySubscriptions) {
            return false;
        }

        $subscriptions = $entity->account->getSubscriptions($eventId);

        if (!$subscriptions->count()) {
            return false;
        }

        // generate JSON data
        $manager = new Manager();
        $manager->setSerializer(new ArraySerializer());
        $manager->parseIncludes($include);

        $resource = new Item($entity, $transformer, $entity->getEntityType());
        $jsonData = $manager->createData($resource)->toArray();

        // For legacy Zapier support
        if (isset($jsonData['client_id'])) {
            $jsonData['client_name'] = $entity->client->getDisplayName();
        }

        foreach ($subscriptions as $subscription) {
            switch ($subscription->format) {
                case SUBSCRIPTION_FORMAT_JSON:
                    $data = $jsonData;
                    break;
                case SUBSCRIPTION_FORMAT_UBL:
                    $data = $ublData;
                    break;
            }

            self::notifySubscription($subscription, $data);
        }
    }

    protected static function notifySubscription($subscription, $data)
    {
        $curl = curl_init();
        $jsonEncodedData = json_encode($data);
        $url = $subscription->target_url;

        if (!Utils::isNinja() && $secret = env('SUBSCRIPTION_SECRET')) {
            $url .= '?secret=' . $secret;
        }

        $opts = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $jsonEncodedData,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Content-Length: ' . strlen($jsonEncodedData)],
        ];

        curl_setopt_array($curl, $opts);

        $result = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($status == 410) {
            $subscription->delete();
        }
    }
}