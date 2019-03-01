# gocardless-laravel
[![Build Status](https://travis-ci.com/Nestednet/gocardless-laravel.svg?branch=master)](https://travis-ci.com/Nestednet/gocardless-laravel)
[![Latest Stable Version](https://poser.pugx.org/nestednet/gocardless-laravel/v/stable)](https://packagist.org/packages/nestednet/gocardless-laravel)
[![Latest Unstable Version](https://poser.pugx.org/nestednet/gocardless-laravel/v/unstable)](https://packagist.org/packages/nestednet/gocardless-laravel)
[![License](https://poser.pugx.org/nestednet/gocardless-laravel/license)](https://packagist.org/packages/nestednet/gocardless-laravel)

GoCardless Pro PHP Client package integration for Laravel.

**1.** If you are using Laravel >5.5 the package will be autodiscobered, for older versions add the service provider at your config/app.php file.

**2.** Publish the configuration file
```bash
$ php artisan vendor:publish --provider="Nestednet\Gocardless\Laravel\GocardlessServiceProvider"
```

**3.** Review the configuration file
```
config/gocardless.php
```
and add your Gocardless API token and environment to the `.env` file.
