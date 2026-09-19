<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        <br>
        <div class="flex gap-3">
            <x-filament::button type="submit" form="submit">
                Save settings
            </x-filament::button>

            <x-filament::button type="button" color="gray" wire:click="testConnection">
                Test connection
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
