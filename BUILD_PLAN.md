# The Academy — Full Build Plan (Laravel + Breeze + Blade)

Single reference doc for the AI agent. Multi-tenant course subscription SaaS: **Organizations** (institutes) have **Teachers** and **Courses**; **Students** subscribe to **Plans** to access courses. Includes payment tracking, PDF invoices, and an audit trail.

No Filament — auth and all UI are built with Laravel Breeze + Blade, hand-written controllers/services/views.

---

## 1. Roles & Tenancy Model

- **Roles**: `system_admin` (platform owner, no organization), `org_admin` (manages one organization), `teacher`, `student`.
- **Tenancy (v1, simplified)**: one organization per user. `organization_id` lives directly on `users`, nullable only for `system_admin`. Every query for courses/plans/subscriptions must be scoped by the current user's `organization_id` (except `system_admin`, who sees all) — enforce this in the Repository layer, not just the UI.

---

## 2. Tech Stack & Setup Order

| Purpose | Package |
|---|---|
| Framework | Laravel (latest LTS) |
| Auth + starter UI | `laravel/breeze` (Blade stack) |
| PDF invoices | `barryvdh/laravel-dompdf` |
| Payments | `stripe/stripe-php` (raw SDK — avoid Laravel Cashier, it hides the webhook/subscription logic you're meant to learn) |
| Roles | plain `role` enum column on `users` (skip `spatie/laravel-permission` for v1) |
| Factories/seeding | built into Laravel |

Setup steps:
1. `laravel new the-academy`
2. `composer require laravel/breeze --dev` → `php artisan breeze:install blade` → `npm install && npm run build`
3. `composer require barryvdh/laravel-dompdf`
4. `composer require stripe/stripe-php` (only needed once you reach the Stripe integration phase, not at project start)

---

## 3. Enums

Native PHP 8.1+ backed enums in `app/Enums/`, each with a `label()` method for display in Blade views:

- `UserRole`: SystemAdmin, OrgAdmin, Teacher, Student
- `OrganizationStatus`: Active, Suspended
- `CourseStatus`: Draft, Published
- `BillingInterval`: Monthly, Yearly
- `SubscriptionStatus`: Trialing, Active, PastDue, Canceled
- `TransactionType`: SubscriptionPayment, Refund
- `TransactionStatus`: Pending, Completed, Failed
- `InvoiceStatus`: Paid, Pending, Failed

---

## 4. Migrations

Build in this order (respects FK dependencies):

1. `organizations`: id, name, address (nullable), email, phone (nullable), status (enum-backed string), timestamps
2. `users` (extend default): + role (enum-backed string), organization_id (nullable FK → organizations, `nullOnDelete`)
3. `courses`: id, organization_id (FK), teacher_id (FK → users), title, description (nullable text), status (enum-backed string), timestamps
4. `plans`: id, organization_id (FK), name, price (decimal 8,2), billing_interval (enum-backed string), timestamps
5. `plan_course` (pivot): plan_id (FK), course_id (FK) — composite PK, no separate id
6. `subscriptions`: id, user_id (FK), plan_id (FK), status (enum-backed string), current_period_start (date), current_period_end (date), timestamps
7. `transactions`: id, subscription_id (nullable FK), type (enum-backed string), amount (decimal 8,2), status (enum-backed string), timestamps
8. `invoices`: id, transaction_id (FK), pdf_path (nullable string), status (enum-backed string), timestamps
9. `audit_logs`: id, user_id (nullable FK), action (string), auditable_type (string), auditable_id (bigint), old_value (json, nullable), new_value (json, nullable), timestamps

**Enforce via model boot/observer (not just migration):** `organization_id` must be null when `role = system_admin`, non-null otherwise.

---

## 5. Models

In `app/Models/`, with correct relationships and enum `casts()`:

- **Organization**: hasMany users, courses, plans
- **User**: belongsTo organization; hasMany courses (as teacher, `teacher_id`); hasMany subscriptions; casts `role` → `UserRole`
- **Course**: belongsTo organization, belongsTo teacher (User); belongsToMany plans (via `plan_course`)
- **Plan**: belongsTo organization; belongsToMany courses; hasMany subscriptions
- **Subscription**: belongsTo user, belongsTo plan; hasMany transactions; casts `status` → `SubscriptionStatus`
- **Transaction**: belongsTo subscription; hasOne invoice; casts `type`/`status` to their enums
- **Invoice**: belongsTo transaction; casts `status` → `InvoiceStatus`
- **AuditLog**: belongsTo user; polymorphic `auditable()` via `morphTo`

---

## 6. Factories

One per model in `database/factories/`:

- **OrganizationFactory** — fake company name, address, email
- **UserFactory** — extend default; states `->systemAdmin()` (no org), `->orgAdmin()`, `->teacher()`, `->student()`, each setting `role` + `organization_id` correctly
- **CourseFactory** — random title/description, status = Published by default; needs `organization_id` + `teacher_id`
- **PlanFactory** — fake name, random price 10–100, random billing interval
- **SubscriptionFactory** — status = Active by default, period = now → +1 month; states `->trialing()`, `->canceled()`, `->pastDue()`
- **TransactionFactory** — type = SubscriptionPayment, status = Completed by default; state `->failed()`
- **InvoiceFactory** — status = Paid by default, `pdf_path` null (generated later, not faked)
- **AuditLogFactory** — random action string, fake old/new value JSON

---

## 7. Seeders

`DatabaseSeeder` orchestration:

1. Seed 1 `system_admin` (no organization)
2. Seed 2–3 `Organization` records
3. Per organization: 1 `org_admin`, 2–3 `teacher`s, 5–10 `student`s
4. Per teacher: 2–4 `courses`
5. Per organization: 2–3 `plans`, each attached to 2–3 random courses via pivot
6. Subset of students: `subscriptions` to plans in their own org, mostly active + a few canceled/past_due
7. Per active subscription: 1–3 completed `transactions`, each with 1 `invoice`
8. A handful of `audit_logs` referencing subscriptions/transactions above

Important: seeders should call **Services** (see section 9), not raw `Model::create()`, wherever seeding a record should trigger related side effects (e.g. seeding a subscription should go through `SubscriptionService::subscribe()` so it also creates its transaction + audit log) — this doubles as an end-to-end sanity check that the service layer works. Keep re-runnable via `migrate:fresh --seed`.

---

## 8. What NOT to build yet (v1 scope)

- Multi-organization-per-user (many-to-many tenancy)
- Prorated mid-cycle upgrades (note as "future improvement")
- Teacher payouts / ledger
- Stripe webhook retry beyond a basic 3-attempt retry
- Granular permissions package — role enum is enough

---

## 9. Backend Architecture — Service + Repository Layers, SOLID

### Layer responsibilities

| Layer | Responsibility | Must NOT do |
|---|---|---|
| Controller | Receive request, validate via Form Request, call a Service, return response/view | No business logic, no direct Eloquent queries |
| Service | Business logic/orchestration | No query building — asks a Repository |
| Repository | Data access only, behind an interface | No business rules |
| Model | Relationships, casts, accessors, scopes | No business logic |

Folder structure:
```
app/
  Http/Controllers/       (thin, one per resource)
  Http/Requests/          (Form Request validation)
  Services/                (SubscriptionService, TransactionService, AuditLogService, ...)
  Repositories/Contracts/  (interfaces)
  Repositories/Eloquent/   (concrete implementations)
  Models/
```

### SOLID, applied here

- **Single Responsibility** — one Service per entity's business logic; one Repository per model.
- **Open/Closed** — e.g. transaction-type handling via match/strategy so new types don't require editing existing methods.
- **Liskov Substitution** — any `XRepositoryInterface` implementation must be swappable without breaking the Service using it.
- **Interface Segregation** — small, resource-specific interfaces, not one giant generic repository interface.
- **Dependency Inversion** — Services type-hint interfaces, never concrete Eloquent classes; bind interface → implementation in `AppServiceProvider`:
```php
$this->app->bind(
    \App\Repositories\Contracts\SubscriptionRepositoryInterface::class,
    \App\Repositories\Eloquent\EloquentSubscriptionRepository::class
);
```

### Worked example (template for every resource)

```php
interface SubscriptionRepositoryInterface {
    public function find(int $id): ?Subscription;
    public function create(array $data): Subscription;
    public function updateStatus(Subscription $subscription, SubscriptionStatus $status): Subscription;
    public function activeForUser(int $userId): Collection;
}

class SubscriptionService {
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptions,
        private TransactionService $transactions,
        private AuditLogService $auditLog,
    ) {}

    public function subscribe(User $user, Plan $plan): Subscription {
        $subscription = $this->subscriptions->create([...]);
        $this->transactions->recordSubscriptionPayment($subscription, $plan->price);
        $this->auditLog->record($user, 'subscription.created', $subscription);
        return $subscription;
    }
}

class SubscriptionController {
    public function store(StoreSubscriptionRequest $request, SubscriptionService $service) {
        $subscription = $service->subscribe($request->user(), Plan::findOrFail($request->plan_id));
        return redirect()->route('subscriptions.show', $subscription);
    }
}
```

Replicate this exact pattern for every resource: Organization, Course, Plan, Subscription, Transaction, Invoice, AuditLog.

### Build order for this layer

1. Repository interface + Eloquent implementation for `Organization` first (no dependencies).
2. Its Service (thin wrapper at first).
3. Its Controller + Form Request + routes.
4. Repeat per resource in migration order: Organization → User → Course → Plan → Subscription → Transaction → Invoice → AuditLog.
5. Only after all exist, add cross-service orchestration (Subscription calling Transaction + AuditLog, as above).

### Don't over-engineer

No generic `BaseRepository<T>` with reflection magic, no CQRS/event sourcing, no extra validation library beyond Form Requests — this is a learning project, keep it explicit and readable.

---

## 10. Authentication (Breeze)

- Breeze provides login/register/password-reset out of the box on Laravel's default `web` session guard — no Sanctum/Passport needed (single server-rendered app, no separate API/mobile client yet).
- One shared login form for all roles — `role` column on `users` decides what they see after login, not a separate login flow per role.
- Route middleware `role:teacher` / `role:student` / etc. (custom `EnsureUserHasRole` middleware) guards each role's route group.
- Replace the default Laravel welcome page: set `/` to redirect to the login route, or point Breeze's login route at `/` directly, so login is the first thing anyone sees.

---

## 11. Frontend

### Brand
- Name: **The Academy**. Tagline: "Learn The Way".
- Two logo marks: a wordmark (green "Th" + dark "eAcademy" text) for the login page and light contexts, and a mountain icon mark for the nav bar and favicon/compact contexts. Never recolor either — place on a background it already contrasts against.

### Color palette (dark theme — current direction)

| Token | Hex | Usage |
|---|---|---|
| `--color-bg` | `#0E1614` | Page background |
| `--color-surface` | `#161F1D` | Cards, nav bar, table rows |
| `--color-surface-raised` | `#1D2926` | Modals, dropdowns |
| `--color-accent` | `#2FBFA8` | Primary buttons, links, active nav |
| `--color-accent-hover` | `#3FD6BD` | Hover state |
| `--color-accent-soft` | `#173B35` | Badge backgrounds, subtle fills |
| `--color-text` | `#EAF2F0` | Primary text |
| `--color-text-muted` | `#8FA39E` | Secondary text, captions |
| `--color-border` | `#263330` | Dividers, input borders |
| `--color-danger` | `#E5695E` | Failed/past_due status |
| `--color-warning` | `#E0B23D` | Pending/trialing status |

Note: test the wordmark's dark-gray "eAcademy" text against `--color-surface` in the nav — if contrast is weak, use the mountain-icon mark there instead and reserve the wordmark for the login page.

### Typography
- Headings: **Manrope**, 600–700 weight.
- Body/UI: **Inter**, 400–500 weight.
- Numeric data (prices, dates in tables): Inter with `font-variant-numeric: tabular-nums`.

| Role | Size | Weight |
|---|---|---|
| Page title | 1.5rem | 700 |
| Section heading | 1.125rem | 600 |
| Body | 0.9375rem | 400 |
| Caption/label | 0.8125rem | 500, muted color |

### Shape & elevation
- Pill shape (`border-radius: 999px`) reserved for **buttons and status badges** only.
- Cards, modals, inputs, dropdowns: `border-radius: 12px`.
- Cards use a soft shadow instead of a hard border to stay light against the background.
- Buttons: solid accent fill + light text for primary actions; accent-soft fill + accent text for secondary; plain text + accent color for tertiary. All pill-shaped, `padding: 0.625rem 1.25rem`.

### Layout — top navigation
```
┌──────────────────────────────────────────────────────────┐
│  [Logo]              Courses  Plans  Students   [●] Fawzi ▾│
└──────────────────────────────────────────────────────────┘
```
- Logo: top-left (mountain-icon mark), links to dashboard home.
- Nav links: muted by default, accent color + small underline when active.
- Username: top-right, circular avatar (initials on accent-soft background if no photo) + dropdown for account/logout.
- Footer: minimal — © The Academy, tagline, muted text, surface background.

### Signature element
Status pills (subscription/invoice/transaction status) get a small leading dot (●) in the status color before the label — not just a colored background. This is the one recurring, deliberate visual detail, and it reinforces the product's actual conceptual core: tracking state changes over time (audit log, subscription lifecycle, payment status).

### Motion
Minimal and functional only: 120ms color transition on hover; a brief 300ms background flash (using `--color-warning`, fading to resting state) when a status changes, so state changes get noticed once; no decorative page-load or scroll animations.

### Accessibility floor
- Visible keyboard focus: `2px solid --color-accent` outline, 2px offset.
- Text contrast meets WCAG AA at all sizes used.
- Status is never color-only — always dot + color + text label together.

### Build instructions
1. Base: Breeze's default Blade scaffolding.
2. One master layout (`resources/views/layouts/app.blade.php`): header (logo left, nav, username right) using `--color-surface` + bottom hairline border; `@yield('content')` / slot for page body; minimal footer.
3. All CSS tokens as custom properties in one file (`resources/css/theme.css`), imported once — never hardcode hex values inline.
4. Role-specific view folders (`admin/`, `org-admin/`, `teacher/`, `student/`) all extend the one master layout — only the content slot changes per role.
5. Login page: same dark theme, wordmark logo centered, otherwise empty `--color-bg` page — this is also the site's root (`/`) route.

---

## 12. Session Checklist (for every AI agent session)

- State which numbered section you're working from.
- Ask for **one resource or feature at a time**, not "build the whole app."
- Have the agent stop after that piece so you can review before continuing.

## Your own review checklist

- [ ] Did I write/check the schema or business rule myself before the AI touched it?
- [ ] Can I explain what every accepted file does, out loud, without looking?
- [ ] Did I write the tests myself, or at least read and understand them?
- [ ] For the trickiest logic in this piece, did I try it myself first?
