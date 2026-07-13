<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            [
                'group' => 'general',
                'key' => 'app_name',
                'label' => 'Application Name',
                'description' => 'Displayed in browser title and emails.',
                'value' => config('app.name', 'Laraplate'),
                'type' => 'string',
                'is_public' => true,
            ],
            [
                'group' => 'general',
                'key' => 'app_tagline',
                'label' => 'Tagline',
                'description' => 'Short description shown on login page.',
                'value' => '',
                'type' => 'string',
                'is_public' => true,
            ],
            [
                'group' => 'general',
                'key' => 'app_description',
                'label' => 'Description',
                'description' => 'Longer description used in meta tags.',
                'value' => '',
                'type' => 'text',
                'is_public' => true,
            ],
            [
                'group' => 'general',
                'key' => 'maintenance_mode',
                'label' => 'Maintenance Mode',
                'description' => 'Show maintenance page to non-admin users.',
                'value' => '0',
                'type' => 'boolean',
                'is_public' => false,
            ],
            // Appearance
            [
                'group' => 'appearance',
                'key' => 'theme_color',
                'label' => 'Theme Color',
                'description' => 'Primary accent color for the UI.',
                'value' => 'default',
                'type' => 'select',
                'options' => [
                    'default' => 'Default',
                    'zinc' => 'Zinc',
                    'slate' => 'Slate',
                    'blue' => 'Blue',
                    'green' => 'Green',
                    'rose' => 'Rose',
                    'orange' => 'Orange',
                    'violet' => 'Violet',
                ],
                'is_public' => true,
            ],
            [
                'group' => 'appearance',
                'key' => 'toast_position',
                'label' => 'Toast Position',
                'description' => 'Where notifications appear on screen.',
                'value' => 'bottom-right',
                'type' => 'select',
                'options' => [
                    'bottom-right' => 'Bottom Right',
                    'bottom-left' => 'Bottom Left',
                    'bottom-center' => 'Bottom Center',
                    'top-right' => 'Top Right',
                    'top-left' => 'Top Left',
                    'top-center' => 'Top Center',
                ],
                'is_public' => true,
            ],
            [
                'group' => 'appearance',
                'key' => 'dark_mode',
                'label' => 'Dark Mode (Default)',
                'description' => 'App-wide default. Users can override with the toggle.',
                'value' => '0',
                'type' => 'boolean',
                'is_public' => true,
            ],
            [
                'group' => 'appearance',
                'key' => 'sidebar_collapsed',
                'label' => 'Sidebar Collapsed by Default',
                'description' => 'Start with sidebar hidden on desktop.',
                'value' => '0',
                'type' => 'boolean',
                'is_public' => true,
            ],
            // Auth
            [
                'group' => 'auth',
                'key' => 'register_enabled',
                'label' => 'Registration Enabled',
                'description' => 'Allow new users to self-register.',
                'value' => '1',
                'type' => 'boolean',
                'is_public' => true,
            ],
            // Security
            [
                'group' => 'security',
                'key' => 'session_lifetime',
                'label' => 'Session Lifetime (minutes)',
                'description' => 'Idle time before session expires.',
                'value' => '120',
                'type' => 'integer',
                'is_public' => false,
            ],
            [
                'group' => 'security',
                'key' => 'login_attempts_max',
                'label' => 'Max Login Attempts',
                'description' => 'Lockout threshold before rate limiting.',
                'value' => '5',
                'type' => 'integer',
                'is_public' => false,
            ],
            [
                'group' => 'security',
                'key' => 'login_lockout_minutes',
                'label' => 'Lockout Duration (minutes)',
                'description' => 'How long an account is locked after max attempts.',
                'value' => '15',
                'type' => 'integer',
                'is_public' => false,
            ],
            // Notification
            [
                'group' => 'notification',
                'key' => 'badge_poll_seconds',
                'label' => 'Notification Poll Interval (seconds)',
                'description' => 'How often the bell badge refreshes. 0 disables polling.',
                'value' => '30',
                'type' => 'integer',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $data) {
            Setting::firstOrCreate(['key' => $data['key']], $data);
        }
    }
}
