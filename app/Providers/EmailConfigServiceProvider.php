<?php

namespace App\Providers;

use App\Models\EmailSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class EmailConfigServiceProvider extends ServiceProvider
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
        $this->loadEmailConfiguration();
    }

    /**
     * Load email configuration from database if available
     */
    private function loadEmailConfiguration(): void
    {
        try {
            // Check if database tables exist (in case migrations haven't run yet)
            if (!app()->runningInConsole() || app()->runningUnitTests()) {
                $emailSettings = EmailSetting::getActiveSettings();
                
                if ($emailSettings) {
                    // Override mail configuration with database settings
                    Config::set([
                        'mail.default' => $emailSettings->mail_mailer,
                        'mail.mailers.smtp.host' => $emailSettings->mail_host,
                        'mail.mailers.smtp.port' => $emailSettings->mail_port,
                        'mail.mailers.smtp.encryption' => $emailSettings->mail_encryption,
                        'mail.mailers.smtp.username' => $emailSettings->mail_username,
                        'mail.mailers.smtp.password' => $emailSettings->mail_password,
                        'mail.from.address' => $emailSettings->mail_from_address,
                        'mail.from.name' => $emailSettings->mail_from_name,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if database is not available or table doesn't exist
            // This prevents errors during initial setup or migrations
        }
    }
}
