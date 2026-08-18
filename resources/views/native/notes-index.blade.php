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
                <native:row key="{{ $note['uuid'] }}" class="items-center gap-1 px-2 border-b border-gray-200">
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
            @endforeach
        </native:column>
    </native:refreshable>
@endif
