function initTagInputs() {
    document.querySelectorAll('[data-tag-input]').forEach((root) => {
        if (root.dataset.tagInputInitialized) {
            return;
        }

        root.dataset.tagInputInitialized = 'true';

        const available = JSON.parse(root.dataset.available || '[]');
        const chips = root.querySelector('[data-tag-chips]');
        const search = root.querySelector('[data-tag-search]');
        const dropdown = root.querySelector('[data-tag-dropdown]');
        const hidden = root.querySelector('[data-tag-value]');

        let selected = Array.from(root.querySelectorAll('[data-tag-chip]')).map((chip) => chip.dataset.value);
        let activeIndex = -1;

        const isSelected = (name) => selected.some((tag) => tag.toLowerCase() === name.toLowerCase());

        const syncHiddenInput = () => {
            hidden.value = selected.join(',');
        };

        const updatePlaceholder = () => {
            search.placeholder = selected.length ? '' : 'Add tags…';
        };

        const createChipElement = (name) => {
            const chip = document.createElement('span');
            chip.dataset.tagChip = 'true';
            chip.dataset.value = name;
            chip.className = 'inline-flex items-center gap-1 rounded-full bg-indigo-50 py-0.5 pl-2 pr-1 text-xs font-medium text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400';

            const label = document.createElement('span');
            label.dataset.tagChipLabel = 'true';
            label.textContent = name;
            chip.append(label);

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.dataset.tagChipRemove = 'true';
            remove.setAttribute('aria-label', `Remove tag ${name}`);
            remove.className = 'rounded-full p-0.5 hover:bg-indigo-100 dark:hover:bg-indigo-500/20';
            remove.innerHTML = '<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-3"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>';
            chip.append(remove);

            return chip;
        };

        const renderChips = () => {
            root.querySelectorAll('[data-tag-chip]').forEach((chip) => chip.remove());

            selected.forEach((name) => {
                chips.insertBefore(createChipElement(name), search);
            });

            updatePlaceholder();
        };

        const closeDropdown = () => {
            dropdown.classList.add('hidden');
            dropdown.innerHTML = '';
            activeIndex = -1;
            search.setAttribute('aria-expanded', 'false');
        };

        const highlightActive = () => {
            dropdown.querySelectorAll('[data-tag-option]').forEach((option, index) => {
                option.classList.toggle('bg-indigo-50', index === activeIndex);
                option.classList.toggle('dark:bg-indigo-500/10', index === activeIndex);
            });
        };

        const openDropdown = () => {
            const query = search.value.trim();
            const matches = available.filter((name) => ! isSelected(name) && name.toLowerCase().includes(query.toLowerCase()));
            const exactMatch = available.some((name) => name.toLowerCase() === query.toLowerCase());

            dropdown.innerHTML = '';
            activeIndex = -1;

            if (query && ! exactMatch) {
                const create = document.createElement('li');
                create.dataset.tagOption = 'true';
                create.dataset.value = query;
                create.setAttribute('role', 'option');
                create.className = 'cursor-pointer px-3 py-1.5 text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-500/10';
                create.textContent = `Create tag "${query}"`;
                dropdown.append(create);
            }

            matches.forEach((name) => {
                const option = document.createElement('li');
                option.dataset.tagOption = 'true';
                option.dataset.value = name;
                option.setAttribute('role', 'option');
                option.className = 'cursor-pointer px-3 py-1.5 text-gray-900 hover:bg-gray-100 dark:text-zinc-100 dark:hover:bg-zinc-800';
                option.textContent = name;
                dropdown.append(option);
            });

            if (! dropdown.children.length) {
                closeDropdown();

                return;
            }

            dropdown.classList.remove('hidden');
            search.setAttribute('aria-expanded', 'true');
        };

        const addTag = (rawName) => {
            const name = rawName.trim();

            if (! name || isSelected(name)) {
                return;
            }

            selected.push(name);
            renderChips();
            syncHiddenInput();
            search.value = '';
            closeDropdown();
            search.focus();
        };

        const removeTag = (name) => {
            selected = selected.filter((tag) => tag.toLowerCase() !== name.toLowerCase());
            renderChips();
            syncHiddenInput();
        };

        const moveActive = (delta) => {
            const options = dropdown.querySelectorAll('[data-tag-option]');

            if (! options.length) {
                return;
            }

            activeIndex = (activeIndex + delta + options.length) % options.length;
            highlightActive();
            options[activeIndex].scrollIntoView({block: 'nearest'});
        };

        const selectActiveOrTyped = () => {
            const options = dropdown.querySelectorAll('[data-tag-option]');

            if (activeIndex > -1 && options[activeIndex]) {
                addTag(options[activeIndex].dataset.value);

                return;
            }

            if (search.value.trim()) {
                addTag(search.value);
            }
        };

        search.addEventListener('input', openDropdown);
        search.addEventListener('focus', openDropdown);

        search.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                dropdown.classList.contains('hidden') ? openDropdown() : moveActive(1);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                moveActive(-1);
            } else if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                selectActiveOrTyped();
            } else if (event.key === 'Escape') {
                closeDropdown();
            } else if (event.key === 'Backspace' && search.value === '' && selected.length) {
                removeTag(selected[selected.length - 1]);
            }
        });

        dropdown.addEventListener('mousedown', (event) => {
            const option = event.target.closest('[data-tag-option]');

            if (option) {
                event.preventDefault();
                addTag(option.dataset.value);
            }
        });

        chips.addEventListener('click', (event) => {
            const remove = event.target.closest('[data-tag-chip-remove]');

            if (remove) {
                removeTag(remove.closest('[data-tag-chip]').dataset.value);
            }
        });

        document.addEventListener('click', (event) => {
            if (! root.contains(event.target)) {
                closeDropdown();
            }
        });

        syncHiddenInput();
        updatePlaceholder();
    });
}

document.addEventListener('DOMContentLoaded', initTagInputs);
