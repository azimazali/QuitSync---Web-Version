<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('View Smoking Log') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Quantity -->
                    <div class="mb-4">
                        <x-input-label for="quantity" :value="__('Cigarettes Smoked')" />
                        <div class="text-lg font-bold text-gray-800 mt-1">{{ $smokingLog->quantity }}</div>
                    </div>

                    <!-- Date/Time -->
                    <div class="mb-4">
                        <x-input-label for="smoked_at" :value="__('Time')" />
                        <div class="text-gray-700 mt-1">{{ $smokingLog->smoked_at->format('M d, Y, h:i A') }}</div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-4">
                        <x-input-label for="notes" :value="__('Notes')" />
                        <div class="bg-gray-50 p-3 rounded-md border border-gray-200 mt-1 text-gray-600 italic">
                            {{ $smokingLog->notes ?? 'No notes provided.' }}
                        </div>
                    </div>

                     <!-- Location (Optional) -->
                     @if($smokingLog->address)
                     <div class="mb-4">
                         <x-input-label :value="__('Location')" />
                         <div class="text-sm text-gray-500 mt-1">{{ $smokingLog->address }}</div>
                     </div>
                     @endif

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('dashboard') }}"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                            &larr; {{ __('Back to Dashboard') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>