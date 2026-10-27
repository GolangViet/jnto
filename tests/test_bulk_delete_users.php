<?php

declare(strict_types=1);

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__));
}

require BASE_DIR . '/vendor/autoload.php';

$app = new \Core\Application(BASE_DIR);
$db = \Core\Database::connection();

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
            throw new \AssertionError("Expected " . var_export($expected, true) . ", got " . var_export($this->actual, true));
        }
    }

    public function toBeGreaterThan(int|float $expected): void {
        if ($this->actual <= $expected) {
            throw new \AssertionError("Expected value greater than " . $expected . ", got " . var_export($this->actual, true));
        }
    }
}

echo "\n--- Running Bulk Delete Users Unit & Integration Tests ---\n\n";

$userService = new \App\Services\UserService();
$userRepo = new \App\Repositories\UserRepository();

// Helper to find a quiz to attach attempts to
$quizStmt = $db->query("SELECT id FROM cms.quizzes LIMIT 1");
$quizId = (int)$quizStmt->fetchColumn();
if (!$quizId) {
    // create a temporary quiz if none exists
    $db->exec("INSERT INTO cms.quizzes (title, status) VALUES ('Test Quiz For Bulk Delete', 'published')");
    $quizId = (int)$db->lastInsertId();
}

it('successfully deletes multiple users and all their related data', function() use ($db, $userService, $quizId) {
    // 1. Create 2 test users
    $ts = time();
    $u1Email = "test_bulk_1_{$ts}@example.com";
    $u2Email = "test_bulk_2_{$ts}@example.com";
    $uControlEmail = "test_control_{$ts}@example.com";

    $userService->createUser([
        'username' => "test_bulk_1_{$ts}",
        'name' => 'Bulk Test User 1',
        'email' => $u1Email,
        'password' => 'secret123',
        'role' => 'user',
    ]);
    $u1 = $userService->findByEmail($u1Email);
    $u1Id = (int)$u1['id'];

    $userService->createUser([
        'username' => "test_bulk_2_{$ts}",
        'name' => 'Bulk Test User 2',
        'email' => $u2Email,
        'password' => 'secret123',
        'role' => 'user',
    ]);
    $u2 = $userService->findByEmail($u2Email);
    $u2Id = (int)$u2['id'];

    $userService->createUser([
        'username' => "test_control_{$ts}",
        'name' => 'Control User',
        'email' => $uControlEmail,
        'password' => 'secret123',
        'role' => 'user',
    ]);
    $uControl = $userService->findByEmail($uControlEmail);
    $uControlId = (int)$uControl['id'];

    // 2. Attach related data to User 1
    // Facebook post
    $db->prepare("INSERT INTO cms.user_facebook_posts (user_id, facebook_url, score) VALUES (:uid, 'https://facebook.com/test1', 10)")
       ->execute(['uid' => $u1Id]);

    // Quiz attempt
    $stmtAtt = $db->prepare("INSERT INTO cms.quiz_attempts (quiz_id, user_id, status, score) VALUES (:qid, :uid, 'submitted', 10) RETURNING id");
    $stmtAtt->execute(['qid' => $quizId, 'uid' => $u1Id]);
    $attempt1Id = (int)$stmtAtt->fetchColumn();

    // 3. Attach related data to User 2
    $db->prepare("INSERT INTO cms.user_facebook_posts (user_id, facebook_url, score) VALUES (:uid, 'https://facebook.com/test2', 20)")
       ->execute(['uid' => $u2Id]);
    
    $stmtAtt2 = $db->prepare("INSERT INTO cms.quiz_attempts (quiz_id, user_id, status, score) VALUES (:qid, :uid, 'submitted', 20) RETURNING id");
    $stmtAtt2->execute(['qid' => $quizId, 'uid' => $u2Id]);
    $attempt2Id = (int)$stmtAtt2->fetchColumn();

    // 4. Attach related data to Control User
    $db->prepare("INSERT INTO cms.user_facebook_posts (user_id, facebook_url, score) VALUES (:uid, 'https://facebook.com/control', 50)")
       ->execute(['uid' => $uControlId]);

    // Verify all related data exists before deletion
    $attCount = (int)$db->query("SELECT COUNT(*) FROM cms.quiz_attempts WHERE user_id IN ($u1Id, $u2Id)")->fetchColumn();
    expect($attCount)->toBe(2);

    $fpCount = (int)$db->query("SELECT COUNT(*) FROM cms.user_facebook_posts WHERE user_id IN ($u1Id, $u2Id)")->fetchColumn();
    expect($fpCount)->toBe(2);

    // 5. Execute bulk delete on User 1 and User 2
    $deletedCount = $userService->bulkDeleteUsers([$u1Id, $u2Id]);
    expect($deletedCount)->toBe(2);

    // 6. Assert users are gone
    expect($userService->getUserById($u1Id))->toBe(null);
    expect($userService->getUserById($u2Id))->toBe(null);

    // 7. Assert related data for deleted users is gone
    $attCountAfter = (int)$db->query("SELECT COUNT(*) FROM cms.quiz_attempts WHERE user_id IN ($u1Id, $u2Id)")->fetchColumn();
    expect($attCountAfter)->toBe(0);

    $fpCountAfter = (int)$db->query("SELECT COUNT(*) FROM cms.user_facebook_posts WHERE user_id IN ($u1Id, $u2Id)")->fetchColumn();
    expect($fpCountAfter)->toBe(0);

    // 8. Assert control user and their related data are still intact
    $ctrlUser = $userService->getUserById($uControlId);
    expect($ctrlUser !== null)->toBe(true);

    $ctrlFpCount = (int)$db->query("SELECT COUNT(*) FROM cms.user_facebook_posts WHERE user_id = $uControlId")->fetchColumn();
    expect($ctrlFpCount)->toBe(1);

    // Clean up control user
    $userService->deleteUser($uControlId);
    expect($userService->getUserById($uControlId))->toBe(null);
});

