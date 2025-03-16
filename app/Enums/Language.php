<?php
namespace App\Enums;

enum Language: string
{
    case RUS = 'ru';
    case KG = 'kg';

    public function title(string $lang = null): string
    {
        $value = match ($this) {
            self::RUS => 'Русский',
            self::KG => 'Кыргызский',
        };

        return __($value, locale: $lang);
    }

    public function icon(): string
    {
        return match ($this) {
            self::RUS => '🇷🇺',
            self::KG => '🇰🇬',
        };
    }

    public static function default(): self
    {
        return self::RUS;
    }

}
