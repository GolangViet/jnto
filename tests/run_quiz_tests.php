<?php

declare(strict_types=1);

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__));
}

require BASE_DIR . '/vendor/autoload.php';

// Boot framework
$app = new \Core\Application(BASE_DIR);

function green(string $text): string { return "\e[32m" . $text . "\e[0m"; }
function red(string $text): string { return "\e[31m" . $text . "\e[0m"; }

$testsPassed = 0;
$testsFailed = 0;

function it(string $description, callable $testCase) {
    global $testsPassed, $testsFailed;
    try {
        $testCase();
        echo green("✔ PASSED: ") . $description . "\n";
        $testsPassed++;
    } catch (\Throwable $e) {
        echo red("❌ FAILED: ") . $description . "\n";
        echo red("   Error: " . $e->getMessage()) . "\n";
        echo $e->getTraceAsString() . "\n\n";
        $testsFailed++;
    }
}

function expect(mixed $actual): Expectation {
    return new Expectation($actual);
}

class Expectation {
    public function __construct(private mixed $actual) {}
    
    public function toBe(mixed $expected): void {
        if ($this->actual !== $expected) {
            throw new \Exception("Expected " . var_export($expected, true) . ", got " . var_export($this->actual, true));
        }
    }
    
    public function toBeTrue(): void {
        if ($this->actual !== true) {
            throw new \Exception("Expected true, got " . var_export($this->actual, true));
        }
    }
    
    public function toBeFalse(): void {
        if ($this->actual !== false) {
            throw new \Exception("Expected false, got " . var_export($this->actual, true));
        }
    }
}

echo "\n\e[1;35m--- Running Quiz Q&A Feature Unit Tests ---\e[0m\n\n";

// --- TEST CASES ---

it('Normalizes standard string formatting', function() {
    $raw = "  Vườn Kenrokuen   và  Hẻm núi Kakusenkei!! ";
    $norm = \App\Helpers\TextNormalizer::normalize($raw, false);
    expect($norm)->toBe("vườn kenrokuen và hẻm núi kakusenkei");
});

it('Normalizes and removes Vietnamese accents', function() {
    $raw = "Vườn Kenrokuen";
    $norm = \App\Helpers\TextNormalizer::normalize($raw, true);
    expect($norm)->toBe("vuon kenrokuen");
});

it('Validates single choice answers', function() {
    $valService = new \App\Services\AnswerValidationService();
    $q = ['type' => 'single_choice', 'score' => 2.5];
    $options = [
        ['id' => 10, 'is_correct' => false],
        ['id' => 11, 'is_correct' => true],
        ['id' => 12, 'is_correct' => false],
    ];

    // Correct single answer
    $res1 = $valService->validate($q, $options, ['selected_option_ids' => [11]]);
    expect($res1['is_correct'])->toBeTrue();
    expect($res1['awarded_score'])->toBe(2.5);

    // Incorrect answer
    $res2 = $valService->validate($q, $options, ['selected_option_ids' => [12]]);
    expect($res2['is_correct'])->toBeFalse();
    expect($res2['awarded_score'])->toBe(0.0);

    // Multiple selection for single choice is incorrect
    $res3 = $valService->validate($q, $options, ['selected_option_ids' => [11, 12]]);
    expect($res3['is_correct'])->toBeFalse();
});

it('Validates true/false answers', function() {
    $valService = new \App\Services\AnswerValidationService();
    $q = ['type' => 'true_false', 'score' => 1.0];
    $options = [
        ['id' => 1, 'is_correct' => true],  // True
        ['id' => 2, 'is_correct' => false], // False
    ];

    $res1 = $valService->validate($q, $options, ['selected_option_ids' => [1]]);
    expect($res1['is_correct'])->toBeTrue();

    $res2 = $valService->validate($q, $options, ['selected_option_ids' => [2]]);
    expect($res2['is_correct'])->toBeFalse();
});

it('Validates multiple choice answers', function() {
    $valService = new \App\Services\AnswerValidationService();
    $q = ['type' => 'multiple_choice', 'score' => 3.0];
    $options = [
        ['id' => 20, 'is_correct' => true],
        ['id' => 21, 'is_correct' => false],
        ['id' => 22, 'is_correct' => true],
    ];

    // Correct exact match selection
    $res1 = $valService->validate($q, $options, ['selected_option_ids' => [20, 22]]);
    expect($res1['is_correct'])->toBeTrue();
    expect($res1['awarded_score'])->toBe(3.0);

    // Partial selection is incorrect
    $res2 = $valService->validate($q, $options, ['selected_option_ids' => [20]]);
    expect($res2['is_correct'])->toBeFalse();

    // Extra incorrect option makes it incorrect
    $res3 = $valService->validate($q, $options, ['selected_option_ids' => [20, 21, 22]]);
    expect($res3['is_correct'])->toBeFalse();
});

it('Validates open text exact match rules', function() {
    $valService = new \App\Services\AnswerValidationService();
    $q = ['type' => 'open_text', 'score' => 1.5];
    $accepted = [
        ['answer_text' => 'Chùa Daikozenji', 'match_type' => 'exact'],
        ['answer_text' => 'Daikozenji', 'match_type' => 'exact'],
    ];

    // Case and whitespace insensitive correct exact match
    $res1 = $valService->validate($q, $accepted, ['answer_text' => '  chùa daikozenji ']);
    expect($res1['is_correct'])->toBeTrue();
    expect($res1['awarded_score'])->toBe(1.5);

    // Accent insensitive check (e.g. Daikozenji vs daikôzenji or similar)
    $res2 = $valService->validate($q, $accepted, ['answer_text' => 'daikozenji']);
    expect($res2['is_correct'])->toBeTrue();

    // Incorrect answer
    $res3 = $valService->validate($q, $accepted, ['answer_text' => 'Daikozenji temple']);
    expect($res3['is_correct'])->toBeFalse();
});

it('Validates open text contains match rules', function() {
    $valService = new \App\Services\AnswerValidationService();
    $q = ['type' => 'open_text', 'score' => 1.0];
    $accepted = [
        ['answer_text' => 'Vườn Kenrokuen', 'match_type' => 'contains'],
    ];

    $res1 = $valService->validate($q, $accepted, ['answer_text' => 'Tôi đã ghé thăm Vườn Kenrokuen ngày hôm qua']);
    expect($res1['is_correct'])->toBeTrue();

    $res2 = $valService->validate($q, $accepted, ['answer_text' => 'Chùa Daikozenji']);
    expect($res2['is_correct'])->toBeFalse();
});

it('Validates open text fuzzy match rules', function() {
    $valService = new \App\Services\AnswerValidationService();
    $q = ['type' => 'open_text', 'score' => 1.0];
    $accepted = [
        ['answer_text' => 'Kenrokuen', 'match_type' => 'fuzzy', 'similarity_threshold' => 0.8],
    ];

    // Minor typo (1 diff letter in 9 letters: similarity = 8/9 = 0.88 >= 0.80)
    $res1 = $valService->validate($q, $accepted, ['answer_text' => 'Kenroksen']);
    expect($res1['is_correct'])->toBeTrue();

    // Significant diff
    $res2 = $valService->validate($q, $accepted, ['answer_text' => 'Kenro']);
    expect($res2['is_correct'])->toBeFalse();
});

echo "\n" . green("Tests run completed.") . "\n";
echo "Passed: " . green((string)$testsPassed) . " | Failed: " . ($testsFailed > 0 ? red((string)$testsFailed) : green("0")) . "\n\n";

if ($testsFailed > 0) {
    exit(1);
} else {
    exit(0);
}
