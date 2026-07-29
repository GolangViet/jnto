<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\TextNormalizer;

final class AnswerValidationService
{
    /**
     * Validate an answer for a question.
     *
     * @param array $question The question record (containing type, score, etc.)
     * @param array $optionsOrAcceptedAnswers Options for choice-based, or accepted answers for open-text.
     * @param array $userAnswer Payload containing selected_option_ids or answer_text.
     * @return array Contains 'is_correct' (bool) and 'awarded_score' (float).
     */
    public function validate(array $question, array $optionsOrAcceptedAnswers, array $userAnswer): array
    {
        $type = $question['type'] ?? '';
        $questionScore = (float) ($question['score'] ?? 1.0);

        switch ($type) {
            case 'single_choice':
            case 'true_false':
                $selectedOptionIds = $userAnswer['selected_option_ids'] ?? [];
                if (!is_array($selectedOptionIds)) {
                    $selectedOptionIds = [$selectedOptionIds];
                }
                $selectedOptionIds = array_map('intval', $selectedOptionIds);
                
                if (count($selectedOptionIds) !== 1) {
                    return ['is_correct' => false, 'awarded_score' => 0.0];
                }

                $selectedId = $selectedOptionIds[0];
                $correctOptionId = null;
                foreach ($optionsOrAcceptedAnswers as $option) {
                    if (filter_var($option['is_correct'] ?? false, FILTER_VALIDATE_BOOL)) {
                        $correctOptionId = (int) $option['id'];
                        break;
                    }
                }

                $isCorrect = ($correctOptionId !== null && $selectedId === $correctOptionId);
                return [
                    'is_correct' => $isCorrect,
                    'awarded_score' => $isCorrect ? $questionScore : 0.0,
                ];

            case 'multiple_choice':
                $selectedOptionIds = $userAnswer['selected_option_ids'] ?? [];
                if (!is_array($selectedOptionIds)) {
                    $selectedOptionIds = [$selectedOptionIds];
                }
                $selectedOptionIds = array_map('intval', $selectedOptionIds);
                sort($selectedOptionIds);

                $correctOptionIds = [];
                foreach ($optionsOrAcceptedAnswers as $option) {
                    if (filter_var($option['is_correct'] ?? false, FILTER_VALIDATE_BOOL)) {
                        $correctOptionIds[] = (int) $option['id'];
                    }
                }
                sort($correctOptionIds);

                $isCorrect = (!empty($correctOptionIds) && $selectedOptionIds === $correctOptionIds);
                return [
                    'is_correct' => $isCorrect,
                    'awarded_score' => $isCorrect ? $questionScore : 0.0,
                ];

            case 'open_text':
                $userAnswerText = $userAnswer['answer_text'] ?? '';
                if ($userAnswerText === null || trim((string) $userAnswerText) === '') {
                    return ['is_correct' => false, 'awarded_score' => 0.0];
                }

                // Check standard comparison and optional accent-free comparison
                $userNormStandard = TextNormalizer::normalize($userAnswerText, false);
                $userNormNoAccents = TextNormalizer::normalize($userAnswerText, true);

                $isCorrect = false;

                foreach ($optionsOrAcceptedAnswers as $accepted) {
                    $matchType = $accepted['match_type'] ?? 'exact';
                    $acceptedText = $accepted['answer_text'] ?? '';
                    $threshold = isset($accepted['similarity_threshold']) ? (float) $accepted['similarity_threshold'] : 0.85;

                    $accNormStandard = TextNormalizer::normalize($acceptedText, false);
                    $accNormNoAccents = TextNormalizer::normalize($acceptedText, true);

                    if ($matchType === 'exact') {
                        if ($userNormStandard === $accNormStandard || $userNormNoAccents === $accNormNoAccents) {
                            $isCorrect = true;
                            break;
                        }
                    } elseif ($matchType === 'contains') {
                        if (($accNormStandard !== '' && str_contains($userNormStandard, $accNormStandard)) ||
                            ($accNormNoAccents !== '' && str_contains($userNormNoAccents, $accNormNoAccents))) {
                            $isCorrect = true;
                            break;
                        }
                    } elseif ($matchType === 'fuzzy') {
                        $simStandard = self::calculateSimilarity($userNormStandard, $accNormStandard);
                        $simNoAccents = self::calculateSimilarity($userNormNoAccents, $accNormNoAccents);

                        if ($simStandard >= $threshold || $simNoAccents >= $threshold) {
                            $isCorrect = true;
                            break;
                        }
                    }
                }

                return [
                    'is_correct' => $isCorrect,
                    'awarded_score' => $isCorrect ? $questionScore : 0.0,
                ];

            default:
                return ['is_correct' => false, 'awarded_score' => 0.0];
        }
    }

    /**
     * Calculate similarity ratio between two strings (0.0 to 1.0).
     */
    private static function calculateSimilarity(string $s1, string $s2): float
    {
        if ($s1 === $s2) {
            return 1.0;
        }

        $len1 = strlen($s1);
        $len2 = strlen($s2);
        if ($len1 === 0 || $len2 === 0) {
            return 0.0;
        }

        $lev = levenshtein($s1, $s2);
        $maxLen = max($len1, $len2);

        return 1.0 - ($lev / $maxLen);
    }
}
