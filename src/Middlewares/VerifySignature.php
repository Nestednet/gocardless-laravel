<?php
/**
 * Created by PhpStorm.
 * User: eudaldarranztresserra
 * Date: 2019-03-01
 * Time: 13:06
 */

namespace Nestednet\Gocardless\Middlewares;

use Closure;
use Exception;
use GoCardlessPro\Webhook;
use Nestednet\Gocardless\Exceptions\WebhookFailed;

class VerifySignature
{
    public function handle($request, Closure $next)
    {
        $signature = $request->header('Webhook-Signature');

        if (!$signature) {
            throw WebhookFailed::missingSignature();
        }

        if (!$this->isValid($signature, $request->getContent(), $request->route('configKey'))) {
            throw WebhookFailed::invalidSignature($signature);
        }

        return $next($request);
    }

    protected function isValid(string $signature, string $payload, string $configKey = null) : bool
    {
        $secret = ($configKey) ?
            config('gocardless.webhooks.webhook_endpoint_secret_' . $configKey) : config('gocardless.webhooks.webhook_endpoint_secret');

        if (empty($secret)) {
            throw WebhookFailed::noSecretKeyProvided();
        }

        try {
            Webhook::parse($payload, $signature, $secret);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

}