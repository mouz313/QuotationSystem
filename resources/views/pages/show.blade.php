@extends('layouts.guest')
@section('title', $page->title)
@section('content')
<div class="max-w-3xl mx-auto py-12 px-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $page->title }}</h1>
    @if($page->meta_description)
        <p class="text-gray-500 text-sm mb-6">{{ $page->meta_description }}</p>
    @endif
    <div class="prose prose-gray max-w-none">{!! $page->content !!}</div>
</div>
@endsection
