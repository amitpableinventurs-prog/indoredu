# IndorEdu

An online tutoring marketplace built with Laravel 13 (Blade + Tailwind + Alpine.js), MySQL/MariaDB, running under XAMPP.

Students can browse and book tutors, pay securely, message tutors, track attendance and progress, and leave reviews. Tutors manage their own profile, subjects, schedule, courses, and earnings. Admins get analytics, user management, tutor verification, and content moderation.

## Stack

- PHP 8.3, Laravel 13
- MySQL/MariaDB (via XAMPP)
- Blade + Tailwind CSS + Alpine.js (Laravel Breeze scaffolding)
- Stripe & PayPal (pluggable payment gateways, integrated via raw REST APIs — no SDK dependency)
- FullCalendar (CDN) for the calendar view
- Jitsi Meet links auto-generated for video sessions (no video infra to host)

## Local setup

1. Copy `.env` and adjust if needed. By default it points at a MySQL database named `tutor` on `127.0.0.1:3306` with user `root` / no password (XAMPP defaults). Create the database if it doesn't exist:
   ```
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS tutor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```
2. Install dependencies (already done in this environment):
   ```
   composer install
   npm install && npm run build
   ```
3. Run migrations and seed demo data:
   ```
   php artisan migrate
   php artisan db:seed
   php artisan storage:link
   ```
4. Serve the app:
   ```
   php artisan serve
   ```
   Visit http://127.0.0.1:8000

### Demo accounts (password: `Password123!`)

| Role    | Email                        |
|---------|-------------------------------|
| Admin   | admin@tutorhub.test           |
| Tutor   | alice.tutor@tutorhub.test     |
| Student | ethan.student@tutorhub.test   |

## Payments

Payments are pluggable — `App\Services\Payments\PaymentGateway` is the contract; `StripeGateway` and `PaypalGateway` implement it via raw HTTP calls (no vendor SDK). Add more gateways by implementing the interface and registering them in `App\Services\Payments\PaymentManager`.

To actually complete a checkout in Stripe/PayPal test mode, set these in `.env` (currently blank — payments will fail without them):

```
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...        # from `stripe listen` or your webhook endpoint config

PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=...
PAYPAL_CLIENT_SECRET=...
PAYPAL_WEBHOOK_ID=...                  # optional locally; webhook signature check is skipped if blank
```

Webhook endpoints: `POST /webhooks/stripe` and `POST /webhooks/paypal` (CSRF-exempt, signature-verified inside the controller).

## Background jobs

Two scheduled commands exist (`routes/console.php`):

- `bookings:send-reminders` — every 15 minutes, notifies students/tutors of sessions starting within the hour.
- `bookings:auto-complete` — hourly, marks confirmed bookings whose end time has passed as completed.

For these to actually fire, run the scheduler continuously in production:
```
* * * * * php artisan schedule:run >> /dev/null 2>&1
```
or, for local testing, `php artisan schedule:work`.

## Security & privacy notes

- Role-based access via `role` middleware (`App\Http\Middleware\EnsureUserHasRole`) — admin/tutor/student areas are fully separated, with policies (`App\Policies\*`) guarding bookings, courses, reviews, conversations, and tutor profiles.
- Passwords hashed (bcrypt), email verification required (`MustVerifyEmail`), rate limiting on login/register/booking/messaging/reporting endpoints, CSRF protection everywhere except signed webhook endpoints.
- Sensitive uploads (identity documents, certificates, message attachments) are stored on the private `local` disk and served only to authorized users through `SecureFileController`, never directly public.
- Guardian phone numbers are encrypted at rest (`encrypted` Eloquent cast).
- Users can export their data (`/privacy/export`) and delete their account (account is anonymized + soft-deleted, preserving other parties' booking/payment history integrity).
- `SecurityHeaders` middleware adds `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` to every response.

## Known scope trade-offs

- **Video calls**: auto-generated Jitsi Meet links per booking rather than embedded/custom WebRTC — zero extra infrastructure, works in any browser.
- **Messaging**: standard request/response chat with a lightweight conditional auto-refresh (every 15s, only when the compose box is empty) rather than full WebSocket real-time. Upgrading to true real-time would mean adding Laravel Reverb/Pusher broadcasting.
- **Receipts/invoices**: printable HTML pages (browser "Print to PDF") rather than a server-side PDF library, to avoid an extra dependency.
- **Charts**: dashboards use plain Tailwind CSS bar visualizations rather than a JS charting library.
