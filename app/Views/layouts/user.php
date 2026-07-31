<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(config('app.name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --primary-light: rgba(79, 70, 229, 0.1);
            --bg-start: #f8fafc;
            --bg-end: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --card-bg: #ffffff;
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, var(--bg-start) 0%, var(--bg-end) 100%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        .nav {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .nav .container {
            max-width: 1000px;
            margin: auto;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav a {
            color: #94a3b8;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: var(--transition);
            margin-left: 20px;
        }

        .nav a:hover {
            color: #ffffff;
        }

        .nav a.brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            margin-left: 0;
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 32px 24px;
            width: 100%;
            box-sizing: border-box;
        }

        main {
            flex-grow: 1;
        }

        .card {
            position: relative;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: var(--transition);
            animation: fadeInUp 0.4s ease-out;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
            z-index: 5;
        }

        .card:has(.dropdown-menu[style*="display: block"]) {
            z-index: 10;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px 16px;
            margin: 8px 0 20px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: inherit;
            background: #ffffff;
            color: var(--text-main);
            box-sizing: border-box;
            transition: var(--transition);
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 22px;
            font-weight: 600;
            font-size: 0.95rem;
            font-family: inherit;
            border-radius: 10px;
            border: none;
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn.danger {
            background: #ef4444;
        }

        .btn.danger:hover {
            background: #dc2626;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }

        .btn-nav-logout {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #cbd5e1;
            padding: 8px 16px;
            font-size: 0.875rem;
        }

        .btn-nav-logout:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            box-shadow: none;
        }

        .muted {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            display: flex;
            align-items: center;
            font-weight: 500;
            animation: fadeIn 0.3s ease-out;
        }

        .alert.error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            text-align: left;
        }

        th {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
<nav class="nav">
    <div class="container">
        <a href="/" class="brand">
            <svg style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span><?= e(config('app.name')) ?></span>
        </a>
        <div class="nav-actions">
            <?php if ($user = app()->session()->get('user')): ?>
                <a href="/quizzes" style="margin-right: 15px;">Quizzes</a>
                <?php if ($mainQuizId = setting('main_quiz_quiz_id')): ?>
                    <a href="/quizzes/<?= (int)$mainQuizId ?>" style="margin-right: 15px; color: #818cf8; font-weight: 600;">★ Main Quiz</a>
                <?php endif; ?>
                <?php if ($mainSurveyId = setting('main_survey_quiz_id')): ?>
                    <a href="/quizzes/<?= (int)$mainSurveyId ?>" style="margin-right: 15px; color: #fb7185; font-weight: 600;">📋 Main Survey</a>
                <?php endif; ?>
                <?php if ($mainOpenId = setting('main_open_quiz_id')): ?>
                    <a href="/quizzes/<?= (int)$mainOpenId ?>" style="margin-right: 15px; color: #2dd4bf; font-weight: 600;">💬 Open Questions</a>
                <?php endif; ?>
                <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                    <a href="/admin/posts" style="margin-right: 15px;">Admin Panel</a>
                <?php endif; ?>
                <span class="muted" style="color: #94a3b8; margin-left: 10px;">Hello, <strong style="color: #f1f5f9;"><?= e($user['name']) ?></strong></span>
                <form action="/logout" method="post" style="display:inline">
                    <?= csrf_field() ?>
                    <button class="btn btn-nav-logout" type="submit">Logout</button>
                </form>
            <?php else: ?>
                <a href="/login" class="btn" style="color: #fff; text-decoration: none; padding: 8px 18px; margin-left: 0;">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container">
    <?php if ($success = app()->session()->pullFlash('success')): ?>
        <div class="alert">
            <svg style="width: 20px; height: 20px; margin-right: 8px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <?= e($success) ?>
        </div>
    <?php endif; ?>
    <?php if ($error = app()->session()->pullFlash('error')): ?>
        <div class="alert error">
            <svg style="width: 20px; height: 20px; margin-right: 8px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <?= e($error) ?>
        </div>
    <?php endif; ?>
    <?= $content ?>
</main>
<script src="/assets/js/app.js"></script>
</body>
</html>
