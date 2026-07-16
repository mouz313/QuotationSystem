# QuotationSystem — Complete Documentation

> A full-featured, multi-tenant SaaS Quotation Management System with Client Portal, Payment Verification, Real-Time Notifications, and Role-Based Access Control.

---

## Table of Contents

- [Overview](#overview)
- [What Makes This Different](#what-makes-this-different)
- [Key Features](#key-features)
- [Architecture & Tech Stack](#architecture--tech-stack)
- [Authentication & Authorization](#authentication--authorization)
- [Quotation Workflow](#quotation-workflow)
- [Payment System](#payment-system)
- [Client Portal](#client-portal)
- [Email & Notification System](#email--notification-system)
- [Admin Panel](#admin-panel)
- [Company Panel](#company-panel)
- [API Reference](#api-reference)
- [Security Features](#security-features)
- [Database Schema](#database-schema)
- [Export & Reporting](#export--reporting)
- [Settings & Configuration](#settings--configuration)
- [Setup & Installation](#setup--installation)
- [Default Credentials](#default-credentials)

---

## Overview

QuotationSystem is a complete SaaS platform that enables companies to create, send, track, and manage quotations with their clients. It features a dual-panel architecture — a **Company Panel** for businesses and a **Client Portal** for customers — connected by a full quotation lifecycle with payment verification.

### The Problem It Solves

Most quotation tools are single-user, single-company. They lack:
- Multi-company tenant isolation
- Client-facing portals for acceptance/decline/payment
- Payment proof submission and verification workflows
- Real-time status tracking across both parties
- Granular admin oversight with role-based permissions
- Subscription-based package limits

QuotationSystem solves all of these in a single, self-hosted application.

---

## What Makes This Different

### 1. True Multi-Tenant Architecture
Unlike single-user quoting tools, this system supports **multiple companies** on a single installation, each with their own users, clients, items, and quotations. Companies are completely isolated — one company cannot see or access another company's data.

### 2. Dual-Panel Client Portal
Clients don't just receive a PDF via email. They get a **full portal account** where they can:
- View all quotations across multiple companies they work with
- Accept, decline, or request changes — with full status tracking
- Submit payment proofs directly through the portal
- Track payment approval status
- Manage their own profile

**No other quoting tool offers this level of client engagement.**

### 3. Payment Verification Workflow
Most tools stop at "quote sent." This system has a **complete payment cycle**:
- Client submits payment with proof of transfer (screenshot/PDF)
- Company reviews, approves, or rejects with reason
- Approved payments auto-update the quotation's payment status
- Cumulative payment tracking (partial payments supported)
- Full audit trail of every payment action

### 4. Modify-In-Place with Revision History
When a client requests changes, the company **amends the existing quotation** rather than creating a new one. The previous version is automatically saved as a revision. This means:
- No duplicate quotation numbers
- Complete history of every change
- Quote number stays the same throughout the lifecycle

### 5. Smart Quotation Numbering
Auto-generated quote numbers follow the format `QT-YYYYMMDD-NNNN` with database-level locking (`lockForUpdate()`) to prevent collisions, even under concurrent requests.

### 6. Package-Based Resource Limits
Companies are limited by their subscription package:
- Maximum users, clients, and quotations per package
- Automatic enforcement on creation
- Package assignment by super admin
- Visual usage indicators in company settings

### 7. Three-Tier Role System with Granular Permissions
- **Super Admin** — Full platform access
- **Company Admin** — Manages their company's team and settings
- **Staff** — Creates quotations, manages clients
- **Custom Admin Roles** — Granular permissions (12 configurable permissions)

### 8. Real-Time + Persistent Notifications
- **In-app notifications** stored in the database with read/unread tracking
- **Email notifications** on every status change, payment submission, and payment review
- **WebSocket broadcasting** via Pusher for real-time updates
- Notifications work even without Pusher configured (DB fallback)

### 9. Self-Hosted with Full Control
No SaaS dependency. The entire system runs on your own server. You control the data, the branding, the pricing, and the infrastructure.

---

## Key Features

### Core Features
| Feature | Description |
|---------|-------------|
| Multi-Company Support | Complete tenant isolation with shared infrastructure |
| Quotation Management | Full CRUD with line items, taxes, discounts, terms & conditions |
| Milestone Quotations | Type-based quotations with per-item date ranges, progress tracking, and milestone payments |
| Status Workflow | 6-stage lifecycle: draft → sent → opened → change_requested → accepted/declined |
| Payment Verification | Submit proof → review → approve/reject → auto-update totals |
| Per-Milestone Payments | Clients can pay against individual milestones, not just the full quotation |
| Client Portal | Dedicated login for clients to view quotations and submit payments |
| Email Notifications | Automatic branded emails on every quotation and payment action |
| Company-Branded Emails | All emails show company name, logo, and brand color |
| In-App Notifications | Persistent notification bell with unread count and mark-all-read |
| Real-Time Updates | WebSocket broadcasting via Pusher for live status changes |
| PDF Generation | Professional quotation PDFs with DejaVu Sans font (full Unicode currency support) |
| Currency-Aware Display | All amounts show correct currency symbol (₨, ₹, €, £, etc.), never hardcoded |
| Account Details | Company bank/payment info shown on PDFs and quotations |
| CSV Export | Export clients, quotations, and reports |
| Activity Logging | Full audit trail of all actions with user, timestamp, and description |
| Search & Filter | Search across clients, items, quotations with status/date filters |
| Responsive Design | Mobile-friendly sidebar with hamburger menu |
| Inline Validation | Real-time form validation with error messages |
| Soft Deletes | Deleted data is recoverable (7 core tables) |

### Admin Features
| Feature | Description |
|---------|-------------|
| Dashboard | Platform-wide stats, charts, top companies, recent activity |
| Company Management | CRUD, status control (active/inactive/blocked), package assignment |
| Package Management | Define subscription tiers with resource limits |
| Currency & Tax Management | Multi-currency support with configurable tax rates |
| Quotation Oversight | View all quotations across all companies |
| Reports & Exports | Platform analytics with CSV/PDF export |
| System Health | PHP version, DB status, storage, memory, all table counts |
| Pages CMS | Create and publish public pages (Terms, Privacy, About) |
| Email Templates | Editable email templates with placeholder support |
| Settings | App name, social links, Pusher config, SMTP email config |

### Company Features
| Feature | Description |
|---------|-------------|
| Dashboard | Company stats with per-currency totals, recent quotations, monthly charts |
| Client Management | CRUD with search and CSV export |
| Item Catalog | Reusable items for quick quotation building |
| Quotation Builder | Line items, tax, discount, terms, payment instructions, milestone support |
| Milestone Quotations | Create quotations with per-item date ranges, sort order, and progress tracking |
| Email Sending | Send quotation to client with auto-portal-account creation |
| PDF Download | Download branded PDF with DejaVu Sans font (Unicode currency symbols) |
| Clone Quotation | Duplicate an existing quotation as a new draft |
| Payment Management | Approve/reject client payments with auto-total calculation |
| Internal Notes | Add private notes to quotations |
| Team Management | Add/edit/remove team members (admin only) |
| Company Settings | Branding (logo, color, font), default terms, account details |

### Client Features
| Feature | Description |
|---------|-------------|
| Multi-Company View | See quotations from all associated companies in one place |
| Dashboard | Gradient hero header, stat cards, action-required alerts, quotation table with sidebar |
| Quotation View | Horizontal timeline, milestone progress bars, per-milestone payment submission |
| Quotation Actions | Accept, decline, or request changes with notes |
| Payment Submission | Submit payment amount with proof file (image/PDF) against full quote or specific milestone |
| Profile Management | Update name, email, phone, password |
| Password Reset | Self-service password reset via email |
| Status Tracking | Visual timeline of all status changes |

---

## Architecture & Tech Stack

### Backend
| Component | Technology |
|-----------|-----------|
| Language | PHP 8.3+ |
| Framework | Laravel 13 |
| Database | MySQL (SQLite compatible) |
| Authentication | Session-based (web) + Sanctum (API) |
| PDF Generation | barryvdh/laravel-dompdf |
| Real-Time | Pusher WebSocket Broadcasting |
| Session/Cache/Queue | Database driver |

### Frontend
| Component | Technology |
|-----------|-----------|
| CSS Framework | Tailwind CSS 4 |
| Build Tool | Vite 8 |
| JavaScript | Alpine.js (via Vite) |
| Real-Time Client | Laravel Echo + Pusher.js |

### System Architecture
```
┌──────────────────────────────────────────────────────┐
│                    ADMIN PANEL                        │
│              /admin/* (super_admin)                   │
│   Companies · Packages · Users · Reports · Settings  │
└──────────────────────┬───────────────────────────────┘
                       │
┌──────────────────────┴───────────────────────────────┐
│                  COMPANY PANEL                        │
│              /* (company users)                       │
│   Clients · Items · Quotations · Payments · Team     │
└──────────────────────┬───────────────────────────────┘
                       │
          ┌────────────┴────────────┐
          │                         │
┌─────────┴──────────┐  ┌──────────┴──────────┐
│   CLIENT PORTAL    │  │     REST API         │
│  /client/*         │  │  /api/v1/*           │
│  View · Accept     │  │  Sanctum Auth        │
│  Pay · Profile     │  │  Company User Mgmt   │
└────────────────────┘  └─────────────────────┘
```

### Directory Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          (14 controllers)
│   │   ├── Company/        (6 controllers)
│   │   ├── Client/         (3 + 2 auth controllers)
│   │   └── Root            (Auth, Profile, Settings, API Quotation)
│   └── Middleware/          (5 custom middleware)
├── Mail/                   (6 mailables)
├── Models/                 (20 models)
├── Services/
│   └── QuotationCalculator (extracted calculation logic)
└── Providers/              (AppServiceProvider — SMTP config, rate limiters)
database/
├── migrations/             (27 migration files)
└── seeders/                (Demo data seeder)
resources/
├── views/
│   ├── admin/              (Admin panel views)
│   ├── company/            (Company panel views)
│   ├── client/             (Client portal views)
│   ├── layouts/            (app, admin, guest, client layouts)
│   ├── components/         (Reusable Blade components: alert, quotation-status-badge)
│   ├── emails/             (5 branded email templates)
│   └── settings/           (Profile settings views)
```

---

## Authentication & Authorization

### Auth Guards
The system uses **3 separate authentication guards** for complete isolation:

| Guard | Model | Purpose |
|-------|-------|---------|
| `web` (default) | `User` | Company users and super admins |
| `client` | `ClientUser` | Client portal users |
| `sanctum` | `User` | API token authentication |

This means a company user and a client user can have the **same email address** without conflicts — they exist in different tables with separate session stores.

### Role Hierarchy
```
super_admin
    └── Can access: Admin Panel + Company Panel (read-only)
    └── Bypasses: All permission checks
    └── Cannot be: Deleted by other admins

company_admin
    └── Can access: Company Panel (full)
    └── Can manage: Team members, company settings
    └── Cannot access: Admin Panel

staff
    └── Can access: Company Panel (limited)
    └── Can do: Create quotations, manage clients/items
    └── Cannot do: Manage team, change company settings
```

### Admin Role Permissions
Super admins can create custom roles with granular permissions:

| Permission | Grants Access To |
|-----------|-----------------|
| `companies.manage` | Company CRUD, status control, package assignment |
| `packages.manage` | Package CRUD |
| `currencies.manage` | Currency CRUD, default setting |
| `taxes.manage` | Tax CRUD, default setting |
| `settings.manage` | System settings (general, social, pusher, email) |
| `quotations.view` | View all quotations across companies |
| `reports.view` | Reports & Exports page |
| `users.manage` | Admin user CRUD |
| `activity.view` | Activity log viewer |
| `health.view` | System health dashboard |
| `pages.manage` | CMS page management |
| `email_templates.manage` | Email template editor |

### Middleware Protection

| Route Group | Middleware Stack |
|------------|-----------------|
| Admin Panel | `auth` → `admin` → `permission:{perm}` |
| Company Panel | `auth` → `company.active` |
| Company Admin Area | `auth` → `company.active` → `not.admin` → `company.admin` |
| Client Portal | `auth:client` |
| Login Pages | `guest` |
| Company Login POST | `throttle:5,1` |
| Client Login POST | `throttle:5,1` |
| Client Forgot Password | `throttle:5,1` |
| API | `auth:sanctum` → `throttle:api` (60/min) |

---

## Quotation Workflow

### Quotation Types

The system supports two quotation types:

| Type | Description |
|------|-------------|
| `standard` | Traditional quotation with line items and a single grand total |
| `milestone` | Quotation with per-item date ranges, sort order, and individual payment tracking |

**Milestone quotations** add:
- `start_date` and `end_date` on each quotation item (date range per milestone)
- `sort_order` for milestone sequencing
- Progress bar showing completed milestones (paid items)
- Per-milestone payment submission (clients pay against a specific item)
- Visual milestone progress on both company and client views

### Status Lifecycle
```
                    ┌──────────────────────────────────┐
                    │                                  │
                    v                                  │
    ┌─────────┐  send  ┌─────────┐  view  ┌─────────┐ │
    │  DRAFT  │───────▶│  SENT   │──────▶│ OPENED  │ │
    └─────────┘        └─────────┘       └─────────┘ │
                    │              │           │       │
                    │              │           │       │
                    │              ▼           ▼       │
                    │     ┌────────────┐  ┌────────┐  │
                    │     │  DECLINED  │  │ACCEPTED│  │
                    │     └────────────┘  └────────┘  │
                    │                                  │
                    │     ┌──────────────────┐         │
                    └────▶│ CHANGE_REQUESTED  │────────┘
                          └──────────────────┘
                          (amend & re-send → back to SENT)
```

### Valid Transitions

| Current Status | Company Can Set | Client Can Set |
|---------------|----------------|----------------|
| `draft` | → sent | — |
| `sent` | → accepted, declined | → opened (auto), accepted, declined, change_requested |
| `opened` | → accepted, declined | → accepted, declined, change_requested |
| `change_requested` | → sent (after amend) | → accepted, declined |
| `accepted` | — | — |
| `declined` | — | — |

### What Happens at Each Stage

1. **Draft** — Quotation created, not yet sent. Can be edited, cloned, or deleted.
2. **Sent** — Email sent to client. PDF attached. Client portal account auto-created if new.
3. **Opened** — Client viewed the quotation. `viewed_at` timestamp set. Company notified.
4. **Change Requested** — Client submitted change notes. Company can amend and re-send.
5. **Accepted** — Client accepted the quotation. Ready for payment.
6. **Declined** — Client declined. Optional reason recorded.

### Every Status Change Triggers:
- Entry in `quotation_status_logs` (who, when, from/to)
- Email notification to the other party
- In-app notification
- Pusher broadcast event (if configured)

---

## Payment System

### Complete Payment Flow

```
Client                          Company
  │                               │
  │  1. Submit Payment            │
  │  (amount + proof file)        │
  │  (optional: milestone item)   │
  │──────────────────────────────▶│
  │                               │
  │           2. Review Payment   │
  │           (view proof)        │
  │                               │
  │     ┌───────────┴───────────┐ │
  │     │                       │ │
  │     ▼                       ▼ │
  │  APPROVE                 REJECT│
  │  (auto-update            (with │
  │   quotation               reason)│
  │   payment_status)          │   │
  │     │                       │   │
  │◀────┘                       │◀──┘
  │                               │
  │  3. Email notification        │
  │  + in-app notification        │
```

### Payment States
| State | Description |
|-------|-------------|
| `pending` | Client submitted, awaiting company review |
| `approved` | Company approved. Auto-updates quotation payment_status |
| `rejected` | Company rejected. Reason stored in notes |

### Milestone Payments
For milestone quotations, clients can submit payments against a specific milestone (quotation item):
- `quotation_item_id` links the payment to a specific milestone
- When approved, that item is marked as paid (`is_paid` computed attribute)
- The quotation's `paid_amount` reflects only approved milestone payments
- Progress bar shows X/Y milestones paid

### Auto-Calculation Logic
When a payment is approved:
1. Sum all approved payments for the quotation
2. If total >= grand_total → quotation payment_status = `paid`
3. If total > 0 but < grand_total → quotation payment_status = `partial`
4. Update `paid_amount` and `paid_at` on the quotation

### Payment Proof
- Supported formats: JPG, PNG, PDF
- Max size: 5MB
- Stored in: `storage/app/public/payment-proofs/`
- Visible to company users on the quotation detail page

---

## Client Portal

### Portal Account Creation
Client portal accounts are **automatically created** when a company sends a quotation to a client's email for the first time:
1. System checks if a `ClientUser` with that email exists
2. If not, creates one with a random 12-character temporary password
3. Sends welcome email with temp password and login link
4. Links the client to the company via the `client_company` pivot table
5. Client logs in and sets their own password

### Multi-Company Support
A single client can work with **multiple companies**. The portal shows all quotations from all associated companies in one dashboard. The client's companies are linked via the `client_company` pivot table.

### Client Dashboard
- Gradient hero header with summary stats
- 5 stat cards: total quotations, accepted, pending payments, total paid, action required
- Action-required alerts (pending payments, change requests)
- Quotation table with status badges, currency-aware amounts, and quick actions
- Sidebar with recent quotations and quick links

### Quotation Detail View
- Hero header with quote number, status, and total
- Horizontal status timeline (draft → sent → opened → accepted)
- 3-column layout: details, terms, payment
- Milestone progress bar (for milestone quotations)
- Per-milestone payment submission
- Accept / Decline / Request Change buttons
- Status change notes input

### Client Actions
| Action | Available When | Effect |
|--------|---------------|--------|
| View Quotation | Any status | Full details, items, terms, status timeline |
| Accept | sent, opened, change_requested | Status → accepted |
| Decline | sent, opened, change_requested | Status → declined (optional reason) |
| Request Change | sent, opened, change_requested | Status → change_requested (notes required) |
| Submit Payment | sent, opened, accepted | Creates pending payment record |

---

## Email & Notification System

### Email Branding
All email templates are company-branded:
- **Header**: Company logo (if set) + company name + gradient using `brand_color`
- **Buttons**: Styled with the company's `brand_color`
- **Footer**: Company name (not `config('app.name')`)
- **Fallback**: Defaults to indigo (#4f46e5) if no brand color set

### Email Mailables (6 Types)

| Mailable | Sent When | Recipient | Content |
|----------|----------|-----------|---------|
| `ClientWelcomeMail` | First quotation to new client | Client | Temp password, login link, company branding |
| `SendQuotationMail` | Company sends quotation | Client | Quotation details + PDF attachment |
| `QuotationStatusMail` | Any status change | Company or Client | Old/new status, who changed it |
| `ClientPasswordResetMail` | Client forgot password | Client | Password reset link |
| `PaymentSubmittedMail` | Client submits payment | Company user | Payment amount, link to review |
| `PaymentReviewedMail` | Company approves/rejects | Client | Payment status, company notes |

### Email Templates (5 Branded Templates)
| Template | Gradient Color | Content |
|----------|---------------|---------|
| `send-quotation.blade.php` | Company brand color | Quote number, grand total, portal link |
| `quotation-status.blade.php` | Company brand color | Status badges, notes, action button |
| `payment-submitted.blade.php` | Company brand color | Payment amount, client details, review link |
| `payment-reviewed.blade.php` | Company brand color | Payment status, balance summary |
| `client-welcome.blade.php` | Company brand color | Login credentials, portal link |

### In-App Notifications
Stored in the `notifications` table with read/unread tracking:

| Type | Trigger | Shows |
|------|---------|-------|
| `payment_submitted` | Client submits payment | "{Client} submitted ${amount} for {quote}" |
| `payment_approved` | Company approves payment | "Payment of ${amount} approved for {quote}" |
| `payment_rejected` | Company rejects payment | "Payment of ${amount} rejected for {quote}" |
| `status_changed` | Quotation status changes | "Quotation {quote} status changed to {status}" |

Features:
- Unread count badge in navigation
- Mark all as read button
- Click-through to relevant quotation
- Works without Pusher (database-backed)

### Real-Time Broadcasting (Pusher)
When Pusher is configured:
- Status changes broadcast to company channel
- Package assignments broadcast to company channel
- Company status changes broadcast to admin channel

---

## Admin Panel

### Dashboard Analytics
- Total / Active companies
- Total quotations / Accepted quotations
- Total revenue / Monthly revenue
- Total users / Conversion rate
- 12-month revenue chart
- Quotation status breakdown chart
- Top companies by quotation value
- Company growth chart (12 months)
- Recent companies and activity log

### Company Management
- Create, view, edit, delete companies
- Set status: active, inactive, blocked
- Assign subscription packages (auto-cancels previous)
- View company details with user count, package info
- Blocked companies auto-logout all users

### Subscription Packages
Define tiers like:
| Package | Price | Users | Clients | Quotations |
|---------|-------|-------|---------|------------|
| Starter | $9.99/mo | 2 | 25 | 100 |
| Professional | $29.99/mo | 5 | 100 | 500 |
| Enterprise | $79.99/mo | 20 | Unlimited | Unlimited |

### Reports & Exports
- Companies report (CSV/PDF)
- Quotations report with date filtering (CSV/PDF)
- Revenue report by month (CSV/PDF)

### System Health Dashboard
- PHP version, Laravel version, OS
- Database connection status
- All table row counts
- Storage usage (total, logs)
- Memory limit, max upload size
- Pusher configuration status
- Cache connectivity test

---

## Company Panel

### Quotation Builder
1. Select type: **standard** or **milestone**
2. Select client, currency, tax rate
3. Set issue date and optional expiry date
4. Add line items (title, description, quantity, unit price)
5. For milestones: set start_date, end_date per item (auto-computed duration)
6. Apply discount amount and tax percentage
7. Add terms & conditions (default from company settings)
8. Add payment instructions (quotation-specific) and account details (company-wide)
9. Auto-calculated totals via `QuotationCalculator` service

### Quotation Actions
- **Preview** — View before sending
- **Send Email** — Sends to client, auto-creates portal account
- **Download PDF** — DomPDF with company branding
- **Clone** — Duplicate as new draft (QT-XXXX-COPY)
- **Amend** — Edit when client requested changes
- **Add Note** — Internal notes (not visible to client)

### Item Catalog
Reusable items for fast quotation building:
- Title, description, unit price
- Searchable by title or description
- Default currency symbol displayed

### Team Management (Admin Only)
- Add/edit/remove company users
- Assign roles: company_admin or staff
- Package limit enforcement
- Self-deletion protection

### Company Settings
- Company name, email, phone, address, website
- Default terms & conditions (pre-filled on new quotations)
- Logo upload (displayed on PDFs and emails)
- Brand color and font (applied to PDFs and emails)
- Account Details: bank name, account number, routing number, SWIFT/BIC, payment methods, notes (shown on PDFs)
- Package usage stats and limits

---

## API Reference

### Authentication
```
POST /api/v1/register    — Create account (throttled)
POST /api/v1/login       — Get Sanctum token (throttled)
POST /api/v1/logout      — Revoke token (auth required)
GET  /api/v1/me          — Current user info (auth required)
```

### Company User Management
```
GET    /api/v1/company/users          — List users
POST   /api/v1/company/users          — Create user
GET    /api/v1/company/users/{id}     — Show user
PUT    /api/v1/company/users/{id}     — Update user
DELETE /api/v1/company/users/{id}     — Delete user
```

### Quotation (API)
```
POST /api/v1/quotations    — Create quotation (company users only)
```

### Tax (API)
```
GET  /api/v1/taxes    — List active taxes
POST /api/v1/taxes    — Create tax
```

### Admin API
```
GET    /api/v1/admin/companies                   — List companies
GET    /api/v1/admin/companies/{company}         — Show company
PATCH  /api/v1/admin/companies/{company}/status  — Update status
POST   /api/v1/admin/companies/{company}/assign-package
DELETE /api/v1/admin/companies/{company}         — Delete company
apiResource /api/v1/admin/packages               — Full CRUD
```

### Rate Limits
| Endpoint | Limit |
|----------|-------|
| Company Login | 5 requests/minute |
| Client Login | 5 requests/minute |
| Client Password Reset | 5 requests/minute |
| Authenticated API | 60 requests/minute |

---

## Security Features

### Authentication Security
| Feature | Implementation |
|---------|---------------|
| Password Hashing | bcrypt (BCRYPT_ROUNDS=12) |
| Session Regeneration | On login, logout, and password change |
| Session Invalidation | On logout (complete session destroy) |
| CSRF Protection | Laravel middleware on all web routes |
| Remember Me | Optional with secure token |
| Login Throttling | 5 attempts per minute per IP (company + client login) |
| API Throttling | 60 requests per minute per user/IP |
| Public Registration | Disabled — users created only by admins |
| Client Login Check | `is_active` flag prevents deactivated client logins |

### Authorization Security
| Feature | Implementation |
|---------|---------------|
| Role-Based Access | 3-tier: super_admin, company_admin, staff |
| Granular Permissions | 12 configurable permissions via AdminRole |
| Ownership Verification | All controllers verify resource belongs to user |
| Tenant Isolation | Company users can only access their own data |
| Client Access Control | Clients can only see quotations from their companies |
| Company Active Check | Blocked companies auto-logout all users |
| Self-Deletion Protection | Users cannot delete their own accounts |
| Middleware Stack | 5 custom middleware + 3 Laravel built-in |

### Data Security
| Feature | Implementation |
|---------|---------------|
| Soft Deletes | 7 core tables use SoftDeletes (recoverable) |
| XSS Prevention | Blade auto-escaping + CMS content sanitization |
| SQL Injection | Laravel Eloquent parameterized queries |
| File Upload Validation | MIME type + size limits on payment proofs |
| Password Reset | Token-based with 60-minute expiry |
| Client Account Activation | `is_active` check prevents deactivated logins |

### Infrastructure Security
| Feature | Implementation |
|---------|---------------|
| SMTP Timeout | 10-second timeout prevents hanging connections |
| Email Error Handling | All mail sends wrapped in try-catch with `\Log::warning` on failure |
| Migration Safety | Service provider catches DB errors during boot |
| Config Caching | Settings cached for 1 hour to reduce DB queries |
| Email Flooding Protection | Rate-limited on all login and password reset endpoints |

---

## Database Schema

### 27 Tables Overview

| Table | Purpose |
|-------|---------|
| `users` | Company users + super admins |
| `companies` | Company/tenant records (includes `account_details`, `brand_color`) |
| `clients` | Client contacts per company |
| `client_users` | Client portal accounts (separate auth, with `is_active` flag) |
| `client_company` | Client-to-company pivot (multi-tenant) |
| `quotations` | Quotation records with status and `type` (standard/milestone) |
| `quotation_items` | Line items per quotation (with `start_date`, `end_date`, `sort_order` for milestones) |
| `quotation_notes` | Internal notes on quotations |
| `quotation_status_logs` | Status change audit trail |
| `quotation_revisions` | Snapshot history of edits |
| `payments` | Payment submissions and reviews (with `quotation_item_id` for milestone payments) |
| `items` | Reusable item templates |
| `notifications` | In-app notification records |
| `packages` | Subscription package definitions |
| `company_packages` | Package assignments to companies |
| `currencies` | Currency definitions with `symbol` field |
| `taxes` | Tax rate definitions |
| `admin_roles` | Granular permission roles |
| `settings` | Key-value configuration store |
| `pages` | CMS pages |
| `activity_log` | Full audit trail |
| `password_reset_tokens` | Admin/company password resets |
| `client_password_reset_tokens` | Client password resets |
| `sessions` | Database session storage |
| `cache` | Cache storage |
| `personal_access_tokens` | Sanctum API tokens |

### Key Relationships
```
Company ──1:N──▶ User ──1:N──▶ Quotation ──1:N──▶ QuotationItem ──1:N──▶ Payment (milestone)
    │              │                │                    │
    │              │                │                    ├── has start_date, end_date, sort_order
    │              │                │                    ├── is_paid (computed from approved payments)
    │              │                │                    └── paid_amount (sum of approved payments)
    │              │                │
    │              │                ├──1:N──▶ QuotationStatusLog
    │              │                ├──1:N──▶ QuotationRevision
    │              │                ├──1:N──▶ QuotationNote
    │              │                ├── type: 'standard' | 'milestone'
    │              │                └──1:N──▶ Payment (general)
    │              │
    │              ├──1:N──▶ Client ──N:1──▶ ClientUser (with is_active flag)
    │              ├──1:N──▶ Item
    │              └──1:N──▶ Notification
    │
    └──1:N──▶ CompanyPackage ──N:1──▶ Package

ClientUser ──M:N──▶ Company (via client_company pivot)
ClientUser ──1:N──▶ Client
ClientUser ──1:N──▶ Payment
```

---

## Export & Reporting

### CSV Exports
| Export | Location | Columns |
|--------|----------|---------|
| Client List | Company → Clients → Export | Name, Email, Phone, Address, Created |
| Quotation List | Company → Quotations → Export | Quote #, Client, Issue Date, Expiry, Total, Status, Payment |
| Companies Report | Admin → Reports → Export | Name, Email, Status, Users, Package, Created |
| Quotations Report | Admin → Reports → Export | Quote #, Company, Client, Date, Currency, Total, Status |
| Revenue Report | Admin → Reports → Export | Month, Quotations Count, Revenue |

### PDF Exports
| Export | Location |
|--------|----------|
| Quotation PDF | Company → Quotation → PDF (with company branding) |
| Admin Quotation PDF | Admin → Quotation → PDF |
| Email Attachment | Auto-generated when sending quotation via email |
| Reports PDF | Admin → Reports → Download as PDF |

### PDF Branding
PDFs include:
- Company logo and name (always visible in header)
- Brand color (applied to header background)
- **DejaVu Sans font** — supports all Unicode currency symbols (₨, ₹, €, £, ₿, ﷼, د.إ, etc.)
- Payment status section
- Full itemized breakdown with tax and discount
- Account Details section (company bank/payment info)
- Payment Instructions section (quotation-specific terms)

### Account Details vs Payment Instructions
- **Account Details**: Company's permanent bank/payment information (shown on all quotations and PDFs)
- **Payment Instructions**: Quotation-specific payment terms (e.g., "pay within 14 days")

---

## Settings & Configuration

### General Settings
| Setting | Description |
|---------|------------|
| App Name | Application name displayed in header/title |
| App Title | Secondary title |
| App Description | App description |
| Footer Text | Footer text across the app |
| Logo | Application logo |
| Favicon | Browser tab icon |

### Email Configuration
| Setting | Description |
|---------|------------|
| Mail Driver | smtp, sendmail, mailgun, ses, log |
| SMTP Host | Mail server hostname |
| SMTP Port | Mail server port |
| SMTP Username | Authentication username |
| SMTP Password | Authentication password |
| Encryption | tls or ssl |
| From Address | Default sender email |
| From Name | Default sender name |
| Test Email | Send test email with diagnostic output |

### Pusher (Real-Time)
| Setting | Description |
|---------|------------|
| App ID | Pusher application ID |
| App Key | Pusher public key |
| App Secret | Pusher secret |
| Cluster | Pusher cluster (e.g., us2, eu) |
| Enabled | Toggle real-time on/off |

### Social Links
Facebook, Twitter, LinkedIn, Instagram, YouTube, GitHub — displayed in footer.

---

## Setup & Installation

### Requirements
- PHP 8.3+
- MySQL 5.7+ or SQLite
- Composer
- Node.js 18+ & npm

### Quick Setup
```bash
# Clone the repository
git clone <repository-url>
cd QuotationSystem

# Run the setup command (installs everything)
composer setup

# Or manual setup:
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run build
```

### Start Development
```bash
composer dev
```
This runs the server, queue worker, log viewer, and Vite dev server concurrently.

### Available at
| URL | Description |
|-----|------------|
| `http://127.0.0.1:8000` | Welcome page |
| `http://127.0.0.1:8000/login` | Admin/Company login |
| `http://127.0.0.1:8000/admin/dashboard` | Admin panel |
| `http://127.0.0.1:8000/client/login` | Client portal login |

---

## Default Credentials

### Super Admin
| Field | Value |
|-------|-------|
| Email | `admin@quotation.test` |
| Password | `password` |

### Demo Companies
| Company | Status | Package |
|---------|--------|---------|
| TechCorp Solutions | Active | Professional |
| DesignStudio | Active | Starter |
| BuildRight Construction | Inactive | Starter |
| DataFlow Analytics | Blocked | — |

### Default Currencies
USD ($), EUR (€), GBP (£), JPY (¥), INR (₨), AUD (A$), CAD (C$), SGD (S$), PKR (₨), KWD (د.ك)

### Default Taxes
VAT (10% — default), GST (5%), Sales Tax (8.25%), Service Tax (15%)

### Default Packages
| Package | Price | Duration | Users | Clients | Quotations |
|---------|-------|----------|-------|---------|------------|
| Starter | $9.99 | 30 days | 2 | 25 | 100 |
| Professional | $29.99 | 30 days | 5 | 100 | 500 |
| Enterprise | $79.99 | 30 days | 20 | Unlimited | Unlimited |

---

## Summary

QuotationSystem is not just a quotation generator — it's a **complete business workflow platform** that bridges the gap between companies and their clients. With its multi-tenant architecture, client portal, payment verification, and comprehensive notification system, it provides everything needed to manage the quotation-to-payment lifecycle in a single, self-hosted application.

### At a Glance
- **30 database tables** — comprehensive data model
- **20+ Eloquent models** — clean MVC architecture
- **33+ controllers** — organized by panel (Admin/Company/Client)
- **6 Mailables** — branded emails on every action
- **5 custom middleware** — layered security
- **3 auth guards** — complete tenant isolation
- **12 admin permissions** — granular access control
- **6-stage quotation workflow** — draft to accepted
- **2 quotation types** — standard and milestone-based
- **3-step payment verification** — submit, review, approve
- **Per-milestone payments** — pay against individual milestones
- **7 soft-deletable tables** — data recovery
- **85+ Blade views** — responsive, branded UI
- **Company-branded emails** — logo, name, and brand color in all emails
- **Unicode-safe PDFs** — DejaVu Sans font for all currency symbols
- **RESTful API** — Sanctum-authenticated
- **Real-time updates** — Pusher WebSocket support
