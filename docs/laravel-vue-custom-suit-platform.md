# Production-Ready Implementation Guide: Custom Suit Platform (Laravel + Vue)

## 1) Outcome & Scope
Build a production-ready made-to-measure suit platform with:
- A high-conversion step-based configurator.
- Measurement capture and fit-profile reuse.
- Cart, checkout, payment, and order tracking.
- Full admin panel for catalog, rules, pricing, and operations.
- Operational readiness (security, performance, observability, recovery).

> This document is implementation-focused and organized in execution order.

---

## 2) Target Architecture

### 2.1 Deployment pattern
Use a **modular monolith** first:
- Faster delivery than microservices.
- Strong transactional consistency for pricing + orders.
- Clear extraction path later for search/render/analytics.

### 2.2 Repository layout
```text
project/
  backend/               # Laravel API
  apps/
    storefront/          # Vue customer app
    admin/               # Vue admin app
  packages/
    ui/                  # Shared UI components/tokens
    sdk/                 # Generated TypeScript API client
  infra/
    docker/
    ci/
    runbooks/
  docs/
```

### 2.3 Core runtime components
- **Laravel API** (PHP 8.3+, Laravel 12+)
- **MySQL 8**
- **Redis** (cache + queue)
- **Queue workers** (notifications, webhooks, thumbnails, exports)
- **Object storage + CDN** for media
- **Vue 3 + TypeScript + Pinia** for both storefront and admin

---

## 3) Step-by-Step Delivery Plan (12 Weeks)

## Step 1 — Foundation & Environments (Week 1)
### Deliverables
- Monorepo scaffold and environment setup.
- Auth baseline (Sanctum) and RBAC baseline (`spatie/laravel-permission`).
- CI pipeline with lint + unit tests.

### Backend tasks
- Initialize Laravel API with `/api/v1` versioned routes.
- Setup Sanctum + login/register/password reset.
- Add roles: `super_admin`, `merchandiser`, `operations`, `support`, `analyst`.

### Frontend tasks
- Bootstrap `apps/storefront` and `apps/admin` with Vue 3 + TS.
- Setup shared theme tokens and primitive UI components.

### Exit criteria
- Storefront and admin can authenticate successfully.
- CI passes on pull requests.

## Step 2 — Catalog & Admin Catalog Module (Weeks 2–3)
### Deliverables
- Product/fabric data model.
- Admin catalog CRUD and media upload.
- Storefront fabric browse APIs with filters.

### Data model (minimum)
- `products`
- `fabrics`
- `product_options`
- `option_values`
- `fabric_media`

### Admin capabilities
- Fabric CRUD with draft/publish.
- Option/value CRUD.
- CSV import preview + validation.
- Image upload pipeline (thumbnail + zoom variants).

### Exit criteria
- Business team can manage entire catalog without developer help.

## Step 3 — Configurator Core + Rules + Pricing (Weeks 4–5)
### Deliverables
- Wizard configurator with persistent state.
- Compatibility rule engine.
- Live price preview service.

### UX contract (must-have)
- Clear progress and current step indicator.
- Instant preview and price updates after each selection.
- Back navigation preserves existing selections.
- Required-step validation before next action.
- Sticky summary on desktop; sticky CTA on mobile.

### APIs
- `GET /api/v1/fabrics`
- `GET /api/v1/products/{id}/options`
- `POST /api/v1/configurations/validate`
- `POST /api/v1/configurations/price-preview`
- `POST /api/v1/me/saved-configurations`

### Exit criteria
- User can complete full configuration flow from fabric to review.
- Price totals are deterministic and auditable.

## Step 4 — Cart, Checkout, Payments, Orders (Weeks 6–7)
### Deliverables
- Cart and checkout APIs.
- Payment integration (Stripe first).
- Order lifecycle + customer order timeline.

### APIs
- `POST /api/v1/cart/items`
- `PATCH /api/v1/cart/items/{id}`
- `POST /api/v1/checkout/intent`
- `POST /api/v1/orders`
- `GET /api/v1/orders/{number}`

### Reliability requirements
- Idempotency key required for order creation.
- Payment webhook signature verification mandatory.
- Duplicate webhook handling must be safe.

### Exit criteria
- Payment success/failure states reconcile correctly.
- No duplicate orders from retries.

