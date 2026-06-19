<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superposko - Coming Soon</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --primary: #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.15);
            --accent: #a855f7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            position: relative;
            padding: 2rem;
        }

        /* Ambient Background Glows */
        .glow-1 {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
            z-index: 1;
            pointer-events: none;
        }

        .glow-2 {
            position: absolute;
            bottom: -10%;
            right: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.08) 0%, transparent 70%);
            z-index: 1;
            pointer-events: none;
        }

        /* Container & Main Content */
        main {
            z-index: 2;
            text-align: center;
            margin: auto;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
            animation: fadeIn 1s ease-out;
        }

        .status-badge {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #eab308;
            border-radius: 50%;
            box-shadow: 0 0 8px #eab308;
            animation: pulse 2s infinite;
        }

        h1 {
            font-size: clamp(2.5rem, 8vw, 4rem);
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 30%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        p {
            font-size: clamp(1rem, 3vw, 1.25rem);
            color: var(--text-muted);
            font-weight: 300;
            line-height: 1.6;
        }

        .progress-container {
            width: 100%;
            max-width: 320px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            height: 6px;
            border-radius: 9999px;
            margin-top: 1rem;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            width: 65%;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 9999px;
            position: relative;
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.5);
        }

        /* Footer */
        footer {
            z-index: 2;
            font-size: 0.875rem;
            color: var(--text-muted);
            font-weight: 400;
            animation: fadeIn 1.2s ease-out;
        }

        footer a {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 600;
            position: relative;
            transition: color 0.3s ease;
        }

        footer a::after {
            content: '';
            position: absolute;
            width: 100%;
            transform: scaleX(0);
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: var(--primary);
            transform-origin: bottom right;
            transition: transform 0.25s ease-out;
        }

        footer a:hover {
            color: var(--primary);
        }

        footer a:hover::after {
            transform: scaleX(1);
            transform-origin: bottom left;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(234, 179, 8, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 8px rgba(234, 179, 8, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(234, 179, 8, 0);
            }
        }
    </style>
</head>
<body>

    <div class="glow-1"></div>
    <div class="glow-2"></div>

    <main>
        <div class="status-badge">
            <span class="status-dot"></span>
            Tahap Pengerjaan
        </div>
        <h1>SuperPosko</h1>
        <p>Mohon maaf, saat ini situs sedang dalam proses pengembangan & pemeliharaan.</p>
        <div class="progress-container">
            <div class="progress-bar"></div>
        </div>
    </main>

    <footer>
        <p>Copyright &copy; <a href="https://kuukok.my.id" target="_blank" rel="noopener noreferrer">Kuukok.id</a></p>
    </footer>

</body>
</html>
