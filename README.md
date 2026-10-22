# Dorm Portal

A web application for running daily dorm life at Asia-Pacific International University: room inspections, worship events, and, as the project grows, worship attendance, cleaning duty, visits, and user accounts.

This repository is **starter code** for a course project. Your tasks, the git rules, and the grading rubric are in **[ASSIGNMENT.md](ASSIGNMENT.md)**. Read it before you write any code.

## Tech stack

- **PHP 8.4** and **Laravel 13**
- **Blade** views, **Tailwind CSS 4**, and **Vite**
- **Pest** for tests, **Laravel Pint** for code style
- **SQLite** by default (tests use an in-memory database)

## Getting started

Requirements: PHP 8.4, Composer, Node.js 20+, and Git.

```bash
git clone <your-repo-url> dorm_portal
cd dorm_portal
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Run the app (server and Vite together):

```bash
composer run dev
```

Then open http://localhost:8000/roomcheck or http://localhost:8000/events.

## Running tests and code style

```bash
php artisan test --compact            # all tests
php artisan test --compact --filter=Event   # only matching tests
vendor/bin/pint                        # fix code style
```

> On a fresh clone, **85 tests pass and 27 fail** (the leftover photo tests and the home-page test). That is expected; fixing them is Milestone 1.
>
> If many tests fail with status **419**, a stale config cache is the cause. Run `php artisan config:clear`.

## Modules

| Module | URL | Status |
|---|---|---|
| Room inspections | `/roomcheck` | Working CRUD, needs refactoring |
| Worship events | `/events` | List and create, with schedule rules |
| Worship attendance | n/a | To build (`WorshipController`) |
| Cleaning duty | n/a | To build (`CleaningController`) |
| Visits | n/a | To build (`VisitController`) |
| Users and login | n/a | To build (`UserController`) |

**Worship schedule:** Monday is evening only, Tuesday to Friday are morning only, there is no worship on weekends or Thai public holidays, and each dorm gets at most one event per day. The rules live in `app/Http/Requests/StoreEventRequest.php`.

## Where things are

```
app/Http/Controllers/   one controller per module
app/Http/Requests/      form validation (FormRequest classes)
app/Models/             Eloquent models
app/Support/            helpers (Thai holiday list)
database/migrations/    table definitions; add new ones, never edit old ones
database/seeders/       demo data for `php artisan migrate --seed`
resources/views/        Blade templates, one folder per module
routes/web.php          all routes, with a module map at the top
tests/Feature/          Pest feature tests
```

Every important file starts with a comment explaining its purpose. `TODO:` comments mark the work to be done and name the task they belong to:

```bash
grep -rn "TODO" app routes database resources tests
```

## Contributing (students)

Work on a `milestone-N` branch, commit each step with a `type(scope): summary` message, and open one pull request per milestone. The full rules are in [ASSIGNMENT.md, section 2](ASSIGNMENT.md#2-git-workflow-mandatory).

## Learning resources

- [Laravel documentation](https://laravel.com/docs)
- [Laravel Bootcamp / Learn](https://laravel.com/learn)
- [Pest documentation](https://pestphp.com/docs)
- [Tailwind CSS documentation](https://tailwindcss.com/docs)
- [Conventional Commits](https://www.conventionalcommits.org/)
