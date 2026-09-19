<x-filament-panels::page>
    <div class="w-full p-4 bg-gray-500 shadow-lg" style="background-color: #51515163;border-radius: 5px;">
        <h2 class="mb-5 text-3xl">World Slot</h2>
        <hr style="border-color: #4b4b4b8c;padding-bottom: 10px">
        <div class="flex justify-between w-full gap-4">
            <div class="flex flex-col gap-4 w-full justify-between">
                <button wire:click="loadProvider('slot')" class="bg-primary-600 px-3 py-2 w-full text-center">
                    <div wire:loading wire:target="loadProvider('slot')">Loading providers</div>
                    <div wire:loading.remove wire:target="loadProvider('slot')">Load providers (Slot)</div>
                </button>
                <button wire:click="loadProvider('casino')" class="bg-primary-600 px-3 py-2 w-full">
                    <div wire:loading wire:target="loadProvider('casino')">Loading providers</div>
                    <div wire:loading.remove wire:target="loadProvider('casino')">Load providers (Casino)</div>
                </button>
                <button wire:click="loadProvider('pachinko')" class="bg-primary-600 px-3 py-2 w-full">
                    <div wire:loading wire:target="loadProvider('pachinko')">Loading providers</div>
                    <div wire:loading.remove wire:target="loadProvider('pachinko')">Load providers (Pachinko)</div>
                </button>
            </div>
            <button wire:click="loadGames" class="bg-primary-500 px-3 py-2 w-full">
                <div wire:loading wire:target="loadGames">Loading games</div>
                <div wire:loading.remove wire:target="loadGames">Load games</div>
            </button>
        </div>
    </div>

    <div class="w-full p-4 bg-gray-500 shadow-lg" style="background-color: #51515163;border-radius: 5px;">
        <h2 class="mb-5 text-3xl">Updates</h2>
        <hr style="border-color: #4b4b4b8c;padding-bottom: 10px">

        @if($output)
            <div class="p-4">
                <code>
                    {!! $output !!}
                </code>
            </div>
        @endif

        <div class="flex justify-between w-full gap-4 mb-3">
            <button wire:click="runMigrate" class="bg-primary-600 px-3 py-2 w-full text-center">
                <div wire:loading wire:target="runMigrate">Running migrations</div>
                <div wire:loading.remove wire:target="runMigrate">Run migrations</div>
            </button>
        </div>

        <div class="flex justify-between w-full gap-4 mb-3">
            <button wire:click="runMigrateWithSeed" class="bg-primary-600 px-3 py-2 w-full text-center">
                <div wire:loading wire:target="runMigrateWithSeed">Running migrations with seed</div>
                <div wire:loading.remove wire:target="runMigrateWithSeed">Run migrations with seed</div>
            </button>
        </div>

        <br>
        <br>

        <form wire:submit="submit" class="mt-5">
            {{ $this->form }}

            <br>
            <x-filament::button type="submit" form="submit" class="w-full">
                Upload file
            </x-filament::button>
        </form>
    </div>



</x-filament-panels::page>
