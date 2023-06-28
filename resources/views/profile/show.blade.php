<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="text-xl font-bold text-gray-800">{{ $user->name }}</div>
                <div class="text-xl text-gray-800">{{ $user->custom_domain }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