## Step 5 — Measurements & Post-Purchase Operations (Weeks 8–9)
### Deliverables
- Measurement profile system.
- Fit preferences and reuse in checkout.
- Alteration/remake workflow.

### Data model (minimum)
- `measurement_profiles`
- `measurement_entries`
- `fit_preferences`
- `returns_or_alterations`

### Exit criteria
- Customer can save/reuse measurements.
- Operations/admin can resolve post-purchase fit issues.

## Step 6 — Hardening, Launch Readiness, Go-Live (Weeks 10–12)
### Deliverables
- Security hardening, observability, load testing.
- Backup/restore validation and incident runbooks.
- Staging signoff and production rollout plan.

### Exit criteria
- SLOs achieved in staging load test.
- Release checklist fully completed.

---

## 4) Detailed Admin Panel Blueprint

## 4.1 Modules
1. Dashboard
2. Catalog
3. Rule Builder
4. Pricing
5. Orders
6. Returns/Alterations
7. Users/Roles/Audit

## 4.2 Critical admin workflows
- **Catalog publish workflow:** draft -> reviewed -> published.
- **Rule testing:** submit sample payload, inspect allow/deny reasons.
- **Price governance:** scheduled changes and rollback history.
- **Operations board:** SLA columns (`new`, `measurement_review`, `in_production`, `qc`, `shipped`).

## 4.3 RBAC matrix (minimum)
- `super_admin`: full access
- `merchandiser`: catalog + pricing + rule builder
- `operations`: order statuses + production tracking
- `support`: order lookup + returns/alterations
- `analyst`: dashboard/read-only exports

---

## 5) API-First Contract Strategy
1. Publish OpenAPI spec before major frontend development.
2. Generate TypeScript SDK into `packages/sdk`.
3. Consume SDK in both storefront and admin.
4. Enforce schema with contract tests in backend CI.

### Example: price preview request
```json
{
  "product_id": "suit_001",
  "fabric_id": "fab_navy_italian",
  "options": {
    "lapel": "notch",
    "vents": "double",
    "pockets": "flap"
  },
  "market": "US",
  "currency": "USD"
}
```

### Example: price preview response
```json
{
  "base_price": 79900,
  "adjustments": [
    {"code": "fabric_tier_premium", "amount": 12000}
  ],
  "discounts": [],
  "total": 91900,
  "lead_time_days": 21,
  "valid_until": "2026-03-24T23:59:59Z"
}
```

---

## 6) Security & Compliance Baseline
- HTTPS + HSTS enforced.
- Laravel policies on all sensitive endpoints.
- Rate limits (auth, coupon, checkout, webhook endpoints).
- Admin mutation audit logs.
- Encryption at rest for sensitive profile fields.
- Privacy operations: data export + deletion workflow.

---

## 7) Performance & Reliability Targets

### Targets
- Configurator first useful paint: <2.5s on broadband.
- Read API p95: <300ms.
- Checkout API p95: <500ms.

### Methods
- Redis cache for read-heavy catalog/rules.
- CDN + image variants (WebP/AVIF).
- Queue offload for heavy work (exports, thumbnails, emails).
- DB indexing for common filters and order lookups.

---

## 8) CI/CD & Release Process

## 8.1 Pipeline stages
1. Lint + static analysis.
2. Unit/feature tests.
3. Build storefront/admin assets.
4. Security and dependency scan.
5. Deploy staging.
6. Smoke tests + approval gate.
7. Deploy production.

## 8.2 Zero-downtime migration rules
- Additive schema changes first.
- Deploy app code second.
- Remove deprecated fields later.

---

## 9) Launch Checklist
- [ ] OpenAPI published and versioned.
- [ ] RBAC matrix tested.
- [ ] Admin audit trail verified.
- [ ] Payment webhooks replay-tested.
- [ ] Backup restore drill passed.
- [ ] Load test report approved.
- [ ] Incident runbooks in `infra/runbooks`.

---

## 10) Recommended Next Artifacts (in order)
1. `docs/api/openapi-v1.yaml`
2. `backend/database/migrations/*` for core entities
3. `apps/storefront/src/modules/configurator/*`
4. `apps/admin/src/modules/catalog/*`
5. `infra/ci/*` pipeline definitions

If you want, the next step is to generate **artifact #1 (OpenAPI v1)** and then proceed sequentially.
