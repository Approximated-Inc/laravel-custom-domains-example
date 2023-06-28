<x-app-layout>
<div class="text-xl">This should only load on {{ $user->custom_domain }}</div>
<div class="text-xl">{{ $user->name }}</div>
</x-app-layout>
