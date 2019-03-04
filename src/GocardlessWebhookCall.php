<?php
/**
 * Created by PhpStorm.
 * User: eudaldarranztresserra
 * Date: 2019-03-01
 * Time: 13:08
 */

namespace Nestednet\Gocardless;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Nestednet\Gocardless\Exceptions\WebhookFailed;

class GocardlessWebhookCall extends Model
{
    public $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'exception' => 'array',
    ];

    public function process()
    {
        $this->clearException();

        if ($this->resource_type === '') {
            throw WebhookFailed::missingResource($this);
        }

        if ($this->action === '') {
            throw WebhookFailed::missingAction($this);
        }

        event("gocardless-webhooks::{$this->resource_type}_{$this->action}", $this);

        $jobClass = $this->determineJobClass($this->resource_type, $this->action);

        if ($jobClass === "") {
            return;
        }

        if (! class_exists($jobClass)) {
            throw WebhookFailed::jobClassDoesNotExist($jobClass, $this);
        }

        dispatch(new $jobClass($this));
    }

    public function saveException(Exception $exception)
    {
        $this->exception = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ];

        $this->save();

        return $this;
    }

    protected function determineJobClass(string $resourceType, string $action) : string
    {
        $formattedResourceType = str_replace('.', '_', $resourceType);
        $formattedAction = str_replace('.', '_', $action);

        $jobClassKey = "{$formattedResourceType}_{$formattedAction}";

        return config("gocardless.webhooks.jobs.{$jobClassKey}", "");
    }

    protected function clearException()
    {
        $this->exception = null;

        $this->save();

        return $this;
    }

}