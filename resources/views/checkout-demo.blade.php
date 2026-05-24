<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OnePay checkout demo</title>
    <style>
        :root { font-family: system-ui, sans-serif; }
        body { max-width: 42rem; margin: 2rem auto; padding: 0 1rem; color: #111; }
        h1 { font-size: 1.35rem; }
        label { display: block; font-size: 0.8rem; font-weight: 600; margin-top: 0.85rem; }
        input, select, textarea { width: 100%; padding: 0.45rem 0.5rem; margin-top: 0.2rem; box-sizing: border-box; }
        textarea { min-height: 4rem; }
        .hint { font-size: 0.75rem; color: #555; margin-top: 0.15rem; }
        button { margin-top: 1.25rem; padding: 0.55rem 1.2rem; background: #0f766e; color: #fff; border: 0; border-radius: 6px; cursor: pointer; font-weight: 600; }
        button:hover { background: #0d9488; }
        .alert { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; }
        .note { background: #f0fdfa; border: 1px solid #99f6e4; padding: 0.75rem 1rem; border-radius: 6px; font-size: 0.85rem; margin-bottom: 1.25rem; }
        pre { background: #f4f4f5; padding: 0.75rem; border-radius: 6px; overflow: auto; font-size: 0.75rem; }
    </style>
</head>
<body>
    <h1>OnePay checkout link (demo)</h1>
    <p class="note">
        Set <code>ONEPAY_APP_ID</code>, <code>ONEPAY_APP_TOKEN</code>, and <code>ONEPAY_HASH_SALT</code> in <code>.env</code>.
        Set <code>APP_URL</code> to the URL you use with <code>php artisan serve</code> so the return URL is correct.
    </p>

    @if ($errors->any())
        <div class="alert">
            <strong>Could not start checkout.</strong>
            <ul style="margin:0.5rem 0 0 1rem;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('onepay_context'))
        <pre>{{ json_encode(session('onepay_context'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    @endif

    @if (session('gateway_response'))
        <p><strong>Gateway body</strong></p>
        <pre>{{ json_encode(session('gateway_response'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    @endif

    <form method="post" action="{{ route('demo.checkout.submit') }}">
        @csrf
        <label for="reference">Reference (optional, 10–21 chars if set)</label>
        <input id="reference" name="reference" value="{{ old('reference') }}" maxlength="21" placeholder="Leave empty to auto-generate">
        <div class="hint">Auto: DEMO + 17-char ULID (21 chars max for OnePay API).</div>

        <label for="currency">Currency *</label>
        <select id="currency" name="currency" required>
            @foreach (['LKR', 'USD'] as $c)
                <option value="{{ $c }}" @selected(old('currency', 'LKR') === $c)>{{ $c }}</option>
            @endforeach
        </select>

        <label for="amount">Amount *</label>
        <input id="amount" name="amount" type="number" step="0.01" min="0.01" required value="{{ old('amount', '200.00') }}">

        <label for="first_name">First name *</label>
        <input id="first_name" name="first_name" required value="{{ old('first_name', 'John') }}">

        <label for="last_name">Last name *</label>
        <input id="last_name" name="last_name" required value="{{ old('last_name', 'Doe') }}">

        <label for="phone">Phone *</label>
        <input id="phone" name="phone" required value="{{ old('phone', '+94771234567') }}">

        <label for="email">Email *</label>
        <input id="email" name="email" type="email" required value="{{ old('email', 'john@example.com') }}">

        <label for="redirect_url">Transaction return URL (optional)</label>
        <input id="redirect_url" name="redirect_url" type="url" value="{{ old('redirect_url', $defaultReturnUrl) }}">
        <div class="hint">Default: this app’s return route. Must be reachable by OnePay’s servers.</div>

        <label for="additional_data">Additional data (optional)</label>
        <textarea id="additional_data" name="additional_data" placeholder="Any string for OnePay additionalData">{{ old('additional_data') }}</textarea>

        <label for="items">Item ids (optional, comma-separated)</label>
        <input id="items" name="items" value="{{ old('items') }}" placeholder="item_1, item_2">

        <button type="submit">Create link &amp; go to gateway</button>
    </form>
</body>
</html>
