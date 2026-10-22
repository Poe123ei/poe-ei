# Dorm Portal: Course Project

You are taking over a small Laravel application that helps dorm staff run daily dorm life: room inspections, worship events, and, after your work, attendance, cleaning duty, visits, and user accounts.

The project is split into **three graded milestones**. You work **individually**. Every change you make must be **committed step by step**, and your commit history is graded as strictly as your code.

---

## 1. Getting started

### 1.1 Setup

```bash
git clone <your-repo-url> dorm_portal
cd dorm_portal
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan config:clear
php artisan test --compact
```

Run the app with `composer run dev` (or `php artisan serve` and `npm run dev` in two terminals).

On a fresh clone, **85 tests pass and 27 fail**: the photo tests and `ExampleTest`. That is expected, and fixing them is part of Milestone 1.

### 1.2 What already exists

| Module | URL | Status |
|---|---|---|
| Room inspections | `/roomcheck` | Working CRUD, but written "the long way" and unstyled |
| Worship events | `/events` | List and create only, with strict schedule rules |
| Worship attendance | n/a | Placeholder: `WorshipController` |
| Cleaning duty | n/a | Placeholder: `CleaningController` |
| Visits | n/a | Placeholder: `VisitController` |
| Users | n/a | Placeholder: `UserController` |
| Photo gallery | n/a | **Leftover code that is not part of this project.** You remove it. |

**Worship schedule rules** (already enforced in `app/Http/Requests/StoreEventRequest.php`): Monday is evening only, Tuesday to Friday are morning only, there is no worship on weekends or Thai public holidays, and each dorm gets at most one event per day.

### 1.3 Read the comments

Every important file starts with a comment explaining what it does. `TODO:` comments mark where work is expected, and most name the task they belong to (for example *Milestone 1, Task 1.4*). To list them all:

```bash
grep -rn "TODO" app routes database resources tests
```

When you finish a task, **update or remove its `TODO:` comments in the same commit**.

---

## 2. Git workflow (mandatory)

These rules apply to every milestone. Breaking them costs points (see the rubric), and some break the strict gates in section 4.

### 2.1 Branches and pull requests

1. Start each milestone from an up-to-date `main`:
   ```bash
   git checkout main
   git pull
   git checkout -b milestone-1      # milestone-2, milestone-3 later
   ```
2. **Never commit directly to `main`.**
3. Push the branch and open **one pull request into `main`** for the milestone. The instructor merges it after grading. Start the next milestone only from the merged `main`.

### 2.2 Commit messages

