<?php
/**
 * Created by PhpStorm.
 * User: eudaldarranztresserra
 * Date: 2019-03-01
 * Time: 13:07
 */

namespace Nestednet\Gocardless\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Nestednet\Gocardless\Middlewares\VerifySignature;

class GocardlessWebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware(VerifySignature::class);
    }

    public function __invoke(Request $request)
    {
        $payload = $request->input();

        $modelClass = config('gocardless.webhooks.model');

        $gocardlessWebhookCall = $modelClass::create([
            'resource_type' => $payload['resource_type'] ?? '',
            'action' => $payload['action'] ?? '',
            'payload' => $payload,
        ]);

        try {
            $gocardlessWebhookCall->process();
        } catch (Exception $exception) {
            $gocardlessWebhookCall->saveException($exception);
            throw $exception;
        }

        return response()->json(['message' => 'ok']);
    }
}