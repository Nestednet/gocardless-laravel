# gocardless-laravel
[![Build Status](https://travis-ci.com/Nestednet/gocardless-laravel.svg?branch=master)](https://travis-ci.com/Nestednet/gocardless-laravel)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Nestednet/gocardless-laravel/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Nestednet/gocardless-laravel/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/nestednet/gocardless-laravel/v/stable)](https://packagist.org/packages/nestednet/gocardless-laravel)
[![Latest Unstable Version](https://poser.pugx.org/nestednet/gocardless-laravel/v/unstable)](https://packagist.org/packages/nestednet/gocardless-laravel)
[![License](https://poser.pugx.org/nestednet/gocardless-laravel/license)](https://packagist.org/packages/nestednet/gocardless-laravel)

##### GoCardless Pro PHP Client package integration for Laravel.

This package tries to provide an easy, scalable and maintainable way to integrate Gocardless into your laravel project.

It provides a Facade that wraps the Gocardless PHP client and also an easy way to handle the webhooks that Gocardless sends. This is done following the steps that [Spatie](https://spatie.be/opensource/php) uses for their [Stripe Laravel Webhooks package](https://github.com/spatie/laravel-stripe-webhooks).

##### Installation

Get the package with composer:

```bash
composer require nestednet/gocardless-laravel
```

**1.** If you are using Laravel >5.5 the package will be autodiscobered, for older versions add the service provider at your config/app.php file.

**2.** Publish the configuration file
```bash
$ php artisan vendor:publish --provider="Nestednet\Gocardless\GocardlessServiceProvider"
```
This will publish both the configuration file and the migration file.

**3.** Review the configuration file
```
config/gocardless.php
```
and add your Gocardless API token and environment to the `.env` file.

**4.** After publishing the migration you can run the migration and create the `gocardless_webhooks_table`

**5.** The package provides a Macro route (`gocardlessWebhooks`). You can create a route at your routes file of your app. This route will be the endpoint where Gocardless will send the webhooks, you should register this webhook endpoint at your Gocardless dashboard.

```php
Route::gocardlessWebhooks('gocardless-webhook-endpoint');
```

This will register a `POST` route to a the controller provided by this package. You should add the route to the `except` array of the `VerifyCsrfToken` middleware.

```php
protected $except = [
    'gocardless-webhook-endpoint',
];
```

##### Usage

Once the package is properly installed you can use the `Gocardless` facade to access the methods of the Gocardless PHP client. The documentation of this methods can be found here: [Gocardless PHP cleint documentation](https://github.com/gocardless/gocardless-pro-php)

If you use Gocardless at your project you provably will use webhooks to handle the asynchronous payment states. This package provides an easy way to handle the webhooks.

Gocardless will send you webhooks with events. This events will contain the updates of your Gocardless resources.

This package will verify the signature of the requests and if it's valid. Unless something goes terribly wrong, and even if one of the events inside the webhook fails the controller will reposnd with a `200` to Gocardless. This prevents Gocardless from spamming retries to the endpoint.

If an event fails to be processed the exception will be saved to the database into the `gocardless_webhook_calls` table, you can find the failed events there.

This package provides two ways to handle the webhook requests:

* Using jobs
* Using events

##### Using jobs
You can find a jobs array inside the `config\gocardless.php`. 

You can register any job that you want to the gocardless events. An event from Gocardless references one resource `resource_type` and one `action`. In order to register a job to an action you should add it with the key `{resource_type}_{action}`. 
```php
'jobs' => [
 // '{resource_type}_{action} => path/to/job::class,
    'payments_created' => App\Jobs\PaymentConfirmed::class,
]
```

In order to avoid timeouts it's highly recommended to use queued jobs.

##### Using events

Every time an event is processed by the package it will trigger an event with this structure: 

`gocardless-webhooks::{resource_type}_{action}`

The payload of the event will be the `GocardlessWebhookCall` (or an extended model) instance created with the request. 

You can register listeners to this events in the `EventServiceProvider`:

```php
/**
 * The event listener mappings for the application.
 *
 * @var array
 */
protected $listen = [
    'gocardless-webhooks::payments_created' => [
        App\Listeners\ListenerOfPaymentsCreated::class,
    ],
];
```

