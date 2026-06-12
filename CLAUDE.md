# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Sistema CUP is a Laravel 12 / PHP 8.2 academic management system for a university. It handles student registration (with Stripe payments), class scheduling, grade tracking, attendance, and reporting. The UI uses Blade templates with Alpine.js, Tailwind CSS, and Vite.

## Common Commands

```bash
# Full setup from scratch
composer setup          # installs deps, copies .env, generates key, runs migrations, installs npm

# Development
composer dev            # starts PHP server + queue worker + log tail + Vite (concurrent)
php artisan serve       # PHP dev server only
npm run dev             # Vite only

# Testing
composer test           # php artisan config:clear && php artisan test
php artisan test --filter TestClassName   # single test

# Code style
./vendor/bin/pint       # Laravel Pint (PSR-12)

# Assets
npm run build           # production Vite build
```

## Architecture

### Role-Based Structure

Four user roles drive the entire application. Each has its own route prefix, controller namespace, and view folder:

| Role | Prefix | Controller dir | View dir |
|------|--------|---------------|----------|
| Administrador | `/admin` | `Admin/` | `admin/` |
| Coordinador | `/coordinador` | `Coordinador/` | `coordinador/` |
| Docente | `/docente` | `Docente/` | `docente/` |
| Estudiante | `/estudiante` | `Estudiante/` | `estudiante/` |

Role enforcement uses `CheckRol` middleware applied per-route group. The `User` model has helper methods: `esAdmin()`, `esCoordinador()`, `esDocente()`, `esEstudiante()`.

Each role corresponds to a separate profile model: `Administrador`, `Coordinador`, `Docente`, `Estudiante` — linked to `User` via `persona_id` through the `Persona` model.

### Key Flows

**Student Registration** — 5-step wizard under `/inscripcion/paso/{1-5}`:
1. Personal data → 2. Academic documents → 3. File uploads → 4. Stripe payment → 5. Confirmation.
Stripe webhook at `/stripe/webhook` finalizes payment status.

**Academic Cycle** — `Gestion` (academic year/period) is the top-level entity. `Carrera` (degree programs) are linked to `Gestion` via `CarreraGestion`. `Grupo` (class groups) belong to `CarreraGestion`. `MateriaGrupo` links subjects to groups, and `Asignacion` links teachers to those.

**Grade & Attendance** — Teachers manage `ClaseProgramada` (scheduled classes), record `Asistencia` per student, and enter `Nota` records per exam (`Examen`). The `AdmisionFinalService` handles bulk admission result processing.

**Bulk Import** — `ProcesarImportacion` job processes Excel uploads asynchronously (queue driver: `database` in production).

### Database

Development uses SQLite (default). Production uses PostgreSQL on Render. The `DB_CONNECTION` env var switches between them. 30 migration files under `database/migrations/`. Core seeders: `AdminSeeder`, `GestionSeeder`, `CarreraSeeder`, `MateriaSeeder`, `ConfigSistemaSeeder`.

### Frontend

Blade templates only — no SPA. Alpine.js for interactivity, Tailwind CSS for styling, compiled via Vite (`vite.config.js`). Shared layout components in `resources/views/components/`.

## Environment Setup

Copy `.env.example` to `.env` and configure:

```env
DB_CONNECTION=sqlite          # or pgsql for PostgreSQL
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

Queue and session drivers default to `database` — run `php artisan migrate` before starting.

## Deployment (Render)

Docker-based via `Dockerfile` (PHP 8.2 Apache + Node 20). On startup, `docker-start.sh` generates `.env` from Render environment variables, caches config/routes/views, and runs migrations. The `render.yaml` defines a free-tier PostgreSQL database named `sistemacup`. Stripe keys must be set manually in the Render dashboard.
