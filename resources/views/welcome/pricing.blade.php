<section id="pricing" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16" data-animate="fade-up">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Simple, Transparent Pricing</h2>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">Choose the plan that fits your team. All plans include core features.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            @forelse($packages as $index => $package)
                @php
                    $isPopular = $index === 1;
                @endphp
                <div class="tilt-card relative p-8 rounded-2xl border-2 transition-all duration-300 {{ $isPopular ? 'border-indigo-600 popular-glow scale-105' : 'border-gray-100 hover:border-indigo-200 hover:shadow-lg' }}" data-animate="fade-up" style="--d: {{ $index * 0.1 }}s">
                    @if($isPopular)
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-indigo-600 text-white text-xs font-semibold rounded-full">Most Popular</div>
                    @endif

                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $package->name }}</h3>
                    <p class="text-gray-500 text-sm mb-6">{{ $package->description }}</p>

                    <div class="mb-6">
                        <span class="text-4xl font-extrabold text-gray-900">${{ number_format($package->price, 2) }}</span>
                        <span class="text-gray-400 text-sm">/ month</span>
                    </div>

                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm text-gray-600"><x-icon name="check" class="w-4 h-4 text-indigo-500 shrink-0" />{{ $package->max_users }} Team {{ Str::plural('User', $package->max_users) }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><x-icon name="check" class="w-4 h-4 text-indigo-500 shrink-0" />{{ $package->max_clients }} {{ Str::plural('Client', $package->max_clients) }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><x-icon name="check" class="w-4 h-4 text-indigo-500 shrink-0" />{{ number_format($package->max_quotations) }} {{ Str::plural('Quotation', $package->max_quotations) }}</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><x-icon name="check" class="w-4 h-4 text-indigo-500 shrink-0" />{{ $package->duration_days }}-day billing cycle</li>
                        <li class="flex items-center gap-3 text-sm text-gray-600"><x-icon name="check" class="w-4 h-4 text-indigo-500 shrink-0" />PDF export & reports</li>
                    </ul>

                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="block w-full py-3 text-center text-sm font-semibold rounded-xl transition-all duration-300 {{ $isPopular ? 'bg-indigo-600 text-white hover:bg-indigo-700 hover:shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Get Started
                        </a>
                    @endif
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-gray-400" data-animate="fade-up">
                    <x-icon name="package" class="w-12 h-12 mx-auto mb-4 opacity-50" />
                    <p>Packages coming soon. Contact us for details.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
