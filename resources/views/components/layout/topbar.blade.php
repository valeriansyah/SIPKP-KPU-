<header class="bg-surface border-b border-border h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 shadow-sm z-10">
    <div class="flex items-center gap-4">
        <!-- Mobile menu button -->
        <button id="mobile-menu-btn" class="lg:hidden text-text-secondary hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary rounded-md p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Breadcrumb / Page Title -->
        <div class="hidden sm:block">
            <h2 class="text-lg font-semibold text-text">@yield('title', 'Dashboard')</h2>
        </div>
    </div>

    <!-- Right Side Actions -->
    <div class="flex items-center gap-4">
        @auth
            <!-- District Info if applicable -->
            @if(auth()->user()->district)
            <div class="hidden md:flex items-center text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded-full border border-gray-200 shadow-inner">
                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ auth()->user()->district->name }}
            </div>
            @endif
            
            <!-- User Menu -->
            <div class="relative">
                <button class="flex items-center gap-2 focus:outline-none hover:bg-gray-50 p-1 rounded-md transition-colors">
                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shadow-sm border border-primary-light">
                        {{ substr(auth()->user()->full_name, 0, 1) }}
                    </div>
                    <span class="text-sm font-medium text-text hidden sm:block">{{ auth()->user()->full_name }}</span>
                    <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        @else
            <a href="{{ route('login') }}" class="text-sm font-medium text-primary hover:text-primary-dark">Login</a>
        @endauth
    </div>
</header>
