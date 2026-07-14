@props(['type' => 'success', 'dismissible' => false])

@if(session($type))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        class="mb-4 p-4 rounded-lg text-sm flex items-start gap-3
            {{ $type === 'success' ? 'bg-green-50 border border-green-200 text-green-700' : '' }}
            {{ $type === 'error' ? 'bg-red-50 border border-red-200 text-red-700' : '' }}
            {{ $type === 'warning' ? 'bg-yellow-50 border border-yellow-200 text-yellow-700' : '' }}
            {{ $type === 'info' ? 'bg-blue-50 border border-blue-200 text-blue-700' : '' }}"
    >
        @if($type === 'success')
            <x-icon name="check" class="w-4 h-4 mt-0.5 shrink-0" />
        @elseif($type === 'error')
            <x-icon name="x" class="w-4 h-4 mt-0.5 shrink-0" />
        @elseif($type === 'warning')
            <x-icon name="alert" class="w-4 h-4 mt-0.5 shrink-0" />
        @elseif($type === 'info')
            <x-icon name="info" class="w-4 h-4 mt-0.5 shrink-0" />
        @endif
        <span class="flex-1">{{ session($type) }}</span>
        @if($dismissible)
            <button @click="show = false" class="shrink-0 opacity-50 hover:opacity-100">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        @endif
    </div>
@endif

@if($type === 'error' && $errors->any())
    <div
        x-data="{ show: true }"
        x-show="show"
        x-transition
        class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-start gap-3"
    >
        <x-icon name="x" class="w-4 h-4 mt-0.5 shrink-0" />
        <div class="flex-1">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @if($dismissible)
            <button @click="show = false" class="shrink-0 opacity-50 hover:opacity-100">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        @endif
    </div>
@endif
