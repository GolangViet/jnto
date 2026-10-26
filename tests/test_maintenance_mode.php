<?php

declare(strict_types=1);

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__));
}

require BASE_DIR . '/vendor/autoload.php';

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

echo "\n\e[1;35m--- Running Maintenance Mode Unit Tests ---\e[0m\n\n";

it('Allows all requests when maintenance mode is disabled', function() {
    $middleware = new \App\Middleware\MaintenanceMiddleware();
    $request = app()->request();
    $response = app()->response();
    
    // Ensure maintenance mode is off
    $repo = new \App\Repositories\SettingRepository();
    $repo->setSetting('maintenance_mode', '0');
    
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    
    $result = $middleware->handle($request, $response);
    expect($result)->toBeTrue();
});

it('Exempts admin URLs when maintenance mode is active', function() {
    $middleware = new \App\Middleware\MaintenanceMiddleware();
    $request = app()->request();
    $response = app()->response();
    
    $repo = new \App\Repositories\SettingRepository();
    $repo->setSetting('maintenance_mode', '1');
    
    // Test /admin/login
    $_SERVER['REQUEST_URI'] = '/admin/login';
    expect($middleware->handle($request, $response))->toBeTrue();
    
    // Test /admin/settings
    $_SERVER['REQUEST_URI'] = '/admin/settings';
    expect($middleware->handle($request, $response))->toBeTrue();

    // Test /api/admin/quizzes
    $_SERVER['REQUEST_URI'] = '/api/admin/quizzes';
    expect($middleware->handle($request, $response))->toBeTrue();
    
    // Test /logout
    $_SERVER['REQUEST_URI'] = '/logout';
    expect($middleware->handle($request, $response))->toBeTrue();
    
    // Reset
    $repo->setSetting('maintenance_mode', '0');
});

it('Exempts authenticated admin users across any URL', function() {
    $middleware = new \App\Middleware\MaintenanceMiddleware();
    $request = app()->request();
    $response = app()->response();
    
    $repo = new \App\Repositories\SettingRepository();
    $repo->setSetting('maintenance_mode', '1');
    
    // Simulate logged-in admin
    app()->session()->put('user', ['id' => 1, 'role' => 'admin', 'name' => 'Admin']);
    
    $_SERVER['REQUEST_URI'] = '/quizzes';
    expect($middleware->handle($request, $response))->toBeTrue();
    
    $_SERVER['REQUEST_URI'] = '/';
    expect($middleware->handle($request, $response))->toBeTrue();
    
    // Clean up
    app()->session()->forget('user');
    $repo->setSetting('maintenance_mode', '0');
});

it('Renders custom HTML/CSS content when provided or falls back to default card', function() {
    // Case 1: Custom HTML with <style>
    $customHtml = '<style>.custom-test { color: red; }</style><div class="custom-test">Custom Maintenance Alert</div>';
    $renderedCustom = \Core\View::render('pages/index-2', ['message' => $customHtml], false);
    
    // Must contain raw <style> tag without being converted to &lt;style&gt;
    expect(str_contains($renderedCustom, '<style>.custom-test { color: red; }</style>'))->toBeTrue();
    expect(str_contains($renderedCustom, 'Custom Maintenance Alert'))->toBeTrue();
    // Default heading should NOT be shown
    expect(str_contains($renderedCustom, 'CHƯƠNG TRÌNH ĐÃ HẾT HẠN'))->toBeFalse();
    
    // Case 2: Empty message -> default card rendered
    $renderedDefault = \Core\View::render('pages/index-2', ['message' => null], false);
    expect(str_contains($renderedDefault, 'CHƯƠNG TRÌNH ĐÃ HẾT HẠN'))->toBeTrue();
    expect(str_contains($renderedDefault, 'Theo dõi Fanpage JNTO'))->toBeTrue();
});

echo "\n" . green("Validation completed.") . "\n";
echo "Passed: " . green((string)$testsPassed) . " | Failed: " . ($testsFailed > 0 ? red((string)$testsFailed) : green("0")) . "\n\n";
