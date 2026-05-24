# OnePay Laravel demo app

This is a **Laravel 11** sample that uses the local package [`onepay/laravel-checkout`](../php-plugin) via a Composer **path repository** (`../php-plugin`).

## Run locally

1. Copy environment file and set keys:

   ```bash
   cd onepay-laravel-demo
   cp .env.example .env
   php artisan key:generate
   ```

2. Add your **OnePay** credentials to `.env`:

   - `ONEPAY_APP_ID`
   - `ONEPAY_APP_TOKEN`
   - `ONEPAY_HASH_SALT`

3. Set **`APP_URL`** to match how you run the app (e.g. `http://127.0.0.1:8000`) so `transaction_redirect_url` is valid for OnePay.

4. Start the server:

   ```bash
   php artisan serve
   ```

5. Open `/` in the browser, submit the form, and you should be redirected to the OnePay gateway when credentials and payload are valid.

## Routes

| Method | Path               | Purpose                    |
|--------|--------------------|----------------------------|
| GET    | `/`                | Checkout form              |
| POST   | `/checkout`        | Creates link, redirects    |
| GET    | `/payment/return`  | Demo return URL            |

## Use Packagist instead of the path repo

In `composer.json`, remove the `repositories` block and change the require line to:

```json
"onepay/laravel-checkout": "^2.0"
```

Then run `composer update onepay/laravel-checkout`.
