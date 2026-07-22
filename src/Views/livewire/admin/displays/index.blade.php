<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('kds::kds.displays') }}</h2>
            <p class="text-gray-500 text-sm mt-1">{{ __('kds::kds.manage_displays') }}</p>
        </div>
        <button wire:click="openCreateModal"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>{{ __('kds::kds.add_display') }}</span>
        </button>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 p-4 mb-6 shadow-sm">
        <div class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase">{{ __('kds::kds.search') }}</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('kds::kds.search_placeholder') }}"
                        class="w-full text-right pl-3 pr-10 py-2 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="w-full sm:w-auto sm:min-w-[160px]">
                <label class="block text-xs font-bold text-gray-400 mb-1.5 uppercase">{{ __('kds::kds.display_type') }}</label>
                <select wire:model.live="typeFilter"
                    class="w-full py-2 px-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white">
                    <option value="">{{ __('kds::kds.display_type') }}</option>
                    <option value="kitchen">{{ __('kds::kds.kitchen') }}</option>
                    <option value="workshop">{{ __('kds::kds.workshop') }}</option>
                    <option value="bar">{{ __('kds::kds.bar') }}</option>
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">{{ __('kds::kds.display_name') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">{{ __('kds::kds.display_type') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">{{ __('kds::kds.location') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">{{ __('kds::kds.display_status') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase">{{ __('kds::kds.last_heartbeat') }}</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase text-center">{{ __('kds::kds.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($displays as $display)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                {{ $display->name }}
                                @if($display->name_ar)
                                    <span class="block text-xs text-gray-500">{{ $display->name_ar }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                @php
                                    $typeLabels = ['kitchen' => __('kds::kds.kitchen'), 'workshop' => __('kds::kds.workshop'), 'bar' => __('kds::kds.bar')];
                                @endphp
                                {{ $typeLabels[$display->display_type] ?? $display->display_type }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $display->location ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'online' => 'bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400',
                                        'offline' => 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400',
                                        'maintenance' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-950/30 dark:text-yellow-400',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$display->status] ?? $statusClasses['online'] }}">
                                    {{ __('kds::kds.' . $display->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $display->last_heartbeat ? $display->last_heartbeat->diffForHumans() : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.kds.screen', $display->id) }}" title="{{ __('kds::kds.view_screen') }}"
                                        class="p-2 text-gray-500 hover:text-green-600 dark:hover:text-green-400 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <button wire:click="openEditModal({{ $display->id }})" title="{{ __('kds::kds.edit_display') }}"
                                        class="p-2 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="$dispatch('swal:confirm', {
                                            title: '{{ __('kds::kds.delete') }}',
                                            text: '{{ __('kds::kds.delete_confirm') }}',
                                            onConfirm: 'delete',
                                            params: { id: {{ $display->id }} }
                                        })"
                                        title="{{ __('kds::kds.delete') }}"
                                        class="p-2 text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <span>{{ __('kds::kds.no_displays') }}</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($displays->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                {{ $displays->links() }}
            </div>
        @endif
    </div>

    <div x-data="{ open: @entangle('showFormModal') }" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="open = false" class="fixed inset-0 bg-gray-500/75 dark:bg-gray-950/75 transition-opacity"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-middle bg-white dark:bg-gray-900 rounded-xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-800">
                <form wire:submit.prevent="save">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6 border-b border-gray-50 dark:border-gray-800 pb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $displayId ? __('kds::kds.edit_display') : __('kds::kds.add_display') }}
                            </h3>
                            <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('kds::kds.display_name') }} *</label>
                                <input type="text" wire:model="name" class="w-full py-2 px-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white">
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('kds::kds.display_name_ar') }}</label>
                                <input type="text" wire:model="name_ar" class="w-full py-2 px-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('kds::kds.display_type') }} *</label>
                                <select wire:model="display_type" class="w-full py-2 px-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white">
                                    <option value="kitchen">{{ __('kds::kds.kitchen') }}</option>
                                    <option value="workshop">{{ __('kds::kds.workshop') }}</option>
                                    <option value="bar">{{ __('kds::kds.bar') }}</option>
                                </select>
                                @error('display_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('kds::kds.location') }}</label>
                                <input type="text" wire:model="location" class="w-full py-2 px-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('kds::kds.display_status') }} *</label>
                                <select wire:model="status" class="w-full py-2 px-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-white">
                                    <option value="online">{{ __('kds::kds.online') }}</option>
                                    <option value="offline">{{ __('kds::kds.offline') }}</option>
                                    <option value="maintenance">{{ __('kds::kds.maintenance') }}</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-2">
                        <button type="button" @click="open = false" class="px-4 py-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            {{ __('kds::kds.cancel') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                            {{ __('kds::kds.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
