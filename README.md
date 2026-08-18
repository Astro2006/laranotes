# LaraNotes

LaraNotes is a simple notes application built with Laravel. It lets you create, search, edit, and delete notes with a title and content.

## Features

- Create, view, edit, and delete notes (`title` + `content`).
- Full-text style search across note titles and content, with pagination (15 notes per page).
- Validation prevents empty or whitespace-only titles/content.

## Tech Stack

- [Laravel](https://laravel.com) 13 (PHP 8.3+)
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

Notes are managed through a standard resourceful route set at `/notes`:

| Method | URI | Action |
| --- | --- | --- |
| GET | `/notes` | List notes (supports `?search=`) |
| GET | `/notes/create` | Show the create form |
| POST | `/notes` | Store a new note |
| GET | `/notes/{note}` | Show a note |
| GET | `/notes/{note}/edit` | Show the edit form |
| PUT/PATCH | `/notes/{note}` | Update a note |
| DELETE | `/notes/{note}` | Delete a note |

The root URL (`/`) redirects to `/notes`.

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
