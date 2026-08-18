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
