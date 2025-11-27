<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LavaLust Debug</title>
    <style>
        body { font-family: Consolas, monospace; background: #111; color: #eee; padding: 20px; }
        h1 { color: #f33; }
        pre { background: #222; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🔥 LavaLust Error Debug Page</h1>
    <p><strong>Error Message:</strong> <?= $error_message ?? 'No error message available.' ?></p>
    <h3>Backtrace:</h3>
    <pre><?= $error_trace ?? '' ?></pre>
</body>
</html>
