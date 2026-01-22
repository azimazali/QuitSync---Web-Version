<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Smoking Habits') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your smoking history and costs to ensure accurate savings calculations.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.smoking.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="cigarettes_per_day" :value="__('Cigarettes Per Day')" />
            <x-text-input id="cigarettes_per_day" name="cigarettes_per_day" type="number" class="mt-1 block w-full"
                :value="old('cigarettes_per_day', $user->cigarettes_per_day)" required autofocus
                autocomplete="cigarettes_per_day" />
            <x-input-error class="mt-2" :messages="$errors->get('cigarettes_per_day')" />
        </div>

        <div>
            <x-input-label for="pack_price" :value="__('Pack Price ($)')" />
            <x-text-input id="pack_price" name="pack_price" type="number" step="0.01" class="mt-1 block w-full"
                :value="old('pack_price', $user->pack_price)" required autocomplete="pack_price" />
            <x-input-error class="mt-2" :messages="$errors->get('pack_price')" />
        </div>

        <div>
            <x-input-label for="quit_date" :value="__('Quit Date')" />
            <x-text-input id="quit_date" name="quit_date" type="datetime-local" class="mt-1 block w-full"
                :value="old('quit_date', $user->quit_date ? $user->quit_date->format('Y-m-d\TH:i') : '')"
                autocomplete="quit_date" />
            <x-input-error class="mt-2" :messages="$errors->get('quit_date')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>