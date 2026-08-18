# LaraNotes

LaraNotes is a simple notes application built with Laravel. It lets you create, search, edit, and delete notes with a title and content.

## Features

- Create, view, edit, and delete notes (`title` + `content`).
- Full-text style search across note titles and content, with pagination (15 notes per page).
- Validation prevents empty or whitespace-only titles/content.

## Tech Stack

- [Laravel](https://laravel.com) 13 (PHP 8.3+)
- [Livewire](https://livewire.laravel.com) 4, including single-file components, for the `/lw/notes` routes
- [Flux UI](https://fluxui.dev) (Pro) for components
- [Tailwind CSS](https://tailwindcss.com) v4
- [Vite](https://vitejs.dev) for asset bundling
- [Pest](https://pestphp.com) for testing

## Getting Started

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate

npm run build
```

During development, run the app and asset watcher together:

```bash
composer run dev
```

This starts the Laravel server, queue listener, log tailer (`pail`), and Vite dev server concurrently. The app is then available at the URL shown in your terminal (typically `http://localhost:8000`).

## Routes

Notes can be managed through two parallel implementations that share the same `Notes`/`Tags` models, layout, and language switcher, but are built differently. The root URL (`/`) redirects to `/notes`.

### Classic — `/notes`

A standard resourceful route set backed by `NotesController` and Blade views (full page loads, form posts). The create/edit form embeds a Livewire component (`<x-note-form>`) for the tag picker, and saves notes directly via `Notes::create()` / `$note->update()` using a plain `flux:textarea` for content.

| Method | URI | Route name | Action |
| --- | --- | --- | --- |
| GET | `/notes` | `notes.index` | List notes (supports `?search=`) |
| GET | `/notes/create` | `notes.create` | Show the create form |
| POST | `/notes` | `notes.store` | Store a new note |
| GET | `/notes/{note}` | `notes.show` | Show a note |
| GET | `/notes/{note}/edit` | `notes.edit` | Show the edit form |
| PUT/PATCH | `/notes/{note}` | `notes.update` | Update a note |
| DELETE | `/notes/{note}` | `notes.destroy` | Delete a note |

### Livewire — `/lw/notes`

A fully Livewire-driven equivalent registered with `Route::livewire()`, built as single-file components under `resources/views/pages/lw/notes/⚡*.blade.php`. Links use `wire:navigate` for SPA-style transitions (no full page reload), content is edited with a rich-text `flux:editor`, and saves go through `App\Actions\Notes\CreateNote` / `UpdateNote`, which sanitize the submitted HTML (`NoteContentHelper::sanitize()`) before persisting.

| Method | URI | Route name | Action |
| --- | --- | --- | --- |
| GET | `/lw/notes` | `lw.notes.index` | List notes (supports `?search=`) |
| GET | `/lw/notes/create` | `lw.notes.create` | Show the create form |
| GET | `/lw/notes/{note}` | `lw.notes.show` | Show a note |
| GET | `/lw/notes/{note}/edit` | `lw.notes.edit` | Show the edit form |

Livewire handles create/update/delete as component actions (`save`, `delete`) over the wire rather than as separate HTTP verbs, so there are no matching POST/PUT/DELETE routes.

### What changes between the two

- **URLs & route names**: `/notes` (`notes.*`) vs `/lw/notes` (`lw.*`).
- **Navigation**: full page loads vs `wire:navigate` (no full reload).
- **Content editor**: plain `flux:textarea` vs rich-text `flux:editor`.
- **Persistence**: direct Eloquent calls vs dedicated `CreateNote`/`UpdateNote` actions that HTML-sanitize the content before saving.
- **Delete feedback**: a session-flashed toast read by Alpine after the redirect vs `Flux::toast()` called directly from the component.
- Search, pagination, tag assignment (including the Tags column in the desktop table), and the delete-confirmation modal work the same way in both.

## Localization

A language switcher (`<x-language-switcher>`) in the header lets users pick between English (`en`) and German (`de`). The choice is stored in the session by `GET /locale/{locale}` (`locale.set`) and applied on every request by the `SetLocale` middleware — it applies to both `/notes` and `/lw/notes`.

## Testing

```bash
php artisan test --compact
```

## Code Style

```bash
vendor/bin/pint
```

## Agentic Development

This project uses [Laravel Boost](https://laravel.com/docs/ai) and repo-specific rules under `.ai/rules` to guide AI coding agents (Claude Code, Cursor, GitHub Copilot, etc.) toward this codebase's conventions.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
