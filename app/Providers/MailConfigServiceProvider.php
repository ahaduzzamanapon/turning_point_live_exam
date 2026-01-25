<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Prevent issues during migration or if table doesn't exist
        if (Schema::hasTable('settings')) {
            $mailDriver = Setting::get('mail_mailer', config('mail.default'));

            // Only override if we have custom settings in DB, otherwise use env/config
            if (Setting::get('mail_host')) {
                $config = [
                    'driver' => $mailDriver,
                    'host' => Setting::get('mail_host'),
                    'port' => Setting::get('mail_port'),
                    'from' => [
                        'address' => Setting::get('mail_from_address'),
                        'name' => Setting::get('mail_from_name'),
                    ],
                    'encryption' => Setting::get('mail_encryption'),
                    'username' => Setting::get('mail_username'),
                    'password' => Setting::get('mail_password'),
                ];

                Config::set('mail.mailers.smtp', array_merge(config('mail.mailers.smtp'), $config));
                Config::set('mail.from.address', Setting::get('mail_from_address'));
                Config::set('mail.from.name', Setting::get('mail_from_name'));
            }
        }
    }
}
