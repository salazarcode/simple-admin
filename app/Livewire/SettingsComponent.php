<?php

namespace App\Livewire;

use App\Models\EmailSetting;
use App\Models\ColorSettings;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Mail\Message;

class SettingsComponent extends Component
{
    public $activeTab = 'email';
    
    // Color Settings
    public $colorThemes;
    public $activeTheme;
    public $customTheme = [
        'name' => '',
        'sidebar_color' => '#151419',
        'header_color' => '#F56E0F',
        'search_area_color' => '#1B1B1E',
        'item_color' => '#262626',
        'button_area_color' => '#FBFBFB',
        'accent_color' => '#F56E0F',
        'text_primary_color' => '#FFFFFF',
        'text_secondary_color' => '#D1D5DB',
    ];
    
    // Email Settings
    public $mail_mailer = 'smtp';
    public $mail_host = '';
    public $mail_port = '587';
    public $mail_username = '';
    public $mail_password = '';
    public $mail_encryption = 'tls';
    public $mail_from_address = '';
    public $mail_from_name = '';
    
    // Modal states
    public $showTestModal = false;
    public $test_email = '';
    public $testing = false;
    public $test_result = '';
    
    // Gmail presets
    public $gmail_presets = [
        'host' => 'smtp.gmail.com',
        'port' => '587',
        'encryption' => 'tls'
    ];

    public function mount()
    {
        $this->loadEmailSettings();
        $this->loadColorSettings();
    }

    public function loadColorSettings()
    {
        $this->colorThemes = ColorSettings::all()->toArray();
        $activeThemeModel = ColorSettings::where('is_active', true)->first();
        
        // If no active theme, activate the first one
        if (!$activeThemeModel && ColorSettings::count() > 0) {
            $activeThemeModel = ColorSettings::first();
            $activeThemeModel->setAsActive();
        }
        
        $this->activeTheme = $activeThemeModel ? $activeThemeModel->toArray() : null;
    }

    public function loadEmailSettings()
    {
        $settings = EmailSetting::getActiveSettings();
        if ($settings) {
            $this->mail_mailer = $settings->mail_mailer;
            $this->mail_host = $settings->mail_host;
            $this->mail_port = $settings->mail_port;
            $this->mail_username = $settings->mail_username;
            $this->mail_password = $settings->mail_password;
            $this->mail_encryption = $settings->mail_encryption;
            $this->mail_from_address = $settings->mail_from_address;
            $this->mail_from_name = $settings->mail_from_name;
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function useGmailPresets()
    {
        $this->mail_host = $this->gmail_presets['host'];
        $this->mail_port = $this->gmail_presets['port'];
        $this->mail_encryption = $this->gmail_presets['encryption'];
    }

    public function saveEmailSettings()
    {
        $this->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required|email',
            'mail_password' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ], [
            'mail_host.required' => 'El servidor SMTP es obligatorio.',
            'mail_port.required' => 'El puerto es obligatorio.',
            'mail_port.numeric' => 'El puerto debe ser un número.',
            'mail_username.required' => 'El usuario es obligatorio.',
            'mail_username.email' => 'El usuario debe ser un email válido.',
            'mail_password.required' => 'La contraseña es obligatoria.',
            'mail_from_address.required' => 'El email de origen es obligatorio.',
            'mail_from_address.email' => 'El email de origen debe ser válido.',
            'mail_from_name.required' => 'El nombre de origen es obligatorio.',
        ]);

        // Deactivate other settings
        EmailSetting::where('is_active', true)->update(['is_active' => false]);

        // Create or update settings
        EmailSetting::create([
            'mail_mailer' => $this->mail_mailer,
            'mail_host' => $this->mail_host,
            'mail_port' => $this->mail_port,
            'mail_username' => $this->mail_username,
            'mail_password' => $this->mail_password,
            'mail_encryption' => $this->mail_encryption,
            'mail_from_address' => $this->mail_from_address,
            'mail_from_name' => $this->mail_from_name,
            'is_active' => true,
        ]);

        // Reload email configuration to apply changes immediately
        $this->reloadEmailConfiguration();

        session()->flash('success', 'Configuración de email guardada y aplicada exitosamente.');
    }

    public function reloadEmailConfiguration()
    {
        $emailSettings = EmailSetting::getActiveSettings();
        
        if ($emailSettings) {
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

    public function useEnvConfiguration()
    {
        // Deactivate database settings
        EmailSetting::where('is_active', true)->update(['is_active' => false]);
        
        // Clear form fields
        $this->resetEmailForm();
        
        session()->flash('success', 'Ahora se usa la configuración del archivo .env');
    }

    public function resetEmailForm()
    {
        $this->mail_mailer = 'smtp';
        $this->mail_host = '';
        $this->mail_port = '587';
        $this->mail_username = '';
        $this->mail_password = '';
        $this->mail_encryption = 'tls';
        $this->mail_from_address = '';
        $this->mail_from_name = '';
    }

    public function openTestModal()
    {
        $this->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'required|email',
            'mail_password' => 'required|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ], [
            'mail_host.required' => 'Debe completar todos los campos antes de probar.',
            'mail_port.required' => 'Debe completar todos los campos antes de probar.',
            'mail_username.required' => 'Debe completar todos los campos antes de probar.',
            'mail_password.required' => 'Debe completar todos los campos antes de probar.',
            'mail_from_address.required' => 'Debe completar todos los campos antes de probar.',
            'mail_from_name.required' => 'Debe completar todos los campos antes de probar.',
        ]);

        $this->test_email = '';
        $this->test_result = '';
        $this->showTestModal = true;
    }

