<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'QuotationSystem') }} - Professional Quotation Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 antialiased">
    @include('welcome.navbar')
    @include('welcome.hero')
    @include('welcome.features')
    @include('welcome.how-it-works')
    @include('welcome.pricing')
    @include('welcome.footer')
</body>
</html>
