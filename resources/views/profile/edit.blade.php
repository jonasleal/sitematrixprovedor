<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            @if(auth()->user()->id !== 1 && !auth()->user()->hasRole('super-admin'))
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @else
                <div class="p-4 sm:p-8 bg-gray-50 border border-indigo-100 shadow-sm sm:rounded-lg flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-indigo-900">Conta Protegida</h3>
                        <p class="mt-1 text-sm text-gray-600">Por questões de segurança da infraestrutura, a conta raiz do sistema não pode ser apagada.</p>
                    </div>
                    <i class="fas fa-shield-alt text-4xl text-indigo-300"></i>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>