@props(['status'])

<span {{ $attributes->merge(['class' => "badge badge-{$status}"]) }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
