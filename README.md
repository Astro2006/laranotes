# Laravel NativePHP — Setup & Notes

## 1. Installation

```bash
composer require nativephp/mobile
php artisan native:install
```

`native:install` scaffolds the native config, publishes assets, and sets up the mobile build tooling on top of an existing Laravel app.

## 2. Deactivate CSRF Tokens

NativePHP's mobile shell doesn't carry a browser session/cookie the same way a normal web request does, so Laravel's CSRF middleware will reject its requests unless you exempt them.

In `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->validateCsrfTokens(except: ['*']);
})
```

## 3. Hot Reloading

Wire NativePHP's Vite plugin into `vite.config.js` so changes reload inside the native shell instead of requiring a full rebuild:

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

## 4. Create the Database

NativePHP apps typically run on SQLite locally:

```bash
touch database/database.sqlite && php artisan migrate
```

## 5. `.env` Template

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

`NATIVEPHP_APP_ID` is the bundle identifier used when the app is built for iOS/Android — it needs to match what's registered in your Apple Developer / Play Console setup.

## 6. Run the App

```bash
php artisan native:run --watch --vite
```

`--watch` keeps the process alive and rebuilds on file changes; `--vite` runs the Vite dev server alongside it for hot reloading.

---

## 7. Installing a codebar Plugin

NativePHP plugins are just Composer packages. To develop against a local, unpublished plugin (like an internal codebar package), clone it and point Composer at the local path instead of a registry:

```bash
git clone git@github.com:codebar-ag/mobile.fileoperation.plugin.git
```

**`composer.json`** — add the dependency and a path repository pointing to your local clone:

```json
"require": {
    "codebar/mobile-toolkit": "*"
},
"repositories": [
    {
        "name": "codebar/mobile-toolkit",
        "type": "path",
        "url": "/Users/julian_leipert/Projects/mobile.fileoperation.plugin"
    }
]
```

**Register the plugin's service provider** so Laravel loads it:

```php
MobileToolkitServiceProvider::class,
```

**Publish, register, and verify the plugin:**

```bash
php artisan vendor:publish --tag=nativephp-plugins-provider
php artisan native:plugin:register vendor/nativephp-plugin-name
php artisan native:plugin:list
```

- `vendor:publish` copies the plugin's provider stub into your app so you can register it.
- `native:plugin:register` tells NativePHP's native build to actually bundle the plugin into the compiled app.
- `native:plugin:list` confirms which plugins are currently registered — useful for sanity-checking after adding a new one.

---

## 8. Native CRUD Bottom Sheets

NativePHP can present a native (non-web) bottom sheet form for creating/editing a model, backed by the `NativeCrudFormV2` builder from the `codebar/mobile-toolkit` plugin. You define the form once as a reusable "form definition," then call `presentCreate()` / `presentEdit()` on it to trigger the native sheet.

### Route + Trigger Button

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

Note the pattern: visiting the route doesn't render a page — it presents the native sheet as a side effect, then redirects back to the underlying index route.

### Controller — Edit

Same idea, but pre-filled with an existing model:

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

## 9. Native List Component (Glass UI)

Beyond triggering sheets from a web view, NativePHP lets you build entire screens as native components — no HTML/browser rendering involved. These live in a `NativeComponents` folder and render via a `native.*.blade.php` view using `<native:*>` tags (which map to real native UI elements, not HTML).

Set the app's native start screen:

```env
NATIVEPHP_START_URL=/notes-native
```

Register the native route:

```php
Route::native('/notes-native', NotesIndex::class);
```

### The Component Class

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

**Key thing to understand about `onNativeFormValueChanged`:** the CRUD form plugin normally persists the form itself through a global `Event::listen()` hook. But the "save" tap is delivered as a *native* event, which only reaches `#[On(...)]` handlers defined on the currently active screen — it never reaches the plugin's global listener. So this method manually re-implements the plugin's own persistence logic (remember field values as they change → on save, load the cached meta/values → validate completeness → persist → clean up → dismiss the sheet → refresh the list).

### The View — `native/notes-index.blade.php`

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

This gives you: an empty state, pull-to-refresh (`<native:refreshable>`), and per-row tap-to-edit plus a trailing trash icon for inline delete — all rendered as native views, not a WebView.

---

## 10. Delete via the Sheet's "…" Menu

Instead of (or in addition to) the inline trash icon, you can add a "Delete" action inside the CRUD sheet's own overflow menu. This requires three changes:

**1. Give the row a bottom border** (cosmetic, matches the tags screen below):

```blade
<native:row key="{{ $note['uuid'] }}" class="items-center gap-1 px-2 border-b border-gray-200">
```

**2. Mark the form as deletable**, which adds the "Delete" option to the sheet's menu:

```php
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
```

**3. Handle the delete row ID** in `onNativeFormValueChanged`, and add the `deleteFromSheet` helper it calls:

```php
public function onNativeFormValueChanged(string $rowId, ?string $id = null, mixed $value = null): void
{
    if ($id === null || ! str($id)->startsWith('crud:notes:')) {
        return;
    }

    if ($rowId === NativeCrudFormV2::DELETE_ROW_ID) {
        $this->deleteFromSheet($id);

        return;
    }

    // ...existing save/remember logic continues below
}

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
```

`deleteFromSheet` mirrors the save path: look up the sheet's cached meta (which model + which key it's editing), destroy the record, clear the cached form state, dismiss the sheet, and refresh the list.

---

## 11. Native Bottom Tab Navigation

To show multiple native screens behind a shared bottom tab bar, define a **native layout** class and group your native routes under it. Layout classes live alongside your screens, e.g. under `App\NativeLayouts`. Tab icons use SF Symbols names.

### Register the Tab Group

