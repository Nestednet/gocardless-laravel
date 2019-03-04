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

        foreach ($payload['events'] as $event) {
            $gocardlessWebhookCall = $modelClass::create([
                'resource_type' => $event['resource_type'] ?? '',
                'action' => $event['action'] ?? '',
                'payload' => $event,
            ]);

            try {
                $gocardlessWebhookCall->process();
            } catch (Exception $exception) {
                $gocardlessWebhookCall->saveException($exception);
                //Improve the way we handle the exceptions here, add the option to notify the exceptions.
            }
        }

        return response()->json(['message' => 'ok']);
    }
}