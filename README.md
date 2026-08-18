# Laravel NativePHP — Setup & Notes

## Installation

```bash
composer require nativephp/mobile
php artisan native:install
```

## Deactivate CSRF Token

In `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->validateCsrfTokens(except: ['*']);
})
```

## Hot Reloading

In `vite.config.js`:

```js
import { nativephpMobile, nativephpHotFile } from './vendor/nativephp/mobile/resources/js/vite-plugin.js';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            hotFile: nativephpHotFile(),
        }),
        tailwindcss(),
        nativephpMobile(),
    ]
});
```

## Create Database

```bash
touch database/database.sqlite && php artisan migrate
```

## `.env` Template

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:UXXc6Y7d+T3JXewSJ73k76X7r3YLBPnmUx1Al0W650s=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

# PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

NATIVEPHP_APP_ID=com.julianleipert.jadenovabrave
```

## Run

```bash
php artisan native:run --watch --vite
```

## Install the codebar Plugin

```bash
git clone git@github.com:codebar-ag/mobile.fileoperation.plugin.git
```

Add the plugin to `composer.json`:

```json
"codebar/mobile-toolkit": "*",

{
    "name": "codebar/mobile-toolkit",
    "type": "path",
    "url": "/Users/julian_leipert/Projects/mobile.fileoperation.plugin"
}
```

Register the plugin in the NativeServiceProvider:

```php
MobileToolkitServiceProvider::class,
```

---

## Native CRUD Bottom Sheets

### Route + Trigger

```php
Route::get('notes/native/create', [NotesController::class, 'presentNativeCreate'])
    ->name('notes.native.create');
```

```blade
<flux:button variant="primary" icon="plus" :href="route('notes.native.create')">New note</flux:button>
```

### Controller — Create

```php
use Codebar\NativeCrudFormV2\NativeCrudFormV2;

/**
 * Present the native create-note popup.
 */
public function presentNativeCreate(): RedirectResponse
{
    $this->noteForm()->presentCreate();

    return redirect()->route('notes.index');
}

/**
 * Build the native CRUD form definition for notes.
 */
protected function noteForm(): NativeCrudFormV2
{
    return NativeCrudFormV2::for(Notes::class)
        ->titles(create: 'New note', edit: 'Edit note')
        ->saveLabels(create: 'Create', edit: 'Save')
        ->field('title', 'text', 'Title')
        ->field('content', 'textarea', 'Content')
        ->relation('tags', Tags::class, 'name', 'Tags');
}
```

### Controller — Edit

```php
Route::get('notes/native/{note}/edit', [NotesController::class, 'presentNativeEdit'])
    ->name('notes.native.edit');
```

```php
public function presentNativeEdit(Notes $note): RedirectResponse
{
    $this->noteForm()->presentEdit($note->load('tags'));

    return redirect()->route('notes.index');
}
```

---

## Native List Component (Glass UI)

Create a `NativeComponents` folder / native view.

```php
Route::native('/notes-native', NotesIndex::class);
```

```php
<?php

namespace App\NativeComponents;

use App\Models\Notes;
use App\Models\Tags;
use Codebar\NativeCrudFormV2\NativeCrudFormV2;
use Illuminate\View\View;
use Native\Mobile\Attributes\On;
use Native\Mobile\Edge\NativeComponent;
use Native\Mobile\Events\NativeForm\ValueChanged;
use Native\Mobile\Facades\NativeForm;

class NotesIndex extends NativeComponent
{
    /** @var array<int, array{uuid: string, title: string, content: string, tags: string}> */
    public array $notes = [];

    public function mount(): void
    {
        $this->loadNotes();
    }

    public function refresh(): void
    {
        $this->loadNotes();
    }

    /**
     * Present the native bottom sheet to create a new note.
     */
    public function create(): void
    {
        $this->noteForm()->presentCreate();
    }

    /**
     * Present the native bottom sheet pre-filled to edit the given note.
     */
    public function edit(string $uuid): void
    {
        $note = Notes::findByUuidOrFail($uuid)->load('tags');

        $this->noteForm()->presentEdit($note);
    }

    public function delete(string $uuid): void
    {
        Notes::where('uuid', $uuid)->delete();

        $this->loadNotes();
    }