```php
Route::nativeGroup(MainTabsLayout::class, function (): void {
    Route::native('/notes-native', NotesIndex::class);
    Route::native('/tags-native', TagsIndex::class);
});
```

### The Layout

```php
<?php

namespace App\NativeLayouts;

use Native\Mobile\Edge\Layouts\Builders\Tab;
use Native\Mobile\Edge\Layouts\Builders\TabBar;
use Native\Mobile\Edge\Layouts\NativeLayout;
use Native\Mobile\Edge\NativeComponent;

class MainTabsLayout extends NativeLayout
{
    public function usesNativeChrome(): bool
    {
        return true;
    }

    public function tabBar(NativeComponent $screen): ?TabBar
    {
        return TabBar::make()
            ->add(Tab::link('Notes', '/notes-native', icon: 'file-text'))
            ->add(Tab::link('Tags', '/tags-native', icon: 'tag'));
    }
}
```

`usesNativeChrome()` returning `true` tells NativePHP to render the tab bar (and other chrome) using native OS components rather than a web-rendered equivalent.

### A Second Screen: Tags

Same pattern as `NotesIndex`, but simpler (no relation field) and includes the delete-from-sheet handling from the start.

**View — `native/tags-index.blade.php`:**

```blade
<native:top-bar title="Tags">
    <native:top-bar-action id="add" icon="plus" label="New tag" @tap="create" />
</native:top-bar>

@if (empty($tags))
    <native:column class="w-full h-full items-center justify-center gap-2 p-8">
        <native:text class="text-lg font-semibold text-center">No tags</native:text>
        <native:text class="text-center text-secondary">Tap the + button to create your first tag.</native:text>
    </native:column>
@else
    <native:refreshable @refresh="refresh" class="w-full h-full">
        <native:column class="w-full">
            @foreach ($tags as $tag)
                <native:row key="{{ $tag['uuid'] }}" class="items-center gap-1 px-2 border-b border-gray-200">
                    <native:pressable class="flex-1" @press="edit('{{ $tag['uuid'] }}')">
                        <native:toolkit-list-item
                            headline="{{ $tag['name'] }}"
                            supporting="{{ $tag['notesCount'] }} {{ $tag['notesCount'] === 1 ? 'note' : 'notes' }}"
                            trailingIcon="chevron-right"
                        />
                    </native:pressable>

                    <native:pressable class="p-2" @press="delete('{{ $tag['uuid'] }}')">
                        <native:icon name="trash" class="text-red-500" />
                    </native:pressable>
                </native:row>
            @endforeach
        </native:column>
    </native:refreshable>
@endif
```

**Component — `App\NativeComponents\TagsIndex`:**

```php
<?php

namespace App\NativeComponents;

use App\Models\Tags;
use Codebar\NativeCrudFormV2\NativeCrudFormV2;
use Illuminate\View\View;
use Native\Mobile\Attributes\On;
use Native\Mobile\Edge\NativeComponent;
use Native\Mobile\Events\NativeForm\ValueChanged;
use Native\Mobile\Facades\NativeForm;

class TagsIndex extends NativeComponent
{
    /** @var array<int, array{uuid: string, name: string, notesCount: int}> */
    public array $tags = [];

    public function mount(): void
    {
        $this->loadTags();
    }

    public function refresh(): void
    {
        $this->loadTags();
    }

    /**
     * Present the native bottom sheet to create a new tag.
     */
    public function create(): void
    {
        $this->tagForm()->presentCreate();
    }

    /**
     * Present the native bottom sheet pre-filled to edit the given tag.
     */
    public function edit(string $uuid): void
    {
        $tag = Tags::findByUuidOrFail($uuid);

        $this->tagForm()->presentEdit($tag);
    }

    public function delete(string $uuid): void
    {
        Tags::where('uuid', $uuid)->delete();

        $this->loadTags();
    }

    /**
     * Same pattern as NotesIndex::onNativeFormValueChanged — the checkmark's
     * ValueChanged event is delivered natively to this screen's own #[On]
     * handler rather than the plugin's global listener, so persistence is
     * handled here.
     */
    #[On(ValueChanged::class)]
    public function onNativeFormValueChanged(string $rowId, ?string $id = null, mixed $value = null): void
    {
        if ($id === null || ! str($id)->startsWith('crud:tags:')) {
            return;
        }

        if ($rowId === NativeCrudFormV2::DELETE_ROW_ID) {
            $this->deleteFromSheet($id);

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

        $this->loadTags();
    }

    public function render(): View
    {
        return view('native.tags-index');
    }

    private function tagForm(): NativeCrudFormV2
    {
        return NativeCrudFormV2::for(Tags::class)
            ->titles(create: 'New tag', edit: 'Edit tag')
            ->saveLabels(create: 'Create', edit: 'Save')
            ->deletable('Delete tag')
            ->field('name', 'text', 'Name');
    }

    /**
     * Delete the tag the "…" menu's Delete item was tapped for, tear down
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

        $this->loadTags();
    }

    private function loadTags(): void
    {
        $this->tags = Tags::withCount('notes')->orderBy('name')->get()
            ->map(fn (Tags $tag): array => [
                'uuid' => $tag->uuid,
                'name' => $tag->name,
                'notesCount' => $tag->notes_count,
            ])
            ->all();
    }
}
```

Note `notesCount` comes from `withCount('notes')`, which adds a `notes_count` attribute via a subquery — cheaper than loading and counting the relation in PHP.

---

## 12. Troubleshooting

**500 Server Error**
Double-check every setup step above was followed — CSRF exemption, `.env` values, and DB migration are the usual culprits.

**Vite Manifest not found**

```bash
composer update
npm install && npm update
```

This usually means dependencies are out of sync after a fresh clone or a NativePHP version bump — reinstalling both PHP and JS deps regenerates the manifest Vite expects.
