<header class="bg-surface border-b border-gray-100 h-20 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 shadow-sm z-40 sticky top-0 relative">
    <div class="flex items-center gap-4">
        <!-- Mobile menu button -->
        <button id="mobile-menu-btn" class="lg:hidden text-gray-500 hover:text-primary hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-primary rounded-lg p-2 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Breadcrumb / Page Title -->
        <div class="hidden sm:block">
            <h2 class="text-lg font-bold text-gray-800">@yield('title', 'Dashboard')</h2>
        </div>
    </div>

    <!-- Right Side Actions -->
    <div class="flex items-center gap-4 lg:gap-6">
        @auth
            @php
                $roleName = auth()->user()->role ? strtolower(auth()->user()->role->role_name) : '';
                
                // Color mapping per Step 5
                $roleColor = 'bg-gray-500';
                $roleTextColor = 'text-gray-500';
                
                if (str_contains($roleName, 'pelapor')) {
                    $roleColor = 'bg-red-500';
                    $roleTextColor = 'text-red-600';
                    $profileRoute = route('pelapor.profile.edit');
                } elseif (str_contains($roleName, 'sub operator')) {
                    $roleColor = 'bg-green-500';
                    $roleTextColor = 'text-green-600';
                    $profileRoute = '#';
                } elseif (str_contains($roleName, 'operator provinsi') || str_contains($roleName, 'operator')) {
                    $roleColor = 'bg-blue-500';
                    $roleTextColor = 'text-blue-600';
                    $profileRoute = '#';
                }
                
                $avatarUrl = auth()->user()->profile_photo_url ?? null;
                $initial = strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1));
            @endphp
            
            <!-- User Menu -->
            <div class="relative" id="account-dropdown-container">
                <button id="account-menu-btn" class="flex items-center gap-3 focus:outline-none hover:bg-gray-50 p-1.5 pr-3 rounded-xl border border-transparent hover:border-gray-200 transition-all duration-200">
                    
                    <!-- Avatar with Role Indicator -->
                    <x-ui.avatar :user="auth()->user()" size="md" :showRole="true" />
                    
                    <!-- Text Content (Hidden on Mobile, shown on Tablet/Desktop) -->
                    <div class="hidden md:flex flex-col items-start text-left">
                        <span class="text-sm font-bold text-gray-900 leading-tight">{{ auth()->user()->full_name ?? 'Guest User' }}</span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">{{ auth()->user()->role->role_name ?? 'User' }}</span>
                        </div>
                    </div>
                    
                    <svg class="w-4 h-4 text-gray-400 hidden md:block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="account-dropdown" class="hidden absolute right-0 top-[calc(100%+8px)] w-72 bg-white rounded-xl shadow-xl border border-gray-100 z-[100] transform transition duration-200 ease-out origin-top-right">
                    <!-- Dropdown Header -->
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50 rounded-t-xl">
                        <div class="flex items-center gap-3">
                            <x-ui.avatar :user="auth()->user()" size="lg" />
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900">{{ auth()->user()->full_name ?? 'Guest User' }}</span>
                                <span class="text-xs font-bold {{ $roleTextColor }} mt-0.5">{{ auth()->user()->role->role_name ?? 'User' }}</span>
                                @if(auth()->user()->district)
                                    <span class="text-[11px] text-gray-500 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Wilayah: {{ auth()->user()->district->name }}
                                    </span>
                                @elseif(str_contains($roleName, 'operator'))
                                    <span class="text-[11px] text-gray-500 mt-1 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                        Akses: Global Monitoring
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="border-t border-gray-100 py-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-bold flex items-center gap-3 rounded-b-xl">
                                <span class="text-lg">🚪</span> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const btn = document.getElementById('account-menu-btn');
                    const dropdown = document.getElementById('account-dropdown');
                    
                    if (btn && dropdown) {
                        btn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            dropdown.classList.toggle('hidden');
                        });
                        
                        document.addEventListener('click', function(e) {
                            if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
                                dropdown.classList.add('hidden');
                            }
                        });
                    }
                });
            </script>
        @else
            <a href="{{ route('login') }}" class="text-sm font-bold text-primary hover:text-primary-dark bg-red-50 hover:bg-red-100 px-4 py-2 rounded-lg transition-colors">Login</a>
        @endauth
    </div>
</header>