Use the [Conventional Commits](https://www.conventionalcommits.org/) format:

```
type(scope): imperative summary of the change
```

- **type** is one of `feat`, `fix`, `refactor`, `test`, `style`, `docs`, or `chore`.
- **scope** is the module: `events`, `roomcheck`, `home`, `layout`, `auth`, `users`, `db`, `attendance`, `cleaning`, `visits`, or `photos`.
- The summary is written in the imperative ("add", not "added"), in lower case, with no full stop, in **72 characters or fewer**.

| ✅ Good | ❌ Not accepted |
|---|---|
| `feat(events): add edit and update actions` | `update` |
| `test(events): cover schedule rules on update` | `fix stuff` |
| `refactor(roomcheck): move validation to form request` | `wip` |
| `chore(photos): remove photo feature` | `final version` / `asdf` / `.` |

### 2.3 Commit rules

- **One logical change per commit.** Don't mix a refactor, a new feature, and formatting in one commit.
- Each task below lists **required commits**. That is the minimum: you may split a step into more commits, but you may not merge two required steps into one.
- **Every commit must work.** Before each commit, run:
  ```bash
  vendor/bin/pint
  php artisan test --compact
  ```
  The only exception is Milestone 1: the 27 tests that fail on the starter may keep failing until the task that fixes them (1.1 and 1.2).
- **Forbidden:** merge commits from `main` into your branch, `git push --force` after you open the PR, and the messages in the ❌ column above.
- **Never commit** `.env`, `vendor/`, `node_modules/`, `public/build/`, or uploaded files. Check with `git status` before each commit.

### 2.4 Pull request description

Every PR description must contain:

1. **Tasks completed**, as a list with task numbers.
2. **How to test**: the URLs to open and the steps to follow.
3. **Screenshots** of every new or changed page.
4. **Known issues**, if any. Honesty here is worth more than hiding a bug.
5. The **submission checklist** from section 6, ticked.

---

## 3. Milestones and tasks

Each task lists its **acceptance criteria** (what the grader checks) and **required commits** (in order).

### Milestone 1: Clean up and finish the starter (100 pts)

Branch: `milestone-1`

#### 1.1 Remove the photo feature
The repository contains leftover photo-gallery code that does not belong to the dorm portal.
- **Delete:** `app/Models/Photo.php`, `app/Http/Requests/StorePhotoRequest.php`, `app/Http/Requests/UpdatePhotoRequest.php`, `database/factories/PhotoFactory.php`, `database/migrations/2026_10_19_074004_create_photos_table.php`, `tests/Feature/PhotoControllerTest.php`, and any files in `storage/app/public/photos/`.
- **Criteria:**
  - `git grep -il photo -- app database tests routes resources` returns nothing.
  - `php artisan migrate:fresh` runs without errors.
  - No other test breaks.
- **Commits:**
  1. `chore(photos): remove photo feature`

#### 1.2 Home page
- Add a `/` route named `home` that renders a landing page linking to every live module.
- **Criteria:**
  - `ExampleTest` passes.
  - Every link on the page works.
  - A new test asserts that the page shows a link to each module.
- **Commits:**
  1. `feat(home): add home page route and view`
  2. `test(home): assert home page links to modules`

#### 1.3 Shared layout
- Create one Blade layout (a component such as `<x-layout>`, or `@extends`) with the page `<head>`, a navigation bar, and flash messages (`session('status')`). Style it with **Tailwind via Vite** (`@vite`), which is already installed.
- Convert every page to use it.
- **Criteria:**
  - No view other than the layout contains `<html>`, `<head>`, or `<style>`.
  - The navigation appears on every page.
  - Room inspection pages look consistent with the event pages.
- **Commits:**
  1. `feat(layout): add shared layout with navigation`
  2. `refactor(events): use shared layout`
  3. `refactor(roomcheck): use shared layout`
  4. `refactor(home): use shared layout`

#### 1.4 Dorm list as data
- Dorm names are hardcoded in `events/create.blade.php`, and the server accepts any string.
- Move the list to a single source (a config file or a PHP enum), use it in the view, and validate it with `Rule::in` (or `Rule::enum`).
- **Criteria:**
  - An unknown dorm is rejected with a validation error.
  - The view and the validation read from the same source.
  - Existing tests that use invalid dorm names (`North Dorm`, `South Dorm`) are updated to use real dorms.
- **Commits:**
  1. `refactor(events): load dorm list from a single source`
  2. `feat(events): validate dorm against the dorm list`
  3. `test(events): reject unknown dorms`

#### 1.5 Complete the events CRUD
- Add `show`, `edit`, `update`, and `destroy`, and extend the route to the full resource.
- `update` must enforce **every** schedule rule. Reuse the rules (for example a shared base FormRequest or a trait) rather than copying them. The "one event per dorm per day" check must ignore the event being edited.
- Add View, Edit, and Delete actions to the list (Delete needs a confirmation).
- **Criteria:**
  - Every schedule rule fails correctly on update.
  - Saving an event without changes succeeds.
  - A missing event returns 404 on show, edit, update, and destroy.
- **Commits:**
  1. `feat(events): add event detail page`
  2. `refactor(events): share schedule rules between store and update`
  3. `feat(events): add edit and update actions`
  4. `feat(events): add delete action`
  5. `test(events): cover show, update and delete`

#### 1.6 Clean up room inspections
- **Criteria:**
  - All existing roomcheck tests still pass, apart from changes required by the refactor.
  - Every controller method has a return type.
  - The `TODO:` comments for this task are removed.
- **Commits:**
  1. `refactor(roomcheck): move validation to form request`
  2. `refactor(roomcheck): use mass assignment with fillable`
  3. `refactor(roomcheck): use route model binding`
  4. `feat(roomcheck): add inspection detail page`
  5. `feat(roomcheck): paginate inspection list`
  6. `test(roomcheck): add factory and use it in tests and seeder`

---

### Milestone 2: Authentication, roles and data model (100 pts)

Branch: `milestone-2`, from `main` after Milestone 1 is merged.

#### 2.1 Authentication
- Login and logout with Laravel's built-in session authentication (`Auth::attempt`, `auth` middleware). Installing a starter-kit package requires **written approval from the instructor first**.
- Every module route requires login. Guests are redirected to the login page.
- **Criteria:**
  - Tests cover a successful login, wrong credentials, logout, and a guest being redirected from a protected page.
- **Commits:**
  1. `feat(auth): add login and logout`
  2. `feat(auth): protect module routes`
  3. `test(auth): cover login, logout and guest redirects`

#### 2.2 Roles
- Add a `role` column to `users` backed by a PHP enum `App\Enums\Role` with cases `Admin`, `Staff`, and `Student`.
- Write a policy for each module: staff and admins manage inspections and events, while students can only view.
- **Criteria:**
  - The UI hides actions a user may not perform, and the server also blocks them with a 403 response.
  - Tests cover each role on each protected action.
- **Commits:**
  1. `feat(db): add role column and Role enum`
  2. One commit per policy, e.g. `feat(events): add event policy`
  3. `test(auth): cover permissions for each role`

#### 2.3 Dorms, rooms and students
- Create a `dorms` table (name) and a `rooms` table (dorm_id, number, capacity), and add `users.room_id` (nullable, students only). Include models, relationships, factories, and seeders.
- In **new** migrations (never edit old ones), replace `room_checks.room_id/student_id/student_name` and `events.dorm` with real foreign keys, and **keep the existing data**.
- **Criteria:**
  - `php artisan migrate:fresh --seed` produces a realistic demo dataset.
  - The dorm list from Task 1.4 now comes from the `dorms` table.
  - No free-text student names remain.
- **Commits:**
  1. `feat(db): add dorms table and model`
  2. `feat(db): add rooms table and model`
  3. `feat(db): assign students to rooms`
  4. `refactor(roomcheck): link inspections to rooms and students`
  5. `refactor(events): link events to dorms`
  6. `test(db): cover relationships and room capacity`

#### 2.4 User management
- Replace the stubs in `UserController` with admin-only CRUD for users, including role and room assignment. A room may not exceed its capacity.
- Add a profile page where any user can update their own name and password.
- **Commits:**
  1. `feat(users): add admin user management`
  2. `feat(users): add profile page`
  3. `test(users): cover user management and room capacity`

---

### Milestone 3: Dorm modules (100 pts)

Branch: `milestone-3`. **Tasks 3.1 and 3.2 are required. Choose one of 3.3 or 3.4.** The other counts as bonus.

Each module needs its own migration, model, factory, policy, FormRequest(s), controller, views, and tests. Required commits for each module, in order:

1. `feat(db): add <module> tables`
2. `feat(<module>): add <main screen>`, plus one commit per further screen or action
3. `feat(<module>): add <module> policy`
4. `test(<module>): ...`

#### 3.1 Room inspection details
- Each inspection records a date, an inspector (FK to a staff user), a checklist (bed, floor, desk, trash, bathroom: pass or fail), an overall score from 0 to 100, and notes.
- Only staff can create or edit inspections. A student sees only their own room's inspections.
- **Criteria:**
  - The score is validated.
  - The checklist is stored properly, not as one long string.
  - A student cannot open another room's inspection (403).

#### 3.2 Worship attendance
- Add an `attendances` table (event_id, user_id, status), where status is a PHP enum: Present, Late, Absent, or Excused. Each student has one row per event.
- Staff open an event and mark every student of that dorm in one form.
- Each student has a page showing their attendance % and history.
- **Criteria:**
  - Duplicate attendance rows are impossible, enforced by a unique index *and* validation.
  - The percentage is correct (tested).

#### 3.3 Cleaning duty (`CleaningController`)
- Staff create duties (area or room, student, due date). Students mark their own duty as done.
- The list shows overdue duties clearly.
- **Criteria:**
  - A student can only complete their own duties.
  - Overdue logic is tested.

#### 3.4 Visits (`VisitController`)
- Log visitors: visitor name, host student, check-in and check-out times, and a status of Pending, Approved, or Rejected.
- Staff approve or reject visits.
- **Criteria:**
  - Check-out cannot be before check-in.
  - Only staff can approve.
  - A student sees only their own visits.

### Bonus (up to +10 per milestone, max 100)

Each bonus item is its own commits and must include tests:

- Dashboard with stats (attendance %, average inspection score per dorm)
- CSV export of any list
- Search and filters on list pages
- Email notifications (failed inspection, visit approved)
- Holidays stored in the database with an admin screen (replaces `ThailandHolidays`)
- Keep `README.md` up to date: document every module and URL you add

---

## 4. Strict gates

The grader checks these first. **If any gate fails, the PR is returned ungraded** and you must resubmit (the late penalty applies).

| # | Gate | How it is checked |
|---|---|---|
| G1 | All tests pass at the PR head | `php artisan test --compact` |
| G2 | Code is formatted | `vendor/bin/pint --test` |
| G3 | The PR goes from `milestone-N` into `main` | GitHub PR page |
| G4 | No forbidden files are committed | `git ls-files \| grep -E "^\.env$\|^vendor/\|^node_modules/"` is empty |
| G5 | A fresh install works | Clone, run the setup steps in 1.1, then `php artisan migrate:fresh --seed` |

---

## 5. Rubric

The same rubric is applied to each milestone, for 100 points per milestone.

| Criterion | Pts | Excellent (100%) | Adequate (~60%) | Poor (0–30%) |
|---|---|---|---|---|
| **Functionality** | 35 | Every acceptance criterion of every task is met; no bugs found while testing | Most criteria met; minor bugs | Key criteria missing, pages crash, or rules not enforced |
| **Tests** | 20 | Every feature has happy-path *and* failure tests (validation, 404, 403); tests are readable and use factories | Mostly happy-path tests; some failure cases missing | Few or no new tests, or tests that assert nothing meaningful |
| **Code quality and Laravel conventions** | 15 | FormRequests, route model binding, policies, enums, relationships, `php artisan make:` files, typed methods, no dead code | Some conventions skipped; some duplication | Logic in views, raw `$_POST` / `$request->all()`, copy-pasted rules, commented-out code |
| **Commit history** | 20 | Every required commit present, in order, correctly formatted; one logical change each; every commit passes tests | Required steps present, but some vague messages or mixed changes | Few large commits, forbidden messages, broken commits, or commits on `main` |
| **Pull request and documentation** | 10 | Complete PR description (section 2.4) with screenshots; `TODO:` comments updated or removed; new code has meaningful PHPDoc | Partial description or missing screenshots | No description; stale `TODO:`s left behind |

### Penalties

| Situation | Penalty |
|---|---|
| Late submission | −10 per day, up to 3 days; after that the PR is not accepted |
| All work in one squashed or "dump" commit | Commit history scores **0** |
| `.env` or another secret committed (even if deleted later) | −15 |
| Commits made directly on `main` | −10 |
| Force-push after the PR is opened | −10 |
| Copying another student's work | Handled under university academic integrity policy |

---

## 6. Submission checklist

Copy this into every PR description and tick each item:

```markdown
- [ ] Branch is `milestone-N`, created from the latest `main`
- [ ] Every required commit from the task list is present, in order
- [ ] All commit messages follow `type(scope): summary`
- [ ] `vendor/bin/pint --test` passes
- [ ] `php artisan test --compact` passes
- [ ] `php artisan migrate:fresh --seed` works
- [ ] No `.env`, `vendor/` or `node_modules/` in the commits
- [ ] `TODO:` comments for completed tasks are updated or removed
- [ ] PR description has tasks, test steps, screenshots and known issues
```
