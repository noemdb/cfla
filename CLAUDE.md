# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **school management system** (Colegio...) built with Laravel 10, Livewire 3, Tailwind CSS, Alpine.js, and Vite. The application handles student enrollment (censo/catchment), registration (matrícula), payment tracking, academic competitions/debates, anonymous voting/polls, diagnostic assessments, blog/news, and student progression tracking. The app locale is Spanish (`es`).

## Build & Dev Commands

```bash
# Serve the Laravel app
php artisan serve

# Frontend dev (Vite hot-reload)
npm run dev

# Frontend production build
npm run build

# Run all PHPUnit tests
php artisan test

# Run a single test class
php artisan test --filter=TestClassName

# Run a specific test method
php artisan test --filter="test_method_name"

# Laravel Pint (PHP CS fixer)
./vendor/bin/pint

# Queue worker
php artisan queue:work

# WebSocket server (Laravel Reverb)
php artisan reverb:start --host=127.0.0.1 --port=8090

# Laravel Pulse monitoring dashboard
php artisan pulse:check

# Database backup (admin route, also accessible via /admin/database/backup)
```

## Architecture

### Tech Stack
- **Backend**: Laravel 10, PHP 8.3+
- **Frontend**: Blade + Livewire 3 + Alpine.js + Tailwind CSS 3 + WireUI 2
- **Build**: Vite + laravel-vite-plugin
- **Database**: MySQL (primary), SQLite (testing)
- **Real-time**: Laravel Reverb (WebSockets), Pusher protocol
- **Queue**: Database driver
- **Auth**: Session-based with Sanctum tokens, custom role middleware
- **PDF**: barryvdh/laravel-dompdf
- **Email**: Gmail API via google/apiclient, Resend
- **QR**: simplesoftwareio/simple-qrcode
- **AI Agents**: maestroerror/laragent (framework para crear agentes de IA en Laravel)

### Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin panel (VotingDashboard, VotingPoll, Database)
│   │   ├── Auth/           # Custom auth (LoginController, registered user, passwords)
│   │   ├── Census/         # PDF generation for catchment & enrollment
│   │   ├── Educational/    # Competition/debate moderator & board views
│   │   ├── CensusController.php
│   │   ├── HomeController.php
│   │   ├── GmailController.php
│   │   ├── PollVotingController.php
│   │   ├── VotingFingerprintController.php
│   │   └── OrderController.php
│   └── Middleware/
│       ├── IsAdmin.php              # is_admin check
│       ├── IsAdminOrDiagnostic.php   # is_admin OR is_diagnostic
│       └── IsDiagnostic.php          # is_diagnostic check
├── Livewire/
│   ├── Admin/              # Admin Livewire components (Voting, Diagnostic, Educational)
│   ├── App/                # Public/app Livewire components (Catchment, Enrollment, General, Payment, Post, Voting, Educational)
│   └── Forms/              # Reusable form components
├── Models/
│   ├── app/
│   │   ├── Academy/        # Core academic models
│   │   ├── Admon/          # Administrative/financial (Payments, Banks, Exchange Rates)
│   │   ├── Blog/           # Posts, Categories, Contacts
│   │   ├── Control/        # Catchment control
│   │   ├── Educational/    # Debate competitions
│   │   ├── Entity/         # Institution, Authority, Academic period
│   │   ├── Instrument/     # Diagnostic assessment (questions, options, sessions)
│   │   ├── Learner/        # Student (Estudiant), Representative (Representant)
│   │   └── Voting/         # Polls, options, sessions, votes
│   ├── User.php
│   └── Visit.php
├── Services/
│   ├── GmailService.php
│   └── SendPulseService.php
├── Jobs/                   # SendWelcomeEmail, SendEmailJobPayment, Email queue jobs
├── Events/                 # CompetitionUpdated, OrderStatusUpdated
├── Notifications/          # CompetitionUpdateNotification
└── Mail/                   # WelcomeEmail, VerificationCodeMail, SendEmailPayment
```

### Key Domain Modules

| Module | Purpose | Key Models |
|--------|---------|------------|
| **Catchment (Censo)** | Student pre-registration campaigns with date windows ("jornadas") | `Academy\Catchment`, `Academy\CatchmentGroup` |
| **Enrollment (Matrícula)** | Full student registration with medical, transport, family data | `Academy\Enrollment` |
| **Prosecución** | Student grade progression tracking | `Learner\Estudiant` (uses `Prosecucions` trait) |
| **Students & Reps** | Core learner & guardian entities | `Learner\Estudiant`, `Learner\Representant` |
| **Payments** | Multi-student payment records, bank methods, exchange rates | `Admon\Payment`, `Admon\Banco`, `Admon\ExchangeRate` |
| **Voting** | Anonymous polls with access tokens, QR codes, fingerprinting | `Voting\VotingPoll`, `Voting\VotingVote`, `Voting\VotingSession` |
| **Debate** | Real-time academic competition/debate system | `Educational\DebateCompetition`, `Educational\Debate` |
| **Diagnóstico** | Diagnostic assessments with questions/options/sessions | `Instrument\DiagQuestion`, `Instrument\DiagSession` |
| **Blog** | News/articles with categories, files, cover pages | `Blog\Post`, `Blog\Category` |
| **Institution** | School entity, authorities, academic periods | `Entity\Institucion`, `Entity\Autoridad`, `Entity\Pescolar` |

### Route Structure

- **Public routes** (`/`): Home, Studia, Diagnóstico, Censo, Enrollment, Payment, Blog posts, Prosecución
- **General routes** (`/general/`): Competition moderator/board/scoreboard views (token-based access)
- **Voting routes** (`/poll/*`, `/voting/*`): Anonymous voting via access tokens, QR codes, assistant guide
- **Admin routes** (`/admin/`): Protected by `auth` + `isAdminOrDiagnostic` (voting, diagnostic, educational) or `isAdmin` (logs, DB backup)
- **Auth routes**: Custom login/logout (not Laravel UI/Breeze/Jetstream)
- **API routes**: Minimal, Sanctum-protected `/api/user`

### Key Patterns

- **Livewire over Controllers**: Most interactive pages use full-page Livewire components rather than controller-returned views (especially admin and wizard flows)
- **Role-based access**: Three tiers — `is_admin`, `is_diagnostic`, standard user. Middleware: `IsAdmin`, `IsAdminOrDiagnostic`
- **PDF generation**: dompdf for catchment forms, enrollment forms, and prosecución reports
- **Real-time**: Laravel Reverb for WebSocket broadcasting (debate competitions, order status). Supervisor config at `supervisor-reverb.conf`
- **Multiple DB connections**: Configured for `s2526` and `s2425` database connections (commented out in models, used for multi-year data)
- **COLUMN_COMMENTS constant**: Several models define `COLUMN_COMMENTS` for Spanish labeling of attributes
- **Anonymous voting**: Polls use access tokens (no auth required) with optional browser fingerprinting via `VotingFingerprintController`

## Claude Code Agents & Skills

This project includes specialized Laravel agents and skills for Claude Code, installed via `iserter/laravel-claude-agents` and community definitions.

### Available Agents (`.claude/agents/`)

| Agent | Description |
|-------|-------------|
| **laravel-architect** | Architecture decisions, design patterns, schema planning |
| **eloquent-specialist** | Eloquent models, relationships, N+1 prevention, migrations |
| **laravel-api-developer** | API resources, Sanctum/Passport, rate limiting |
| **laravel-testing-expert** | Pest PHP, feature/unit tests, TDD, code coverage |
| **laravel-code-reviewer** | Code quality, security checks, best practices |
| **laravel-architecture-reviewer** | Design validation, scalability, technical debt |
| **laravel-debugger** | Error diagnosis, log analysis, query debugging |
| **laravel-performance-optimizer** | Caching, queues, bottleneck identification, Octane |
| **laravel-security-auditor** | OWASP Top 10, auth review, CSRF/XSS protection |
| **laravel-documentation-engineer** | API docs, setup guides, changelogs |
| **laravel-specialist** | General Laravel 10+ specialist (community definition) |
| **laravel-expert-agent** | Laravel 12+ expert - Eloquent, Artisan, API, testing, best practices |
| **laravel-simplifier** | Simplifies PHP/Laravel code for clarity & maintainability |
| **voltagent-laravel-specialist** | VoltAgent's Laravel 10+ specialist - queues, APIs, Eloquent |

### Available Skills (`.claude/skills/`)

| Skill | Description |
|-------|-------------|
| **laravel-tdd** | Red-Green-Refactor with Pest PHP, database testing with factories |
| **laravel-brainstorming** | Feature design, approach exploration, schema and API planning |
| **laravel-systematic-debugging** | Four-phase debugging, root cause identification |
| **eloquent-best-practices** | Query optimization, N+1 prevention, mass assignment, model events |
| **laravel-api-resource-patterns** | Resource transformation, conditional attributes, pagination |
| **laravel-validation-patterns** | Form Requests, custom rules, conditional/array validation |
| **laravel-authorization-patterns** | Gates, Policies, middleware auth, Blade directives |
| **laravel-queue-patterns** | Job batching, chaining, middleware, retry strategies |
| **laravel-event-driven-architecture** | Events/listeners, subscribers, model observers, transaction safety |
| **laravel-caching-strategies** | Cache tags, atomic locks, memoization, invalidation |
| **laravel-notification-patterns** | Mail/database/broadcast/Slack, queueing, on-demand recipients |
| **laravel-middleware-patterns** | Before/after/terminable patterns, groups, parameters |
| **laravel-blade-component-patterns** | Components, slots, $attributes bag, stacks, view fragments |
| **laravel-task-scheduling** | Frequency constraints, overlap prevention, onOneServer, hooks |
| **laravel-feature-flags** | Pennant feature flags, A/B testing, scoping, gradual rollouts |
| **starter-kit-upgrade** | Pull upstream improvements from Laravel starter kits (personalizado) |

### Invoking Agents

In Claude Code, invoke an agent explicitly:

```
> Ask the laravel-architect to review the database schema
> Have laravel-code-reviewer review recent changes
> Ask laravel-performance-optimizer to analyze slow queries
> Use eloquent-specialist to refactor the User model
```

### Official Laravel Plugins (optional)

The official `laravel/agent-skills` plugins can be installed via Claude Code commands:

```
/plugin marketplace add laravel/agent-skills
/plugin install laravel@laravel
/plugin install laravel-cloud@laravel
/plugin install laravel-nightwatch@laravel
```
