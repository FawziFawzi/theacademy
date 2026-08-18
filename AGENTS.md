# AGENTS.md — The Academy

Multi-tenant course subscription SaaS (Laravel + Breeze + Blade). Single source of truth for the build: [BUILD_PLAN.md](BUILD_PLAN.md) — always state which numbered section you're working from.

## Workflow rules (mandatory)

1. Work **one task at a time** from the checklist below — never multiple tasks in one go, never "build the whole app".
2. **Before starting a task**, confirm the task number with the user (they may switch skills/agents per task).
3. **After finishing a task**: run verification (tests/lint/build), then **stop and ask the user** which task to do next so they can review.
4. **After every finished task**, update this file: mark the task complete below and record the date/status in the Progress log.
5. Never start a task without being asked. Never silently skip checklist items.

## Verification commands

- Tests: `composer test` (php artisan test)
- Lint: `vendor/bin/pint --test`
- Build assets: `npm run build`
- Reset DB with seed: `php artisan migrate:fresh --seed`

## Architecture rules (from BUILD_PLAN.md §1, §9)

- One organization per user; every query scoped by `organization_id` except `system_admin` — enforced in the Repository layer.
- Controllers thin (no business logic), Services orchestrate (no queries), Repositories do data access behind interfaces (no business rules), Models hold relationships/casts/scopes.
- Services type-hint repository interfaces; bind in `AppServiceProvider`.
- Seeders must call Services, not raw `Model::create()`, wherever side effects belong.

## Task checklist

### Phase 1 — Setup (§2)
- [x] **T1 — Project setup**: install `laravel/breeze` (blade), `npm install && npm run build`, require `barryvdh/laravel-dompdf`. Verify app boots. (Skip `stripe/stripe-php` — deferred to T13.)
- [x] **T2 — Enums (§3)**: all 8 backed enums in `app/Enums/` with `label()`: UserRole, OrganizationStatus, CourseStatus, BillingInterval, SubscriptionStatus, TransactionType, TransactionStatus, InvoiceStatus.

### Phase 2 — Domain foundation (§4, §5, §6)
- [x] **T3 — Migrations (§4)**: 9 migrations in FK order (organizations → users → courses → plans → plan_course → subscriptions → transactions → invoices → audit_logs), enum-backed string columns. Note: `users.role` defaults to `student` (Breeze registration creates users without a role).
- [x] **T4 — Models (§5)**: 8 models with relationships, enum `casts()`, tenancy scopes; enforce `organization_id` null ⇔ `system_admin` via model boot. Note: enforcement triggers on explicit `role` changes (`isDirty('role')`) — Breeze registration creates role-less users, so registered users keep DB-default `student` + null org until T9 wires org assignment into registration.
- [x] **T5 — Factories (§6)**: OrganizationFactory, UserFactory (+ `systemAdmin`/`orgAdmin`/`teacher`/`student` states), CourseFactory, PlanFactory, SubscriptionFactory (+ trialing/canceled/pastDue), TransactionFactory (+ failed), InvoiceFactory, AuditLogFactory.

### Phase 3 — Service + Repository layer (§9)
- [ ] **T6 — Repositories (§9)**: interfaces in `Repositories/Contracts/`, Eloquent impls in `Repositories/Eloquent/` for all 8 resources, DI bindings in `AppServiceProvider`. Repository enforces tenancy scoping.
- [ ] **T7 — Services (§9)**: thin Services in migration order (Organization → User → Course → Plan → Transaction → Invoice → AuditLog), then SubscriptionService with cross-service orchestration (creates transaction + audit log).

### Phase 4 — HTTP layer (§9.4)
- [ ] **T8 — Controllers + Form Requests + routes**: one thin controller + Form Request per resource, resource routes, tenancy-safe.

### Phase 5 — Auth & roles (§10)
- [ ] **T9 — Auth & roles**: custom `EnsureUserHasRole` middleware, role-guarded route groups, `/` root serves the login page.

### Phase 6 — Frontend (§11)
- [ ] **T10 — Theme & layout (§11)**: `theme.css` tokens, master `layouts/app.blade.php` (logo, nav, avatar dropdown, footer), wordmark + mountain-icon logos, dark login page as `/`.
- [ ] **T11 — Role views**: role-specific view folders (`admin/`, `org-admin/`, `teacher/`, `student/`) extending the master layout; status pills with leading dot; tabular-nums for numeric data.

### Phase 7 — Data (§7)
- [ ] **T12 — Seeders (§7)**: DatabaseSeeder orchestration via Services (system_admin, orgs, per-org users/courses/plans/subscriptions/transactions/invoices, audit logs); re-runnable via `migrate:fresh --seed`.

### Phase 8 — Payments & PDFs (§2)
- [ ] **T13 — PDF invoices**: dompdf invoice generation wired to completed transactions.
- [ ] **T14 — Stripe integration**: `stripe/stripe-php`, checkout + webhook with 3-attempt retry (deferred until after everything above is reviewed).

### Phase 9 — Verification (§12)
- [ ] **T15 — Tests**: feature tests through Services/Repositories (tenancy scoping, role guards, subscription lifecycle), `migrate:fresh --seed` sanity run, full suite green.

## Progress log

| Task | Status | Date |
|------|--------|------|
| T1 | complete | 2026-08-16 |
| T2 | complete | 2026-08-16 |
| T3 | complete | 2026-08-16 |
| T4 | complete | 2026-08-16 |
| T5 | complete | 2026-08-16 |
| T6–T15 | not started | — |