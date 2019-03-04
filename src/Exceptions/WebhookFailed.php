<?php
/**
 * Created by PhpStorm.
 * User: eudaldarranztresserra
 * Date: 2019-03-01
 * Time: 13:07
 */

namespace Nestednet\Gocardless\Exceptions;

use Exception;
use Nestednet\Gocardless\GocardlessWebhookCall;

class WebhookFailed extends Exception
{
    public static function missingSignature()
    {
        return new static('The request did not contain a Signature header - `Webhook-Signature`.');
    }

    public static function invalidSignature($signature)
    {
        return new static("The signature: {$signature} found in the header is invalid.");
    }

    public static function noSecretKeyProvided()
    {
        return new static('The webhook secret key is not set.');
    }

    public static function jobClassDoesNotExist(string $jobClass, GocardlessWebhookCall $webhookCall)
    {
        return new static("Could not process webhook id `{$webhookCall->id}` of type `{$webhookCall->type} because the configured jobclass `$jobClass` does not exist.");
    }

    public static function missingResource(GocardlessWebhookCall $webhookCall)
    {
        return new static("Webhook call id `{$webhookCall->id}` did not contain a resource type. Valid Gocardless webhook calls should always contain a resource type.");
    }

    public static function missingAction(GocardlessWebhookCall $webhookCall)
    {
        return new static("Webhook call id `{$webhookCall->id}` did not contain an action. Valid Gocardless webhook calls should always contain an action.");
    }

    public function render($request)
    {
        return response(['error' => $this->getMessage()], 400);
    }

}