<?php

namespace App\Services;

use App\Libraries\Utils;
use App\Models\Gateway;
use App\Models\GatewayType;
use Form;
use HTML;

class VendorTemplateService
{

    public function processVariables($template, array $data)
    {
        $invitation = $data['invitation'];

        $account = !empty($data['account']) ? $data['account'] : $invitation->account;

        $vendor = !empty($data['vendor']) ? $data['vendor'] : $invitation->bill->vendor;

        $amount = !empty($data['amount']) ? $data['amount'] : $invitation->bill->getRequestedAmount();

        // check if it's a proposal
        if ($invitation->proposal) {
            $bill = $invitation->proposal->bill;
            $entityType = ENTITY_PROPOSAL;
        } else {
            $bill = $invitation->bill;
            $entityType = $bill->getEntityType();
        }

        $contact = $invitation->contact;
        $passwordHTML = isset($data['password']) ? '<p>' . trans('texts.password') . ': ' . $data['password'] . '<p>' : false;
        $documentsHTML = '';

        if ($account->hasFeature(FEATURE_DOCUMENTS) && $bill->hasDocuments()) {
            $documentsHTML .= trans('texts.email_documents_header') . '<ul>';
            foreach ($bill->allDocuments() as $document) {
                $documentsHTML .= '<li><a href="' . HTML::entities($document->getClientUrl($invitation)) . '">' . HTML::entities($document->name) . '</a></li>';
            }
            $documentsHTML .= '</ul>';
        }

        $variables = [
            '$footer' => $account->getEmailFooter(),
            '$emailSignature' => $account->getEmailFooter(),
            '$vendor' => $vendor->getDisplayName(),
            '$idNumber' => $vendor->id_number,
            '$vatNumber' => $vendor->vat_number,
            '$account' => $account->getDisplayName(),
            '$dueDate' => $account->formatDate($bill->getOriginal('partial_due_date') ?: $bill->getOriginal('due_date')),
            '$billDate' => $account->formatDate($bill->getOriginal('bill_date')),
            '$contact' => $contact->getDisplayName(),
            '$firstName' => $contact->first_name,
            '$amount' => $account->formatMoney($amount, $vendor),
            '$total' => $bill->present()->amount,
            '$balance' => $bill->present()->balance,
            '$bill' => $bill->bill_number,
            '$quote' => $bill->bill_number,
            '$number' => $bill->bill_number,
            '$partial' => $bill->present()->partial,
            '$poNumber' => $bill->po_number,
            '$terms' => $bill->terms,
            '$notes' => $bill->public_notes,
            '$link' => $invitation->getLink(),
            '$password' => $passwordHTML,
            '$viewLink' => $invitation->getLink() . '$password',
            '$viewButton' => Form::emailViewButton($invitation->getLink(), $entityType) . '$password',
            '$paymentLink' => $invitation->getLink('payment') . '$password',
            '$paymentButton' => Form::emailPaymentButton($invitation->getLink('payment')) . '$password',
            '$approveLink' => $invitation->getLink('approve') . '$password',
            '$approveButton' => Form::emailPaymentButton($invitation->getLink('approve'), 'approve') . '$password',
            '$customClient1' => $vendor->custom_value1,
            '$customClient2' => $vendor->custom_value2,
            '$customContact1' => $contact->custom_value1,
            '$customContact2' => $contact->custom_value2,
            '$customInvoice1' => $bill->custom_text_value1,
            '$customInvoice2' => $bill->custom_text_value2,
            '$documents' => $documentsHTML,
            '$autoBill' => empty($data['autobill']) ? '' : $data['autobill'],
            '$portalLink' => $invitation->contact->link,
            '$portalButton' => Form::emailViewButton($invitation->contact->link, 'portal'),
        ];

        // Add variables for available payment types
        foreach (Gateway::$gatewayTypes as $type) {
            if ($type == GATEWAY_TYPE_TOKEN) {
                continue;
            }
            $camelType = Utils::toCamelCase(GatewayType::getAliasFromId($type));
            $snakeCase = Utils::toSnakeCase(GatewayType::getAliasFromId($type));
            $variables["\${$camelType}Link"] = $invitation->getLink('payment') . "/{$snakeCase}";
            $variables["\${$camelType}Button"] = Form::emailPaymentButton($invitation->getLink('payment') . "/{$snakeCase}");
        }

        $includesPasswordPlaceholder = strpos($template, '$password') !== false;

        $str = str_replace(array_keys($variables), array_values($variables), $template);

        if (!$includesPasswordPlaceholder && $passwordHTML) {
            $pos = strrpos($str, '$password');
            if ($pos !== false) {
                $str = substr_replace($str, $passwordHTML, $pos, 9/* length of "$password" */);
            }
        }
        $str = str_replace('$password', '', $str);
        $str = autolink($str, 100);

        return $str;
    }
}
