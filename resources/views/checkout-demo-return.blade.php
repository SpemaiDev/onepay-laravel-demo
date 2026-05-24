<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment return (demo)</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 42rem; margin: 2rem auto; padding: 0 1rem; }
        pre { background: #f4f4f5; padding: 1rem; border-radius: 8px; overflow: auto; font-size: 0.85rem; }
        a { color: #0f766e; }
    </style>
</head>
<body>
    <h1>Returned from payment flow</h1>
    <p>This page is the demo <code>transaction_redirect_url</code>. In production, verify the payment server-side using OnePay’s callback or status APIs — do not trust query parameters alone.</p>
    <p><a href="{{ route('demo.checkout') }}">← Back to checkout form</a></p>
    <h2>Query string</h2>
    @if (empty($query))
        <p><em>No query parameters.</em></p>
    @else
        <pre>{{ json_encode($query, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    @endif
</body>
</html>
