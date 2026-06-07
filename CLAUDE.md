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
- **Backend**: Laravel 10, PHP 8.1+
- **Frontend**: Blade + Livewire 3 + Alpine.js + Tailwind CSS 3 + WireUI 2
- **Build**: Vite + laravel-vite-plugin
- **Database**: MySQL (primary), SQLite (testing)
- **Real-time**: Laravel Reverb (WebSockets), Pusher protocol
- **Queue**: Database driver
- **Auth**: Session-based with Sanctum tokens, custom role middleware
- **PDF**: barryvdh/laravel-dompdf
- **Email**: Gmail API via google/apiclient, Resend
- **QR**: simplesoftwareio/simple-qrcode

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
