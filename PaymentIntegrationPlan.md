# T14 Plan — Gateway-Agnostic Payment Integration Revision

## Working from Build Plan Section

This plan works from **BUILD_PLAN.md §14 — Session Checklist**, specifically:

- **T14 — Stripe integration**
- Revised to implement a **gateway-agnostic payment abstraction**
- Stripe remains the only concrete payment gateway implementation for now

No code should be executed while preparing this plan.

---

## Goal

Refactor the existing Stripe-specific payment implementation into a gateway-neutral architecture.

The application should depend on a generic payment contract:
