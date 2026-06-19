<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situs Sedang Dipelihara - SuperPosko</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="/icon_superposko.png" type="image/png">
    <style>
        :root {
            --bg-base: #F4F7F7;
            --bg-surface: #FFFFFF;
            --bg-muted: #E5EBEB;
            --brand-primary: #38BDF8;
            --text-primary: #111827;
            --text-secondary: #4B5563;
            --border-default: #E2E8F0;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-base: #080A0A;
                --bg-surface: #101414;
                --bg-muted: #1A2020;
                --brand-primary: #38BDF8;
                --text-primary: #F9FAF5;
                --text-secondary: #9CA3AF;
                --border-default: #1E293B;
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            width: 100%;
            max-width: 480px;
            text-align: center;
        }

        .card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-default);
            border-radius: 16px;
            padding: 40px 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            margin-bottom: 24px;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 24px;
        }

        .logo-img {
            height: 64px;
            width: auto;
            object-fit: contain;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: rgba(56, 189, 248, 0.1);
            color: var(--brand-primary);
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background-color: var(--brand-primary);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(56, 189, 248, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(56, 189, 248, 0);
            }
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 12px;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        p {
            font-size: 15px;
            line-height: 1.6;
            color: var(--text-secondary);
        }

        .footer {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .footer a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo-container">
                <img class="logo-img" src="/icon_superposko.png" alt="SuperPosko Icon">
            </div>
            
            <div class="status-badge">
                <span class="status-dot"></span>
                Pemeliharaan Sistem
            </div>

            <h1>SuperPosko</h1>
            <p>Mohon maaf, saat ini situs sedang dalam proses pengembangan & pemeliharaan.</p>
        </div>
        
        <div class="footer">
            <p>Copyright &copy; <a href="https://kuukok.id" target="_blank" rel="noopener noreferrer">Kuukok.id</a></p>
        </div>
    </div>
</body>
</html>
