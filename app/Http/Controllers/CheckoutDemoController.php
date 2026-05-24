<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OnePay\Checkout\Exceptions\OnePayException;
use OnePay\Checkout\Services\OnePayService;

/**
 * Minimal UI to exercise onepay/laravel-checkout against the real API.
 */
class CheckoutDemoController extends Controller
{
    public function show(): View
    {
        return view('checkout-demo', [
            'defaultReturnUrl' => route('demo.payment.return'),
        ]);
    }

    public function submit(Request $request, OnePayService $onePay): RedirectResponse
    {
        $validated = $request->validate([
            'reference' => ['nullable', 'string', 'min:10', 'max:64'],
            'currency' => ['required', 'string', 'size:3'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'redirect_url' => ['nullable', 'url', 'max:2048'],
            'additional_data' => ['nullable', 'string', 'max:65535'],
            'items' => ['nullable', 'string', 'max:2000'],
        ]);

        $reference = $validated['reference'] ?? $onePay->generateReference('DEMO');
        $transactionRedirectUrl = $validated['redirect_url'] ?? route('demo.payment.return');

        $payload = [
            'reference' => $reference,
            'currency' => strtoupper($validated['currency']),
            'amount' => $validated['amount'],
            'customer_first_name' => $validated['first_name'],
            'customer_last_name' => $validated['last_name'],
            'customer_phone_number' => $validated['phone'],
            'customer_email' => $validated['email'],
            'transaction_redirect_url' => $transactionRedirectUrl,
        ];

        if (! empty($validated['additional_data'])) {
            $payload['additionalData'] = $validated['additional_data'];
        }

        $itemsRaw = trim((string) ($validated['items'] ?? ''));
        if ($itemsRaw !== '') {
            $payload['items'] = array_values(array_filter(array_map('trim', explode(',', $itemsRaw))));
        }

        try {
            $response = $onePay->createCheckoutLink($payload);
        } catch (OnePayException $e) {
            return back()
                ->withInput()
                ->withErrors(['onepay' => $e->getMessage()])
                ->with('onepay_context', $e->getContext());
        }

        if (! $response->succeeded() || empty($response->redirectUrl)) {
            return back()
                ->withInput()
                ->withErrors([
                    'onepay' => 'The gateway did not return a redirect URL.',
                ])
                ->with('gateway_response', $response->rawResponse);
        }

        return redirect()->away($response->redirectUrl);
    }

    public function paymentReturn(Request $request): View
    {
        return view('checkout-demo-return', [
            'query' => $request->query(),
        ]);
    }
}
