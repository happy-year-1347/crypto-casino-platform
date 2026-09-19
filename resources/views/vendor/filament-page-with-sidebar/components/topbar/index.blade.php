@props([
    'sidebar',
])

<div class="mt-6">
    <nav class="flex items-center justify-between gap-x-4 bg-gradient-to-r from-gray-800 to-gray-900 text-white px-6 py-3 rounded-md shadow-md dark:ring-white/10 overflow-x-auto">
        <div class="flex items-center gap-4">
            {{-- Logo / title area to make admin header clearer --}}
            <div class="flex items-center gap-3">
                @if(View::exists('filament::components.logo'))
                    <div class="h-8 w-auto">
                        @include('filament::components.logo')
                    </div>
                @endif
                @if ($sidebar->getTitle() != null || $sidebar->getDescription() != null)
                    <div class="hidden lg:flex flex-col leading-tight">
                        <h3 class="text-sm font-semibold text-white truncate block">
                            {{ $sidebar->getTitle() }}
                        </h3>
                        <p class="text-xs text-gray-300 flex items-center gap-x-1">
                            {{ $sidebar->getDescription() }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex-1">
            @if (count($sidebar->getNavigationItems()))
                <ul class="flex items-center justify-end gap-x-4">
                    @foreach ($sidebar->getNavigationItems() as $group)
                        @if ($groupLabel = $group->getLabel())
                            <x-filament::dropdown placement="bottom-start" teleport>
                                <x-slot name="trigger">
                                    <x-filament-panels::topbar.item :active="$group->isActive()" :icon="$group->getIcon()">
                                        {{ $groupLabel }}
                                    </x-filament-panels::topbar.item>
                                </x-slot>

                                <x-filament::dropdown.list>
                                    @foreach ($group->getItems() as $item)
                                        @php
                                            $icon = $item->getIcon();
                                            $shouldOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                        @endphp

                                        <x-filament::dropdown.list.item
                                                :badge="$item->getBadge()"
                                                :badge-color="$item->getBadgeColor()"
                                                :href="$item->getUrl()"
                                                :icon="$item->isActive() ? ($item->getActiveIcon() ?? $icon) : $icon"
                                                tag="a"
                                                :target="$shouldOpenUrlInNewTab ? '_blank' : null">
                                            {{ $item->getLabel() }}
                                        </x-filament::dropdown.list.item>
                                    @endforeach
                                </x-filament::dropdown.list>
                            </x-filament::dropdown>
                        @else
                            @foreach ($group->getItems() as $item)
                                <x-filament-page-with-sidebar::topbar.item
                                        :active="$item->isActive()"
                                        :active-icon="$item->getActiveIcon()"
                                        :badge="$item->getBadge()"
                                        :badge-color="$item->getBadgeColor()"
                                        :icon="$item->getIcon()"
                                        :should-open-url-in-new-tab="$item->shouldOpenUrlInNewTab()"
                                        :url="$item->getUrl()">
                                    {{ $item->getLabel() }}
                                </x-filament-page-with-sidebar::topbar.item>
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>
    </nav>
</div>