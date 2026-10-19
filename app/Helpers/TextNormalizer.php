<?php

declare(strict_types=1);

namespace App\Helpers;

final class TextNormalizer
{
    /**
     * Normalize string for comparison.
     *
     * @param string $value
     * @param bool $removeAccents
     * @return string
     */
    public static function normalize(string $value, bool $removeAccents = false): string
    {
        // Unicode normalization
        if (class_exists(\Normalizer::class)) {
            $value = \Normalizer::normalize($value, \Normalizer::FORM_C);
        }

        // Convert to lowercase
        $value = mb_strtolower($value, 'UTF-8');

        // Remove unnecessary punctuation (replace with space to prevent joining words)
        $value = preg_replace('/[[:punct:]]+/u', ' ', $value) ?? $value;

        // Optionally remove accents
        if ($removeAccents) {
            $value = self::removeAccents($value);
        }

        // Replace multiple spaces with one space
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        // Trim leading and trailing spaces
        return trim($value);
    }

    /**
     * Remove accents/diacritics from Vietnamese text.
     *
     * @param string $str
     * @return string
     */
    private static function removeAccents(string $str): string
    {
        $accents = [
            'a' => ['à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ'],
            'e' => ['è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ'],
            'i' => ['ì', 'í', 'ị', 'ỉ', 'ĩ'],
            'o' => ['ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ'],
            'u' => ['ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ'],
            'y' => ['ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ'],
            'd' => ['đ'],
        ];

        foreach ($accents as $nonAccent => $accentList) {
            $str = str_replace($accentList, $nonAccent, $str);
        }

        return $str;
    }
}
