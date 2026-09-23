# Repository Guidelines

## Project Structure

This is a small Laravel 13 API project for a referral program (PHP 8.5). Application code is in `app/`: Eloquent models in `app/Models`, referral logic in `app/Services/Referral`, payment handling in `app/Observers`, and the current-master header middleware in `app/Http/Middleware`. API routes belong in `routes/api.php`; configuration is in `config/`; schema and demo data are in `database/migrations` and `database/seeders`. Tests live in `tests/`. There is no frontend or asset build.

## Development and Verification

The project runs in Sail's PHP 8.5 container (`compose.yaml`). For first-time dependency installation, follow the Docker setup in README; for ongoing work, start Sail with `./vendor/bin/sail up -d` and run Artisan or Composer through `./vendor/bin/sail`.

- `./vendor/bin/sail artisan migrate --seed --force` initializes the local database.
- `./vendor/bin/sail artisan test` runs the Pest/PHPUnit suite.
- `make pint` checks formatting; `make pint-hard` applies Pint; `make stan` runs PHPStan.
- `curl http://localhost/api/ping` checks the Sail-served API.

Use `X-Master-Id` when exercising API endpoints; seeded master `1` is Маша. `./vendor/bin/sail artisan migrate:fresh --seed` resets the local database. README's standalone PHP CLI example currently names PHP 8.3; use the configured PHP 8.5 runtime instead.

## Coding Style

Follow nearby Laravel conventions: four-space PHP indentation, one class per file, PSR-4 namespaces, and `declare(strict_types=1);` in PHP source files. Give public methods explicit parameter and return types. Keep HTTP routing thin and place business rules in the existing service or model layer. Use descriptive camelCase methods and PascalCase class names. Run Pint on changed PHP files before submitting.

## Tests

The project uses Pest/PHPUnit through Laravel; place API behavior tests under `tests/Feature` and name them for the behavior, such as `ReferralRoutesTest.php`. Cover successful requests and key edge cases, including missing/invalid codes, self-referral, repeat attachment, and payment eligibility. No coverage threshold is configured; add focused tests for changed behavior.

## Commits and Pull Requests

The short Git history does not establish a strict commit format. Use a concise imperative subject describing one change (for example, `Add referral earnings endpoint`). PRs should explain behavior and notable assumptions, list verification commands and results, and link the relevant task when one exists. Include request/response examples for API changes.

## Configuration and Data Safety

Keep secrets in `.env`; do not commit local environment files or generated dependencies. Treat seeded data as development fixtures, and do not use destructive database-reset commands against non-local environments.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.5. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines for specific paths also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record a rule with `record-rule` only when the user explicitly asks for one. Instructions for the work at hand are not rules, no matter how emphatic: "remove this typo", "use X here" are work to do, not rules to record. Never record a rule on your own initiative, as a byproduct of a change, or to summarize what you just did. When the user does ask, pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Use `record-rule` rather than your native memory or notes tool, because native memory is personal and session-scoped, while only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `vendor/bin/sail artisan route:list`). Use `vendor/bin/sail artisan list` to discover available commands and `vendor/bin/sail artisan [command] --help` to check parameters.
- Inspect routes with `vendor/bin/sail artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `vendor/bin/sail artisan config:show app.name`, `vendor/bin/sail artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `vendor/bin/sail artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `vendor/bin/sail artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== sail rules ===

# Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
    - Run Artisan Commands: `vendor/bin/sail artisan migrate`
    - Install Composer packages: `vendor/bin/sail composer install`
    - Execute Node commands: `vendor/bin/sail npm run dev`
    - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `vendor/bin/sail artisan list` and check their parameters with `vendor/bin/sail artisan [command] --help`.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `vendor/bin/sail artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/sail bin pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test --format agent`, simply run `vendor/bin/sail bin pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `vendor/bin/sail artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `vendor/bin/sail artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/sail bin pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `vendor/bin/sail artisan test --compact`.

=== pepperfm/ai-guidelines/_core/core rules ===

# Core — Project Guidelines (MUST)

**Версия:** 2026‑01‑30

Этот файл содержит **общие правила**, применимые ко всем проектам в репозитории (Laravel и связанные пакеты).


## 1) Приоритеты инструкций (MUST)

- **MUST > SHOULD.** При конфликте обязателен к исполнению MUST.
- Эти гайдлайны **выше** общих туториалов/примеров из интернета, если не указано иное.
- Если загружено несколько гайдлайнов, действует **каскад**: более специфичный (обычно ближе к рабочей директории) имеет больший приоритет.
- Если пользователь **явно** просит отступить от правил, это допустимо **только если** не нарушает MUST/безопасность/песочницу.

---

## 2) Язык и тон (MUST)

- **Русский — язык общения по умолчанию.**
- Имена компонентов/props/опций/слотов, названия классов/файлов, ключи `.env`, команды CLI, тексты исключений — **не переводить**.
- Для длинных англоязычных логов/трейсов:
  1) сначала дать короткое русское резюме «что сломалось и где»;
  2) затем привести небольшой релевантный фрагмент оригинала (см. §4).

---

## 3) Контейнер и выполнение команд (MUST)

- Проект работает в **Laravel Sail**. Все PHP/Artisan‑команды запускаются через `./vendor/bin/sail artisan ...`.
- **Не использовать** `docker compose exec ...` напрямую — только Sail‑обёртку.
- Примеры:
  - Миграции: `./vendor/bin/sail artisan migrate`
  - Тесты: `./vendor/bin/sail artisan test`
  - Любая Artisan‑команда: `./vendor/bin/sail artisan <command>`
  - Composer: `./vendor/bin/sail composer ...`
- **Нельзя заявлять**, что команда была запущена/миграции применены/тесты пройдены, если это не подтверждено выводом команды.

---

## 4) Дисциплина вывода (SHOULD)

- Не вставлять в ответ «простыню» логов.
- По умолчанию достаточно:
  - 5–15 строк контекста вокруг ошибки **и/или**
  - последние 20–60 строк вывода (tail), если ошибка в конце.
- Если нужен полный лог — сначала спросить, либо предложить сохранить лог в файл и приложить путь.

---

## 5) Источники правды и актуальность (SHOULD)

- Приоритет источников:
  1) код и конфигурация репозитория;
  2) MCP‑серверы проекта;
  3) локальные «зеркала» документации в репозитории;
  4) официальные доки библиотек/фреймворков.
- При сомнениях по версии:
  - уточнить установленную версию (`composer.lock`, `package.json`) и сверять с соответствующей веткой документации.

---

## 6) Без «фоновых обещаний» (MUST)

- Нельзя отвечать в стиле «сделаю позже», «подождите», «вернусь с результатом».
- Либо выполнить задачу прямо сейчас, либо честно описать ограничение и дать следующий лучший вариант.

---

## 7) Skills (SHOULD)

Если в проекте есть каталог `.ai/skills/` — это **набор модульных навыков**.

- **Не подгружай все навыки сразу.** Выбирай 1–3 релевантных под задачу (чтобы экономить контекст/токены).
- Если задача затрагивает конкретный стек — сначала подключай профильный skill (например, Laravel стиль).

=== pepperfm/ai-guidelines/core rules ===

<!-- BEGIN: _core/core.md -->

# Core — Project Guidelines (MUST)

**Версия:** 2026‑01‑30

Этот файл содержит **общие правила**, применимые ко всем проектам в репозитории (Laravel и связанные пакеты).


## 1) Приоритеты инструкций (MUST)

- **MUST > SHOULD.** При конфликте обязателен к исполнению MUST.
- Эти гайдлайны **выше** общих туториалов/примеров из интернета, если не указано иное.
- Если загружено несколько гайдлайнов, действует **каскад**: более специфичный (обычно ближе к рабочей директории) имеет больший приоритет.
- Если пользователь **явно** просит отступить от правил, это допустимо **только если** не нарушает MUST/безопасность/песочницу.

---

## 2) Язык и тон (MUST)

- **Русский — язык общения по умолчанию.**
- Имена компонентов/props/опций/слотов, названия классов/файлов, ключи `.env`, команды CLI, тексты исключений — **не переводить**.
- Для длинных англоязычных логов/трейсов:
  1) сначала дать короткое русское резюме «что сломалось и где»;
  2) затем привести небольшой релевантный фрагмент оригинала (см. §4).

---

## 3) Контейнер и выполнение команд (MUST)

- Проект работает в **Laravel Sail**. Все PHP/Artisan‑команды запускаются через `./vendor/bin/sail artisan ...`.
- **Не использовать** `docker compose exec ...` напрямую — только Sail‑обёртку.
- Примеры:
  - Миграции: `./vendor/bin/sail artisan migrate`
  - Тесты: `./vendor/bin/sail artisan test`
  - Любая Artisan‑команда: `./vendor/bin/sail artisan <command>`
  - Composer: `./vendor/bin/sail composer ...`
- **Нельзя заявлять**, что команда была запущена/миграции применены/тесты пройдены, если это не подтверждено выводом команды.

---

## 4) Дисциплина вывода (SHOULD)

- Не вставлять в ответ «простыню» логов.
- По умолчанию достаточно:
  - 5–15 строк контекста вокруг ошибки **и/или**
  - последние 20–60 строк вывода (tail), если ошибка в конце.
- Если нужен полный лог — сначала спросить, либо предложить сохранить лог в файл и приложить путь.

---

## 5) Источники правды и актуальность (SHOULD)

- Приоритет источников:
  1) код и конфигурация репозитория;
  2) MCP‑серверы проекта;
  3) локальные «зеркала» документации в репозитории;
  4) официальные доки библиотек/фреймворков.
- При сомнениях по версии:
  - уточнить установленную версию (`composer.lock`, `package.json`) и сверять с соответствующей веткой документации.

---

## 6) Без «фоновых обещаний» (MUST)

- Нельзя отвечать в стиле «сделаю позже», «подождите», «вернусь с результатом».
- Либо выполнить задачу прямо сейчас, либо честно описать ограничение и дать следующий лучший вариант.

---

## 7) Skills (SHOULD)

Если в проекте есть каталог `.ai/skills/` — это **набор модульных навыков**.

- **Не подгружай все навыки сразу.** Выбирай 1–3 релевантных под задачу (чтобы экономить контекст/токены).
- Если задача затрагивает конкретный стек — сначала подключай профильный skill (например, Laravel стиль).

<!-- END: _core/core.md -->

---

<!-- BEGIN: laravel/core.md -->

# Codex — Laravel/Sail Guidelines (Lite)

**Версия:** 2026‑03‑25

Этот документ — **короткая версия** Laravel‑правил: только MUST/ограничения.
Детальные примеры и разъяснения вынесены в `.ai/skills/**` (SKILLS), чтобы экономить контекст/токены.

> Общие правила (Core) см. в target: `01-core.md` (layout `flat-numbered`) или `_core/core.md` (layout `folders`).

---

## 1) Skills (подключай по необходимости)

- `laravel-sail-and-tests` — запуск команд и тестов через Sail + правила таймаутов/вывода.
- `laravel-php-style` — подробный PHP/Laravel стиль: strict_types, helpers, Arr::get, FQCN, импорты, контроллеры.

---

## 2) MUST

- Все команды запускаются через **Laravel Sail** (`./vendor/bin/sail ...`).
- Если в проекте доступен Laravel Boost MCP / Docs API, сначала используем его как источник правды по Laravel ecosystem, а не «память» модели.
- Нельзя утверждать, что команда была выполнена, если нет подтверждённого вывода.
- Каждый PHP-файл начинается с `declare(strict_types=1);`.
- Все публичные методы имеют явные return type'ы (для HTTP — конкретные типы ответа).
- Для вендорных типов в сигнатурах — **inline FQCN** (не импортировать ради сокращения).
- Для данных: guaranteed key -> прямой доступ, optional array key -> `Arr::get(...)`, mixed/object path -> `data_get(...)`; не пишем `$payload['x'] ?? null` как замену helper-у.
- Helpers > Facades: если есть helper — используем helper.
- Для `str()`: UUID как строку получаем через `str()->uuid()->toString()`, а обычную PHP-строку из fluent `Stringable` — через `->value()`.
- Интерполяция строк допустима; простые `$var` и `$object->property` пишем без `{}`, более сложные выражения оставляем прямо в строке через `{...}` и не упрощаем их без причины во временные переменные или конкатенацию.
- Для "пусто / заполнено" по умолчанию предпочитаем `blank()` / `filled()`, `=== null` оставляем для проверки именно `null`, а `empty()` в основном для явной проверки пустого массива.
- `Collection` сохраняем для fluent-обработки; в `array` переходим только на границе контракта, native PHP или внешнего payload.
- В Eloquent по умолчанию предпочитаем `exists()/doesntExist()`, `value()`, `firstWhere()`, `findOrFail()/firstOrFail()` и `pluck(...)->all()`.
- В контроллерах helper-first и sugar-first: `response()->json(...)`, `to_route(...)`, `back()`; доменный слой по умолчанию кидает исключения, а не строит HTTP response.
- Не вводим временные переменные и лишние локальные рефакторы без пользы; request-like DI объект по умолчанию называется `$request`.
- Обычный `Request` и inline `$request->validate(...)` допустимы для простых кейсов; если входной слой растёт, default move — в `FormRequest`; если нужен тип — используем typed request methods.
- Для исключений имя переменной всегда `$e`; не логируем/не `report(...)` исключение перед пробросом без новой полезной информации.
- Импорты держим в стабильном порядке; если импортирован родительский класс (`extends BaseClass`), он идёт первым среди всех `use`-импортов файла.
- Используем проектные хелперы: `user()`, `when()`, `valueOrDefault()`, `db()`.
- Контроллеры тонкие, валидация — через `FormRequest`.
- Не читать `env()` в рантайме — только `config()`.
- Доступ к БД: Eloquent по умолчанию; при Query Builder/транзакциях — `db()`.

---

## 3) MUST NOT

- Не использовать `docker compose exec` напрямую.
- Не запускать `php artisan`/`composer` на хосте (вне контейнера).
- Не предлагать Pest `--parallel` без явного подтверждения готовых прав/настроек БД.
- Не использовать фасады, если существует эквивалентный helper.

---

## 4) Быстрые команды (шпаргалка)

> Детали, таймауты и вывод — в skill `laravel-sail-and-tests`.

```bash
# Artisan
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan test

# Composer
./vendor/bin/sail composer i

```

<!-- END: laravel/core.md -->

---

---

=== pepperfm/ai-guidelines/laravel/core rules ===

# Codex — Laravel/Sail Guidelines (Lite)

**Версия:** 2026‑03‑25

Этот документ — **короткая версия** Laravel‑правил: только MUST/ограничения.
Детальные примеры и разъяснения вынесены в `.ai/skills/**` (SKILLS), чтобы экономить контекст/токены.

> Общие правила (Core) см. в target: `01-core.md` (layout `flat-numbered`) или `_core/core.md` (layout `folders`).

---

## 1) Skills (подключай по необходимости)

- `laravel-sail-and-tests` — запуск команд и тестов через Sail + правила таймаутов/вывода.
- `laravel-php-style` — подробный PHP/Laravel стиль: strict_types, helpers, Arr::get, FQCN, импорты, контроллеры.

---

## 2) MUST

- Все команды запускаются через **Laravel Sail** (`./vendor/bin/sail ...`).
- Если в проекте доступен Laravel Boost MCP / Docs API, сначала используем его как источник правды по Laravel ecosystem, а не «память» модели.
- Нельзя утверждать, что команда была выполнена, если нет подтверждённого вывода.
- Каждый PHP-файл начинается с `declare(strict_types=1);`.
- Все публичные методы имеют явные return type'ы (для HTTP — конкретные типы ответа).
- Для вендорных типов в сигнатурах — **inline FQCN** (не импортировать ради сокращения).
- Для данных: guaranteed key -> прямой доступ, optional array key -> `Arr::get(...)`, mixed/object path -> `data_get(...)`; не пишем `$payload['x'] ?? null` как замену helper-у.
- Helpers > Facades: если есть helper — используем helper.
- Для `str()`: UUID как строку получаем через `str()->uuid()->toString()`, а обычную PHP-строку из fluent `Stringable` — через `->value()`.
- Интерполяция строк допустима; простые `$var` и `$object->property` пишем без `{}`, более сложные выражения оставляем прямо в строке через `{...}` и не упрощаем их без причины во временные переменные или конкатенацию.
- Для "пусто / заполнено" по умолчанию предпочитаем `blank()` / `filled()`, `=== null` оставляем для проверки именно `null`, а `empty()` в основном для явной проверки пустого массива.
- `Collection` сохраняем для fluent-обработки; в `array` переходим только на границе контракта, native PHP или внешнего payload.
- В Eloquent по умолчанию предпочитаем `exists()/doesntExist()`, `value()`, `firstWhere()`, `findOrFail()/firstOrFail()` и `pluck(...)->all()`.
- В контроллерах helper-first и sugar-first: `response()->json(...)`, `to_route(...)`, `back()`; доменный слой по умолчанию кидает исключения, а не строит HTTP response.
- Не вводим временные переменные и лишние локальные рефакторы без пользы; request-like DI объект по умолчанию называется `$request`.
- Обычный `Request` и inline `$request->validate(...)` допустимы для простых кейсов; если входной слой растёт, default move — в `FormRequest`; если нужен тип — используем typed request methods.
- Для фиксированных перечислений почти всегда используем enum: сравнение по case (`=== Status::Draft`), `->value` только на границе, labels через методы enum; входные значения валидируем до сравнения.
- Для исключений имя переменной всегда `$e`; не логируем/не `report(...)` исключение перед пробросом без новой полезной информации.
- Импорты держим в стабильном порядке; если импортирован родительский класс (`extends BaseClass`), он идёт первым среди всех `use`-импортов файла.
- Используем проектные хелперы: `user()`, `when()`, `valueOrDefault()`, `db()`.
- Контроллеры тонкие, валидация — через `FormRequest`.
- Не читать `env()` в рантайме — только `config()`.
- Доступ к БД: Eloquent по умолчанию; при Query Builder/транзакциях — `db()`.

---

## 3) MUST NOT

- Не использовать `docker compose exec` напрямую.
- Не запускать `php artisan`/`composer` на хосте (вне контейнера).
- Не предлагать Pest `--parallel` без явного подтверждения готовых прав/настроек БД.
- Не использовать фасады, если существует эквивалентный helper.

---

## 4) Быстрые команды (шпаргалка)

> Детали, таймауты и вывод — в skill `laravel-sail-and-tests`.

```bash
# Artisan
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan test

# Composer
./vendor/bin/sail composer i

```

=== pestphp/pest-plugin-agent/core rules ===

## Pest Agent Plugin

`vendor/bin/pest --agent="<code>"` runs a one-off Pest assertion without creating a test file. Use it for backend checks such as route responses, model relationships, jobs, mail, and notifications.

### ALWAYS load the skill first

Whenever the user asks you to check, verify, confirm, or "make sure" backend behavior works — for example, a route, model, job, mail, or notification — you **MUST** load the **`pest-plugin-agent` skill before doing anything else**. Do not reach for a shell command, a throwaway test file, or manual reasoning first. Load the skill, then follow it exactly.

### NEVER fight shell escaping — use SINGLE outer quotes

Inline the snippet, but wrap it in **single** quotes, not double. Single quotes tell the shell to interpret nothing, so `$variables`, `\App\Models\User`, backticks, and `!` all pass through to PHP literally — **there is nothing to escape.** Use double quotes for PHP string literals inside:


Double outer quotes are the trap the shell springs on you — `--agent="…$user…"` makes the shell interpolate `$user` to nothing. Never do that, and never hand-escape `\$`.

The one thing single quotes can't contain is a literal single quote (an apostrophe in the PHP). Only then, fall back to a file: **Write** the snippet to a `.php` file (plain body statements — no `<?php`, no `use`, fully qualified class names) and run `vendor/bin/pest --agent="$(cat /path/to/snippet.php)"`. `"$(cat …)"` passes the contents verbatim without re-parsing. The plugin resolves the test suite's `uses`/namespace itself, so the file's location does not matter (a scratch/temp path is fine — it need not live under `tests/`).

For backend usage details, load the **`pest-plugin-agent` skill**.

</laravel-boost-guidelines>
