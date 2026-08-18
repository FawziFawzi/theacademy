# The Academy — "Learn The Way"

Multi-tenant course subscription SaaS built with **Laravel** (latest) + **Breeze** (Blade stack). Organizations (institutes) have teachers and courses; students subscribe to plans to access courses. Includes payment tracking, PDF invoices, and an audit trail.

No Filament — auth and UI are hand-built with Breeze + Blade, controllers/services/views written by hand.

## Tech stack

| Purpose | Package |
|---|---|
| Framework | Laravel (latest LTS) |
| Auth + starter UI | `laravel/breeze` (Blade stack) |
| PDF invoices | `barryvdh/laravel-dompdf` |
| Payments | `stripe/stripe-php` (planned — raw SDK, no Cashier) |
| Roles | plain `role` enum column on `users` |
| Factories / seeding | built into Laravel |

## Roles & tenancy

- **Roles**: `system_admin` (platform owner, no organization), `org_admin` (manages one organization), `teacher`, `student`.
- **Tenancy (v1)**: one organization per user. `organization_id` lives on `users` (nullable only for `system_admin`). Every query for courses/plans/subscriptions is scoped by the current user's organization — enforced in the **Repository layer** (`app/Repositories/TenantContext`), not just the UI.

## Setup

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed   # seeds via Services once T12 is done
php artisan serve
```

## Architecture

```
app/
  Enums/                    # 8 backed enums with label() for Blade display
  Http/Controllers/         # thin controllers (no business logic)
  Http/Requests/            # Form Request validation
  Models/                   # relationships, casts, tenancy boot enforcement
  Repositories/Contracts/   # repository interfaces (resource-specific)
  Repositories/Eloquent/    # Eloquent implementations (data access only)
  Repositories/TenantContext # resolves current org; applies tenancy scoping
  Services/                 # business logic / orchestration
  Services/Contracts/       # service interfaces (one per service)
```

Layer rules (SOLID):

| Layer | Responsibility | Must NOT do |
|---|---|---|
| Controller | Validate, call a Service, return response/view | No business logic, no Eloquent queries |
| Service | Business logic / orchestration | No query building — asks a Repository |
| Repository | Data access behind an interface | No business rules |
| Model | Relationships, casts, accessors, scopes | No business logic |

Dependency Inversion at both layers: controllers type-hint **service interfaces**, services type-hint **repository interfaces** (and service interfaces for cross-service deps). Interface → implementation bindings for both layers live in `AppServiceProvider`.

## Verification

```bash
composer test        # php artisan test
vendor/bin/pint --test
npm run build
php artisan migrate:fresh --seed
```

