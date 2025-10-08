<?php
/**
 * AutoWealth Universal Link Handler
 * Handles deep linking to mobile app with fallback to App Store
 */

// Configuration
$config = [
    'ios_app_store' => 'https://apps.apple.com/id/app/autowealth/id1582808842',
    'android_play_store' => 'https://play.google.com/store/apps/developer?id=AUTOWEALTH+PTE+LTD',
    'custom_scheme' => 'autowealth://',
];

// Get path and parameters
$path = isset($_GET['path']) ? $_GET['path'] : 'create_portfolio';
$type = isset($_GET['type']) ? $_GET['type'] : 'cpf';

// Build custom scheme URL
$customSchemeUrl = $config['custom_scheme'] . $path . '?type=' . urlencode($type);

// Detect platform
function getPlatform() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if (stripos($userAgent, 'iPhone') !== false ||
        stripos($userAgent, 'iPad') !== false ||
        stripos($userAgent, 'iPod') !== false) {
        return 'ios';
    }

    if (stripos($userAgent, 'Android') !== false) {
        return 'android';
    }

    return 'web';
}

$platform = getPlatform();

// Desktop users - redirect directly to store
if ($platform === 'web') {
    header('Location: ' . $config['ios_app_store']);
    exit;
}

// Get appropriate store URL
$storeUrl = ($platform === 'ios') ? $config['ios_app_store'] : $config['android_play_store'];

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="apple-itunes-app" content="app-id=1582808842">
    <title>AutoWealth - Download Our App</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 48px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 420px;
            width: 100%;
            text-align: center;
        }

        .icon {
            font-size: 72px;
            margin-bottom: 24px;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        h1 {
            color: #667eea;
            font-size: 32px;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .message {
            color: #555;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .loader {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #667eea;
            animation: loading 1.4s ease-in-out infinite;
        }

        .dot:nth-child(1) { animation-delay: 0s; }
        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes loading {
            0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
            40% { transform: scale(1.2); opacity: 1; }
        }

        .status-text {
            color: #667eea;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .small-text {
            color: #999;
            font-size: 13px;
            margin-top: 24px;
        }

        @media (max-width: 480px) {
            .container {
                padding: 40px 24px;
            }

            h1 {
                font-size: 28px;
            }

            .message {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📱</div>
        <h1>Download Our App Now!</h1>
        <p class="message">Get started with AutoWealth and take control of your investment portfolio</p>

        <div class="loader">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>

        <p class="status-text" id="status">Opening AutoWealth...</p>
        <p class="small-text">Redirecting you in a moment</p>
    </div>

    <script>
        const platform = '<?php echo $platform; ?>';
        const customScheme = '<?php echo htmlspecialchars($customSchemeUrl, ENT_QUOTES); ?>';
        const storeUrl = '<?php echo htmlspecialchars($storeUrl, ENT_QUOTES); ?>';

        <?php if ($platform === 'ios'): ?>
        // iOS: Try custom scheme, then App Store
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = customScheme;
        document.body.appendChild(iframe);

        setTimeout(() => {
            document.getElementById('status').textContent = 'Opening App Store...';
            window.location.href = storeUrl;
        }, 1000);

        <?php else: ?>
        // Android: Use Intent URL
        const intentUrl = 'intent://' + '<?php echo $path; ?>' + '?type=<?php echo urlencode($type); ?>' +
                         '#Intent;scheme=autowealth;package=sg.autowealth.mobile;' +
                         'S.browser_fallback_url=' + encodeURIComponent(storeUrl) + ';end';

        setTimeout(() => {
            window.location.href = intentUrl;
        }, 1000);
        <?php endif; ?>
    </script>
</body>
</html>
