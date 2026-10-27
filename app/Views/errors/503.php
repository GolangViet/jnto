<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Website đang tạm ngưng phục vụ để bảo trì và nâng cấp hệ thống. Chúng tôi sẽ trở lại sớm nhất.">
    <title>Hệ thống đang bảo trì - <?= e(config('app.name', 'JNTO')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            --card-bg: rgba(30, 41, 59, 0.75);
            --card-border: rgba(255, 255, 255, 0.12);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-primary: #ec4899;
            --accent-secondary: #8b5cf6;
            --accent-gradient: linear-gradient(135deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%);
            --warning-bg: rgba(245, 158, 11, 0.15);
            --warning-border: rgba(245, 158, 11, 0.3);
            --warning-text: #fbbf24;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            background: var(--bg-gradient);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient background glow */
        .glow-sphere {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
        }

        .glow-1 {
            width: 480px;
            height: 480px;
            background: #e11d48;
            top: -120px;
            left: -120px;
        }

        .glow-2 {
            width: 520px;
            height: 520px;
            background: #6366f1;
            bottom: -150px;
            right: -150px;
        }

        .container {
            width: 100%;
            max-width: 620px;
            position: relative;
            z-index: 10;
        }

        .maintenance-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 48px 36px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            text-align: center;
            animation: cardAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardAppear {
            0% {
                opacity: 0;
                transform: translateY(24px) scale(0.97);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .icon-wrapper {
            position: relative;
            width: 96px;
            height: 96px;
            margin: 0 auto 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 28px;
            background: linear-gradient(135deg, rgba(244, 63, 94, 0.15), rgba(168, 85, 247, 0.15));
            border: 1px solid rgba(244, 63, 94, 0.3);
            box-shadow: 0 8px 32px rgba(244, 63, 94, 0.2);
        }

        .icon-wrapper svg {
            width: 48px;
            height: 48px;
            color: #fb7185;
            animation: pulseSlow 3s ease-in-out infinite;
        }

        @keyframes pulseSlow {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.08);
                opacity: 0.85;
            }
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--warning-bg);
            border: 1px solid var(--warning-border);
            color: var(--warning-text);
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .status-badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #f59e0b;
            box-shadow: 0 0 8px #f59e0b;
            animation: blink 2s infinite ease-in-out;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.35; }
        }

        h1 {
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.02em;
            margin-bottom: 16px;
            background: var(--accent-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .message-box {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 20px 22px;
            margin: 24px 0;
            text-align: center;
        }

        .message-text {
            color: #cbd5e1;
            font-size: 1.05rem;
            line-height: 1.65;
            font-weight: 400;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 24px;
            text-align: left;
        }

        @media (max-width: 480px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .maintenance-card {
                padding: 32px 20px;
            }
            h1 {
                font-size: 1.7rem;
            }
        }

        .info-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 14px 16px;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .action-area {
            margin-top: 32px;
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .refresh-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            color: #0f172a;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(255, 255, 255, 0.15);
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
            background: #f1f5f9;
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.25);
        }

        .footer-text {
            margin-top: 28px;
            color: #64748b;
            font-size: 0.82rem;
        }
    </style>
</head>
<body>
    <div class="glow-sphere glow-1"></div>
    <div class="glow-sphere glow-2"></div>

    <div class="container">
        <main class="maintenance-card" role="main">
            <div class="icon-wrapper" aria-hidden="true">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            <div class="status-badge" role="status">
                <span class="status-badge-dot"></span>
                <span>Chế độ bảo trì hệ thống / Maintenance Mode</span>
            </div>

            <h1>Hệ Thống Đang Được Bảo Trì</h1>

            <div class="message-box">
                <p class="message-text">
                    <?= nl2br(e($message ?? 'Website đang trong quá trình bảo trì và nâng cấp hệ thống. Xin vui lòng quay lại sau!')) ?>
                </p>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Trạng thái</div>
                    <div class="info-value">Bảo trì theo lịch trình</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Hệ thống</div>
                    <div class="info-value">Đang tạm ngưng phục vụ</div>
                </div>
            </div>

            <div class="action-area">
                <button type="button" class="refresh-btn" onclick="window.location.reload();">
                    <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Tải lại trang (Refresh)
                </button>
            </div>

            <p class="footer-text">
                &copy; <?= date('Y') ?> <?= e(config('app.name', 'JNTO')) ?>. Chúng tôi chân thành xin lỗi vì sự bất tiện này.
            </p>
        </main>
    </div>
</body>
</html>
