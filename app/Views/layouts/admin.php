<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?= e(config('app.name')) ?> - Admin</title>
    <style>
        body{font-family:Arial,sans-serif;margin:0;background:#f5f7fb;color:#1f2937}.container{max-width:1000px;margin:auto;padding:24px}.nav{background:#1f2937;color:#fff}.nav .container{display:flex;justify-content:space-between;align-items:center}.nav a{color:#fff;text-decoration:none;}.card{background:#fff;padding:20px;border-radius:10px;margin-bottom:16px;box-shadow:0 4px 14px rgba(0,0,0,.05)}input,textarea,select{width:100%;padding:10px;margin:6px 0 14px;border:1px solid #d1d5db;border-radius:6px;box-sizing:border-box}.btn{display:inline-block;padding:9px 14px;border:0;border-radius:6px;background:#2563eb;color:#fff;text-decoration:none;cursor:pointer}.danger{background:#dc2626}.muted{color:#6b7280}.alert{padding:12px;border-radius:6px;margin-bottom:15px;background:#dcfce7}.error{background:#fee2e2}table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid #e5e7eb;text-align:left}
    </style>
    <link rel="stylesheet" href="<?= assets('css/admin/app.css') ?>">
    <script>
        window.APP_URL = <?= json_encode(rtrim(url(), '/')) ?>;
    </script>
</head>
<body>
    <nav class="admin-navbar">
        <div class="container admin-nav-container">
            <div class="admin-brand-wrapper">
                <a href="<?= url('admin/dashboard') ?>" class="admin-brand">
                    <strong><?= e(config('app.name')) ?> Admin Panel</strong>
                    <?php if (setting('maintenance_mode') === '1'): ?>
                        <span style="display: inline-flex; align-items: center; gap: 5px; margin-left: 8px; padding: 2px 8px; border-radius: 9999px; background: #dc2626; color: #fff; font-size: 0.7rem; font-weight: bold; letter-spacing: 0.03em;">
                            <span style="width: 6px; height: 6px; background: #fff; border-radius: 50%; display: inline-block;"></span>
                            Maintenance Active
                        </span>
                    <?php endif; ?>
                </a>

                <?php if ($user = app()->session()->get('user')): ?>
                    <button type="button" 
                            id="admin-nav-toggle" 
                            class="admin-nav-toggle" 
                            aria-label="Toggle navigation menu" 
                            aria-expanded="false" 
                            onclick="toggleAdminNav()">
                        <svg class="icon-menu" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="icon-close" style="width: 20px; height: 20px; display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                <?php endif; ?>
            </div>

            <?php if ($user = app()->session()->get('user')): ?>
                <?php
                $currentPath = app()->request()->path();
                $navItems = [
                    ['label' => 'Dashboard', 'url' => url('admin/dashboard'), 'active' => $currentPath === '/admin/dashboard' || $currentPath === '/admin'],
                    ['label' => 'Surveys', 'url' => url('admin/surveys'), 'active' => str_starts_with($currentPath, '/admin/surveys')],
                    ['label' => 'Quizzes', 'url' => url('admin/quizzes'), 'active' => str_starts_with($currentPath, '/admin/quizzes')],
                    ['label' => 'Users', 'url' => url('admin/users'), 'active' => str_starts_with($currentPath, '/admin/users')],
                    ['label' => 'Settings', 'url' => url('admin/settings'), 'active' => str_starts_with($currentPath, '/admin/settings')],
                ];
                ?>
                <div id="admin-nav-collapse" class="admin-nav-collapse">
                    <nav class="admin-nav-links">
                        <?php foreach ($navItems as $item): ?>
                            <a href="<?= e($item['url']) ?>" 
                               class="admin-nav-link <?= $item['active'] ? 'active' : '' ?>">
                                <?= e($item['label']) ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>

                    <div class="admin-nav-divider"></div>

                    <div class="admin-nav-actions">
                        <a href="<?= url('/') ?>" target="_blank" 
                           class="admin-user-site-link"
                           title="Open public user site in new tab">
                            <span>User Site</span>
                            <svg style="width: 12px; height: 12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>

                        <!-- Desktop User Menu Dropdown -->
                        <div class="dropdown admin-desktop-user-menu" style="position: relative; display: inline-block;">
                            <button type="button" 
                                    onclick="toggleDropdown(event, 'admin-user-menu')" 
                                    class="admin-user-menu-btn" 
                                    style="background: #1f2937; border: 1px solid #374151; color: #e5e7eb; padding: 5px 12px; font-size: 0.85rem; border-radius: 9999px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.15s ease-in-out;">
                                <span style="width: 22px; height: 22px; background: #3b82f6; color: #ffffff; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                                    <?= strtoupper(mb_substr($user['name'] ?? 'A', 0, 1)) ?>
                                </span>
                                <span style="font-weight: 500;"><?= e($user['name']) ?></span>
                                <svg style="width: 12px; height: 12px; color: #9ca3af;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="dropdown-admin-user-menu" class="dropdown-menu" onclick="event.stopPropagation()" style="display: none; position: absolute; right: 0; margin-top: 6px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1); min-width: 250px; z-index: 100; overflow: hidden; text-align: left;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; background: #f9fafb; border-bottom: 1px solid #f3f4f6;">
                                    <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                                        <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                            <?= strtoupper(mb_substr($user['name'] ?? 'A', 0, 1)) ?>
                                        </div>
                                        <div style="min-width: 0; flex: 1;">
                                            <div style="font-weight: 600; color: #111827; font-size: 0.85rem; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e($user['name']) ?>">
                                                <?= e($user['name']) ?>
                                            </div>
                                            <?php if (!empty($user['email'])): ?>
                                                <div style="font-size: 0.75rem; color: #6b7280; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e($user['email']) ?>">
                                                    <?= e($user['email']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <span style="flex-shrink: 0; padding: 2px 7px; background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; border-radius: 9999px; font-size: 0.675rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">
                                        Admin
                                    </span>
                                </div>
                                <div style="padding: 6px;">
                                    <form action="<?= url('logout') ?>" method="post" style="display: block; margin: 0;">
                                        <?= csrf_field() ?>
                                        <button type="submit" 
                                                style="display: flex; align-items: center; gap: 8px; width: 100%; text-align: left; background: none; border: none; padding: 8px 12px; color: #dc2626; font-size: 0.85rem; font-weight: 600; border-radius: 6px; cursor: pointer; transition: background 0.15s;" 
                                                onmouseover="this.style.background='#fee2e2'" 
                                                onmouseout="this.style.background='transparent'">
                                            <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile User Profile Card -->
                    <div class="admin-mobile-user-profile">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0;">
                                    <?= strtoupper(mb_substr($user['name'] ?? 'A', 0, 1)) ?>
                                </div>
                                <div style="min-width: 0; flex: 1;">
                                    <div style="font-weight: 600; color: #f9fafb; font-size: 0.875rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?= e($user['name']) ?>
                                    </div>
                                    <?php if (!empty($user['email'])): ?>
                                        <div style="font-size: 0.75rem; color: #9ca3af; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= e($user['email']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span style="flex-shrink: 0; padding: 2px 7px; background: #1e3a8a; color: #93c5fd; border: 1px solid #1d4ed8; border-radius: 9999px; font-size: 0.65rem; font-weight: 600; text-transform: uppercase;">
                                Admin
                            </span>
                        </div>
                        <form action="<?= url('logout') ?>" method="post" style="margin: 0;">
                            <?= csrf_field() ?>
                            <button type="submit" 
                                    style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; background: #dc2626; border: none; padding: 8px 12px; color: #ffffff; font-size: 0.85rem; font-weight: 600; border-radius: 6px; cursor: pointer; transition: background 0.15s;">
                                <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container">
        <?php if (setting('maintenance_mode') === '1'): ?>
            <div style="background: #fef2f2; border: 1px solid #f87171; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span><strong>Maintenance Mode is Active:</strong> Public visitors cannot access the site. <a href="<?= url('admin/settings') ?>" style="color: #b91c1c; text-decoration: underline; font-weight: 600;">Manage Settings</a></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success = app()->session()->pullFlash('success')): ?>
            <div class="alert"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($error = app()->session()->pullFlash('error')): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <script src="<?= assets('js/admin/app.js') ?>"></script>
</body>
</html>