    public function testEmailConnection()
    {
        $this->validate([
            'test_email' => 'required|email',
        ], [
            'test_email.required' => 'El email de destino es obligatorio.',
            'test_email.email' => 'Debe ser un email válido.',
        ]);

        $this->testing = true;
        $this->test_result = '';

        try {
            // Configure mail settings temporarily
            Config::set('mail.mailers.smtp.host', $this->mail_host);
            Config::set('mail.mailers.smtp.port', $this->mail_port);
            Config::set('mail.mailers.smtp.encryption', $this->mail_encryption);
            Config::set('mail.mailers.smtp.username', $this->mail_username);
            Config::set('mail.mailers.smtp.password', $this->mail_password);
            Config::set('mail.from.address', $this->mail_from_address);
            Config::set('mail.from.name', $this->mail_from_name);

            // Send test email
            Mail::raw('Este es un email de prueba desde ' . config('app.name'), function (Message $message) {
                $message->to($this->test_email)
                        ->subject('Email de Prueba - ' . config('app.name'));
            });

            $this->test_result = 'success';
            
            // Update the settings with test result
            $settings = EmailSetting::getActiveSettings();
            if ($settings) {
                $settings->update([
                    'tested_at' => now(),
                    'test_result' => 'success'
                ]);
            }

            session()->flash('success', 'Email de prueba enviado exitosamente.');
            
        } catch (\Exception $e) {
            $this->test_result = 'error';
            
            // Update the settings with test result
            $settings = EmailSetting::getActiveSettings();
            if ($settings) {
                $settings->update([
                    'tested_at' => now(),
                    'test_result' => 'error: ' . $e->getMessage()
                ]);
            }

            session()->flash('error', 'Error al enviar email: ' . $e->getMessage());
        }

        $this->testing = false;
    }

    public function closeTestModal()
    {
        $this->showTestModal = false;
        $this->test_email = '';
        $this->test_result = '';
    }

    public function getCanTestProperty()
    {
        return !empty($this->mail_host) && 
               !empty($this->mail_port) && 
               !empty($this->mail_username) && 
               !empty($this->mail_password) && 
               !empty($this->mail_from_address) && 
               !empty($this->mail_from_name);
    }

    // Color Settings Methods
    public function setActiveTheme($themeId)
    {
        $theme = ColorSettings::find($themeId);
        if ($theme) {
            $theme->setAsActive();
            $this->loadColorSettings();
            session()->flash('success', 'Tema "' . $theme->name . '" activado exitosamente.');
            
            // Force page refresh to apply new colors
            $this->dispatch('refresh-page');
        }
    }

    public function saveCustomTheme()
    {
        $this->validate([
            'customTheme.name' => 'required|string|max:255',
            'customTheme.sidebar_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'customTheme.header_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'customTheme.search_area_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'customTheme.item_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'customTheme.button_area_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'customTheme.accent_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'customTheme.text_primary_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'customTheme.text_secondary_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
        ], [
            'customTheme.name.required' => 'El nombre del tema es obligatorio.',
            'customTheme.sidebar_color.required' => 'El color del sidebar es obligatorio.',
            'customTheme.sidebar_color.regex' => 'El color del sidebar debe ser un código hexadecimal válido (#RRGGBB).',
            'customTheme.header_color.required' => 'El color del header es obligatorio.',
            'customTheme.header_color.regex' => 'El color del header debe ser un código hexadecimal válido (#RRGGBB).',
            // ... más validaciones para cada color
        ]);

        // Create new custom theme
        $newTheme = ColorSettings::create([
            'name' => $this->customTheme['name'],
            'sidebar_color' => $this->customTheme['sidebar_color'],
            'header_color' => $this->customTheme['header_color'],
            'search_area_color' => $this->customTheme['search_area_color'],
            'item_color' => $this->customTheme['item_color'],
            'button_area_color' => $this->customTheme['button_area_color'],
            'accent_color' => $this->customTheme['accent_color'],
            'text_primary_color' => $this->customTheme['text_primary_color'],
            'text_secondary_color' => $this->customTheme['text_secondary_color'],
            'is_active' => false,
        ]);

        // Activate the new theme
        $newTheme->setAsActive();
        
        // Reset form and reload
        $this->resetCustomTheme();
        $this->loadColorSettings();
        
        session()->flash('success', 'Tema personalizado "' . $newTheme->name . '" creado y activado exitosamente.');
    }

    public function resetCustomTheme()
    {
        $this->customTheme = [
            'name' => '',
            'sidebar_color' => '#151419',
            'header_color' => '#F56E0F',
            'search_area_color' => '#1B1B1E',
            'item_color' => '#262626',
            'button_area_color' => '#FBFBFB',
            'accent_color' => '#F56E0F',
            'text_primary_color' => '#FFFFFF',
            'text_secondary_color' => '#D1D5DB',
        ];
    }

    public function render()
    {
        return view('livewire.settings-component')
            ->layout('layouts.app');
    }
}