it('handles empty or invalid user IDs gracefully without errors', function() use ($userService) {
    $count = $userService->bulkDeleteUsers([]);
    expect($count)->toBe(0);

    $count2 = $userService->bulkDeleteUsers([-1, 0]);
    expect($count2)->toBe(0);

    $count3 = $userService->bulkDeleteUsers([99999999]);
    expect($count3)->toBe(0);
});

it('single deleteUser also cleans up related data properly', function() use ($db, $userService, $quizId) {
    $ts = time();
    $uEmail = "test_single_{$ts}@example.com";
    $userService->createUser([
        'username' => "test_single_{$ts}",
        'name' => 'Single Test User',
        'email' => $uEmail,
        'password' => 'secret123',
        'role' => 'user',
    ]);
    $u = $userService->findByEmail($uEmail);
    $uId = (int)$u['id'];

    // Add facebook post and attempt
    $db->prepare("INSERT INTO cms.user_facebook_posts (user_id, facebook_url, score) VALUES (:uid, 'https://facebook.com/test_single', 15)")
       ->execute(['uid' => $uId]);

    $stmtAtt = $db->prepare("INSERT INTO cms.quiz_attempts (quiz_id, user_id, status, score) VALUES (:qid, :uid, 'submitted', 15) RETURNING id");
    $stmtAtt->execute(['qid' => $quizId, 'uid' => $uId]);

    // Single delete
    $deleted = $userService->deleteUser($uId);
    expect($deleted)->toBe(true);

    // Verify user is gone
    expect($userService->getUserById($uId))->toBe(null);

    // Verify related data is gone
    $fpCount = (int)$db->query("SELECT COUNT(*) FROM cms.user_facebook_posts WHERE user_id = $uId")->fetchColumn();
    expect($fpCount)->toBe(0);

    $attCount = (int)$db->query("SELECT COUNT(*) FROM cms.quiz_attempts WHERE user_id = $uId")->fetchColumn();
    expect($attCount)->toBe(0);
});

it('supports customizable per_page limit in pagination', function() use ($userService) {
    $res15 = $userService->getPaginatedUsers([], 1, 15);
    expect(count($res15['items']) <= 15)->toBe(true);

    $res25 = $userService->getPaginatedUsers([], 1, 25);
    expect(count($res25['items']) <= 25)->toBe(true);

    $res50 = $userService->getPaginatedUsers([], 1, 50);
    expect(count($res50['items']) <= 50)->toBe(true);
});

it('preserves query parameters on redirect after user deletion', function() {
    $controller = new \App\Controllers\Admin\UserController();
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('getRedirectUrlWithFilters');
    $method->setAccessible(true);

    // Mock $_POST input parameters
    $_POST['search'] = 'testuser';
    $_POST['role'] = 'admin';
    $_POST['facebook_post'] = 'yes';
    $_POST['per_page'] = 25;
    $_POST['page'] = 2;

    $request = new \Core\Request();
    $redirectUrl = $method->invoke($controller, $request);

    expect(str_contains($redirectUrl, 'search=testuser'))->toBe(true);
    expect(str_contains($redirectUrl, 'role=admin'))->toBe(true);
    expect(str_contains($redirectUrl, 'facebook_post=yes'))->toBe(true);
    expect(str_contains($redirectUrl, 'per_page=25'))->toBe(true);
    expect(str_contains($redirectUrl, 'page=2'))->toBe(true);

    // Clean up globals
    unset($_POST['search'], $_POST['role'], $_POST['facebook_post'], $_POST['per_page'], $_POST['page']);
});

echo "\nValidation completed.\n";
echo "Passed: " . $testsPassed . " | Failed: " . $testsFailed . "\n\n";

if ($testsFailed > 0) {
    exit(1);
}
