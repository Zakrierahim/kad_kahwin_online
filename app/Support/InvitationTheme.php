<?php

namespace App\Support;

final class InvitationTheme
{
    /**
     * @var array<string, array{label: string, primary_color: string, secondary_color: string, background_color: string, font: string, floral_style: string}>
     */
    public const THEMES = [
        'elegant_cream' => [
            'label' => 'Krim Elegan',
            'primary_color' => '#B08D57',
            'secondary_color' => '#EADCC8',
            'background_color' => '#FFF9F0',
            'font' => 'serif',
            'floral_style' => 'classic',
        ],
        'dusty_pink' => [
            'label' => 'Merah Jambu Lembut',
            'primary_color' => '#A85A6A',
            'secondary_color' => '#F3D5D8',
            'background_color' => '#FFF6F7',
            'font' => 'serif',
            'floral_style' => 'jasmine',
        ],
        'sage_green' => [
            'label' => 'Hijau Sage',
            'primary_color' => '#6E8B74',
            'secondary_color' => '#D7E4D8',
            'background_color' => '#F6F8F4',
            'font' => 'serif',
            'floral_style' => 'jasmine',
        ],
        'floral_ivory' => [
            'label' => 'Gading Bunga',
            'primary_color' => '#C48B9F',
            'secondary_color' => '#F6E7EA',
            'background_color' => '#FFFCF8',
            'font' => 'script',
            'floral_style' => 'classic',
        ],
        'royal_gold' => [
            'label' => 'Emas Diraja',
            'primary_color' => '#8C6A2F',
            'secondary_color' => '#E6D3A3',
            'background_color' => '#FBF6EA',
            'font' => 'serif',
            'floral_style' => 'classic',
        ],
        'traditional_malay' => [
            'label' => 'Tradisional Melayu',
            'primary_color' => '#7A1F2B',
            'secondary_color' => '#E7C98A',
            'background_color' => '#FBF7F0',
            'font' => 'serif',
            'floral_style' => 'songket',
        ],
        'minimalist_modern' => [
            'label' => 'Moden Minimal',
            'primary_color' => '#3F4A45',
            'secondary_color' => '#D9DDD8',
            'background_color' => '#F7F6F3',
            'font' => 'sans',
            'floral_style' => 'minimal',
        ],
    ];

    /**
     * @var array<string, string>
     */
    public const FONTS = [
        'serif' => 'Serif Klasik',
        'sans' => 'Sans Moden',
        'script' => 'Skrip Elegan',
    ];

    /**
     * @var array<string, string>
     */
    public const FLORALS = [
        'classic' => 'Klasik',
        'jasmine' => 'Melur',
        'songket' => 'Songket',
        'minimal' => 'Minimal',
        'none' => 'Tiada',
    ];

    /**
     * @param  array<string, mixed>|null  $settings
     * @return array{theme: string, label: string, primary_color: string, secondary_color: string, background_color: string, font: string, floral_style: string, music_enabled: bool, font_class: string, floral_class: string}
     */
    public static function resolve(?array $settings): array
    {
        $settings ??= [];
        $themeKey = is_string($settings['theme'] ?? null) ? $settings['theme'] : 'elegant_cream';

        if (! isset(self::THEMES[$themeKey])) {
            $themeKey = 'elegant_cream';
        }

        $base = self::THEMES[$themeKey];
        $font = is_string($settings['font'] ?? null) && isset(self::FONTS[$settings['font']])
            ? $settings['font']
            : $base['font'];
        $floral = is_string($settings['floral_style'] ?? null) && isset(self::FLORALS[$settings['floral_style']])
            ? $settings['floral_style']
            : $base['floral_style'];

        return [
            'theme' => $themeKey,
            'label' => $base['label'],
            'primary_color' => self::hex($settings['primary_color'] ?? null, $base['primary_color']),
            'secondary_color' => self::hex($settings['secondary_color'] ?? null, $base['secondary_color']),
            'background_color' => self::hex($settings['background_color'] ?? null, $base['background_color']),
            'font' => $font,
            'floral_style' => $floral,
            'music_enabled' => (bool) ($settings['music_enabled'] ?? false),
            'font_class' => match ($font) {
                'sans' => 'font-inv-sans',
                'script' => 'font-inv-script',
                default => 'font-inv-serif',
            },
            'floral_class' => match ($floral) {
                'jasmine' => 'floral-jasmine',
                'songket' => 'floral-songket',
                'minimal' => 'floral-minimal',
                'none' => 'floral-none',
                default => 'floral-classic',
            },
        ];
    }

    /**
     * @return array<string, array{primary_color: string, secondary_color: string, background_color: string, font: string, floral_style: string}>
     */
    public static function presets(): array
    {
        $presets = [];

        foreach (self::THEMES as $key => $theme) {
            $presets[$key] = [
                'primary_color' => $theme['primary_color'],
                'secondary_color' => $theme['secondary_color'],
                'background_color' => $theme['background_color'],
                'font' => $theme['font'],
                'floral_style' => $theme['floral_style'],
            ];
        }

        return $presets;
    }

    private static function hex(mixed $value, string $fallback): string
    {
        if (! is_string($value) || preg_match('/^#[0-9A-Fa-f]{6}$/', $value) !== 1) {
            return strtoupper($fallback);
        }

        return strtoupper($value);
    }
}