    /**
     * The plugin's PersistNativeCrudFormV2 listener is registered via
     * Event::listen(), but the checkmark's ValueChanged event is delivered
     * as a native event (only to the active screen's own #[On] handlers) —
     * it never reaches that global listener, so the plugin's normal
     * persistence never runs. Do it here instead, the same way
     * PersistNativeCrudFormV2 does.
     */
    #[On(ValueChanged::class)]
    public function onNativeFormValueChanged(string $rowId, ?string $id = null, mixed $value = null): void
    {
        if ($id === null || ! str($id)->startsWith('crud:notes:')) {
            return;
        }

        if ($rowId !== 'save') {
            NativeCrudFormV2::remember($id, $rowId, $value);

            return;
        }

        $meta = NativeCrudFormV2::meta($id);

        if ($meta === null) {
            return;
        }

        $values = NativeCrudFormV2::values($id, $meta);

        if (! NativeCrudFormV2::isComplete($meta, $values)) {
            return;
        }

        NativeCrudFormV2::persist($meta, $values);
        NativeCrudFormV2::forgetByMeta($id, $meta);

        NativeForm::dismiss();

        $this->loadNotes();
    }

    public function render(): View
    {
        return view('native.notes-index');
    }

    private function noteForm(): NativeCrudFormV2
    {
        return NativeCrudFormV2::for(Notes::class)
            ->titles(create: 'New note', edit: 'Edit note')
            ->saveLabels(create: 'Create', edit: 'Save')
            ->field('title', 'text', 'Title')
            ->field('content', 'textarea', 'Content')
            ->relation('tags', Tags::class, 'name', 'Tags');
    }

    private function loadNotes(): void
    {
        $this->notes = Notes::with('tags')->latest()->get()
            ->map(fn (Notes $note): array => [
                'uuid' => $note->uuid,
                'title' => $note->title,
                'content' => $note->content,
                'tags' => $note->tags->pluck('name')->implode(', '),
            ])
            ->all();
    }
}
```

### View — `native/notes-index.blade.php`

```blade
<native:top-bar title="Notes">
    <native:top-bar-action id="add" icon="plus" label="New note" @tap="create" />
</native:top-bar>

@if (empty($notes))
    <native:column class="w-full h-full items-center justify-center gap-2 p-8">
        <native:text class="text-lg font-semibold text-center">No notes</native:text>
        <native:text class="text-center text-secondary">Tap the + button to create your first note.</native:text>
    </native:column>
@else
    <native:refreshable @refresh="refresh" class="w-full h-full">
        <native:column class="w-full">
            @foreach ($notes as $note)
                <native:row key="{{ $note['uuid'] }}" class="items-center gap-1 px-2">
                    <native:pressable class="flex-1" @press="edit('{{ $note['uuid'] }}')">
                        <native:toolkit-list-item
                            headline="{{ $note['title'] }}"
                            supporting="{{ $note['content'] }}"
                            overline="{{ $note['tags'] }}"
                            trailingIcon="chevron-right"
                        />
                    </native:pressable>

                    <native:pressable class="p-2" @press="delete('{{ $note['uuid'] }}')">
                        <native:icon name="trash" class="text-red-500" />
                    </native:pressable>
                </native:row>

                <native:divider />
            @endforeach
        </native:column>
    </native:refreshable>
@endif
```

---

## Delete (via sheet's "…" menu)

```blade
<native:row key="{{ $note['uuid'] }}" class="items-center gap-1 px-2 border-b border-gray-200">
```

```php
/**
 * Delete the note the "…" menu's Delete item was tapped for, tear down
 * the sheet's cached field state, and dismiss it.
 */
private function deleteFromSheet(string $handle): void
{
    $meta = NativeCrudFormV2::meta($handle);

    if ($meta === null || $meta['key'] === null) {
        return;
    }

    $meta['model']::destroy($meta['key']);

    NativeCrudFormV2::forgetByMeta($handle, $meta);
    NativeForm::dismiss();

    $this->loadNotes();
}

private function noteForm(): NativeCrudFormV2
{
    return NativeCrudFormV2::for(Notes::class)
        ->titles(create: 'New note', edit: 'Edit note')
        ->saveLabels(create: 'Create', edit: 'Save')
        ->deletable('Delete note')
        ->field('title', 'text', 'Title')
        ->field('content', 'textarea', 'Content')
        ->relation('tags', Tags::class, 'name', 'Tags');
}

public function onNativeFormValueChanged(string $rowId, ?string $id = null, mixed $value = null): void
{
    if ($id === null || ! str($id)->startsWith('crud:notes:')) {
        return;
    }

    if ($rowId === NativeCrudFormV2::DELETE_ROW_ID) {
        $this->deleteFromSheet($id);

        return;
    }
    // ...
}
```

---

## Troubleshooting

**500 Server Error**
Make sure all the steps above were followed correctly.

**Vite Manifest not found**

```bash
composer update
npm install && npm update
```
