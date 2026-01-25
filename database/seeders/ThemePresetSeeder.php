<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ThemePreset;

class ThemePresetSeeder extends Seeder
{
    public function run()
    {
        $presets = [
            [
                'name' => 'Light (Default)',
                'is_default' => true,
                'settings' => [
                    'primary_color' => '#10b981',
                    'danger_color' => '#ef4444',
                    'body_bg_color' => '#ecfdf5',
                    'body_text_color' => '#000000',
                    'sidebar_bg' => '#ffffff',
                    'sidebar_text_color' => '#000000',
                    'active_menu_bg' => '#047857',
                    'active_menu_text_color' => '#ffffff',
                    'hover_menu_bg' => '#047857',
                    'card_bg' => '#ffffff',
                    'table_header_bg' => '#d1fae5',
                    'table_header_text_color' => '#000000',
                    'table_font_size' => '16',
                    'navbar_bg' => '#ffffff',
                    'navbar_text_color' => '#064e3b',
                ]
            ],
            [
                'name' => 'Main',
                'is_default' => true,
                'settings' => [
                    'primary_color' => '#10b981',
                    'danger_color' => '#ef4444',
                    'body_bg_color' => '#ecfdf5',
                    'body_text_color' => '#000000',
                    'sidebar_bg' => '#ffffff',
                    'sidebar_text_color' => '#000000',
                    'active_menu_bg' => '#047857',
                    'active_menu_text_color' => '#ffffff',
                    'hover_menu_bg' => '#047857',
                    'card_bg' => '#ffffff',
                    'table_header_bg' => '#d1fae5',
                    'table_header_text_color' => '#000000',
                    'table_font_size' => '16',
                    'navbar_bg' => '#ffffff',
                    'navbar_text_color' => '#064e3b',
                ]
            ],

            



            [
                'name' => 'Dark Mode',
                'is_default' => true,
                'settings' => [
                    'primary_color' => '#6366f1',
                    'danger_color' => '#ef4444',
                    'body_text_color' => '#e5e7eb',
                    'body_bg_color' => '#1f2937',
                    'sidebar_bg' => '#111827',
                    'sidebar_text_color' => '#9ca3af',
                    'active_menu_bg' => '#374151',
                    'hover_menu_bg' => '#374151',
                    'active_menu_text_color' => '#ffffff',
                    'card_bg' => '#1f2937',
                    'table_header_bg' => '#374151',
                    'table_header_text_color' => '#e5e7eb',
                    'table_font_size' => '16',
                    'navbar_bg' => '#1f2937',
                    'navbar_text_color' => '#e5e7eb',
                ]
            ],
            [
                'name' => 'Midnight Blue',
                'is_default' => true,
                'settings' => [
                    'primary_color' => '#3b82f6',
                    'danger_color' => '#ef4444',
                    'body_text_color' => '#e2e8f0',
                    'body_bg_color' => '#0f172a',
                    'sidebar_bg' => '#1e293b',
                    'sidebar_text_color' => '#94a3b8',
                    'active_menu_bg' => '#334155',
                    'hover_menu_bg' => '#334155',
                    'active_menu_text_color' => '#ffffff',
                    'card_bg' => '#1e293b',
                    'table_header_bg' => '#334155',
                    'table_header_text_color' => '#e2e8f0',
                    'table_font_size' => '16',
                    'navbar_bg' => '#1e293b',
                    'navbar_text_color' => '#e2e8f0',
                ]
            ],
            [
                'name' => 'Forest Green',
                'is_default' => true,
                'settings' => [
                    'primary_color' => '#10b981',
                    'danger_color' => '#ef4444',
                    'body_text_color' => '#064e3b',
                    'body_bg_color' => '#ecfdf5',
                    'sidebar_bg' => '#064e3b',
                    'sidebar_text_color' => '#a7f3d0',
                    'active_menu_bg' => '#047857',
                    'hover_menu_bg' => '#047857',
                    'active_menu_text_color' => '#ffffff',
                    'card_bg' => '#ffffff',
                    'table_header_bg' => '#d1fae5',
                    'table_header_text_color' => '#064e3b',
                    'table_font_size' => '16',
                    'navbar_bg' => '#ffffff',
                    'navbar_text_color' => '#064e3b',
                ]
            ],
            [
                'name' => 'Purple Haze',
                'is_default' => true,
                'settings' => [
                    'primary_color' => '#8b5cf6',
                    'danger_color' => '#ef4444',
                    'body_text_color' => '#f3e8ff',
                    'body_bg_color' => '#2e1065',
                    'sidebar_bg' => '#4c1d95',
                    'sidebar_text_color' => '#ddd6fe',
                    'active_menu_bg' => '#6d28d9',
                    'hover_menu_bg' => '#6d28d9',
                    'active_menu_text_color' => '#ffffff',
                    'card_bg' => '#4c1d95',
                    'table_header_bg' => '#5b21b6',
                    'table_header_text_color' => '#f3e8ff',
                    'table_font_size' => '16',
                    'navbar_bg' => '#4c1d95',
                    'navbar_text_color' => '#f3e8ff',
                ]
            ],
        ];

        foreach ($presets as $preset) {
            ThemePreset::updateOrCreate(
                ['name' => $preset['name']],
                $preset
            );
        }

        // Set default active preset (Light)
        $lightPreset = ThemePreset::where('name', 'Light (Default)')->first();
        if ($lightPreset) {
            \App\Models\ThemeSetting::updateOrCreate(
                ['key' => 'active_theme_preset'],
                ['value' => $lightPreset->id]
            );
        }
    }
}
