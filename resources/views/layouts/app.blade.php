<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="description" content="아마추어 스포츠 팀을 만들고, 경기를 관리하며, 지역 랭킹을 확인하세요">

        <title>{{ config('app.name', 'AllSports') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Static CSS/JS (No Vite) -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                50: '#eff6ff',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                            }
                        }
                    }
                }
            }
        </script>
        <style>
            /* Custom AllSports styles */
            .btn-primary {
                @apply bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200;
            }
            .btn-secondary {
                @apply bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200;
            }
            .form-input {
                @apply block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500;
            }
            .form-label {
                @apply block text-sm font-medium text-gray-700 mb-1;
            }
            .card {
                @apply bg-white shadow-md rounded-lg p-6;
            }
            .navbar {
                @apply bg-white shadow-sm border-b border-gray-200;
            }
        </style>
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen">
            <!-- Mobile Layout (sm 미만) -->
            <div class="sm:hidden">
                <!-- Mobile Header -->
                <header class="bg-white/95 backdrop-blur-md shadow-sm sticky top-0 z-40 border-b border-gray-200">
                    <div class="px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-8 w-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-sm mr-3">
                                <span class="text-white font-bold text-xs">AS</span>
                            </div>
                            <h1 class="text-lg font-bold text-gray-800">{{ config('app.name', 'AllSports') }}</h1>
                        </div>
                        @auth
                            <!-- Mobile User Avatar -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center p-1 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <div class="h-8 w-8 bg-gradient-to-br from-indigo-400 to-indigo-500 rounded-full flex items-center justify-center shadow-sm">
                                        <span class="text-xs font-semibold text-white">
                                            {{ mb_substr(auth()->user()->nickname ?? auth()->user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->nickname ?? auth()->user()->name }}</div>
                                        <div class="text-xs text-gray-500 flex items-center mt-1">
                                            @if(auth()->user()->role === 'admin')
                                                <div class="w-1.5 h-1.5 bg-red-400 rounded-full mr-2"></div>
                                                관리자
                                            @elseif(auth()->user()->role === 'team_owner')
                                                <div class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-2"></div>
                                                팀장
                                            @else
                                                <div class="w-1.5 h-1.5 bg-green-400 rounded-full mr-2"></div>
                                                사용자
                                            @endif
                                        </div>
                                    </div>

                                    <a href="{{ route('mypage') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        마이페이지
                                    </a>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        프로필 수정
                                    </a>

                                    <div class="border-t border-gray-100 my-1"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            로그아웃
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endauth
                    </div>
                </header>

                 <!-- Mobile Content -->
                 <main class="pb-32 bg-gray-50 min-h-screen">
                     @yield('content')
                 </main>

                 <!-- Mobile Bottom Navigation -->
                 @auth
                         <footer class="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-300 z-[9999] shadow-2xl" style="position: fixed !important; bottom: 0 !important; left: 0 !important; right: 0 !important; z-index: 9999 !important;">
                             <nav class="px-4 py-2">
                                 <div class="flex items-center justify-around">
                                     <!-- 홈 -->
                                     <a href="{{ route('home') }}" class="flex-1 text-center py-3 px-2 rounded-lg transition-all duration-200 {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' }}">
                                         <span class="text-base font-bold">홈</span>
                                     </a>

                                     <!-- 팀 -->
                                     <a href="{{ route('teams.index') }}" class="flex-1 text-center py-3 px-2 rounded-lg transition-all duration-200 {{ request()->routeIs('teams.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' }}">
                                         <span class="text-base font-bold">팀</span>
                                     </a>

                                     <!-- 경기 -->
                                     <a href="{{ route('matches.index') }}" class="flex-1 text-center py-3 px-2 rounded-lg transition-all duration-200 {{ request()->routeIs('matches.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' }}">
                                         <span class="text-base font-bold">경기</span>
                                     </a>

                                     <!-- 랭킹 -->
                                     <a href="{{ route('rankings.index') }}" class="flex-1 text-center py-3 px-2 rounded-lg transition-all duration-200 {{ request()->routeIs('rankings.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' }}">
                                         <span class="text-base font-bold">랭킹</span>
                                     </a>

                                     <!-- 마이페이지 -->
                                     <a href="{{ route('mypage') }}" class="flex-1 text-center py-3 px-2 rounded-lg transition-all duration-200 {{ request()->routeIs('mypage') || request()->routeIs('profile.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' }}">
                                         <span class="text-base font-bold">마이</span>
                                     </a>
                                 </div>
                             </nav>
                         </footer>
                 @endauth
            </div>

            <!-- Tablet Layout (sm 이상 lg 미만) -->
            <div class="hidden sm:block lg:hidden">
                <!-- Tablet Header -->
                <header class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-40 border-b border-gray-200">
                    <div class="max-w-4xl mx-auto px-4 py-4">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('home') }}" class="flex items-center group">
                                <div class="h-10 w-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-sm mr-3 group-hover:shadow-md transition-all">
                                    <span class="text-white font-bold text-sm">AS</span>
                                </div>
                                <span class="text-xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">AllSports</span>
                            </a>

                            @auth
                                <!-- User Avatar -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center p-2 rounded-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                                        <div class="h-9 w-9 bg-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                            <span class="text-sm font-semibold text-white">
                                                {{ mb_substr(auth()->user()->nickname ?? auth()->user()->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </button>

                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->nickname ?? auth()->user()->name }}</div>
                                            <div class="text-sm text-gray-500">{{ auth()->user()->email }}</div>
                                        </div>

                                        <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            프로필 수정
                                        </a>

                                        <div class="border-t border-gray-100 my-1"></div>

                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="flex items-center w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50">
                                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                로그아웃
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </header>

                 <!-- Tablet Navigation -->
                 <nav class="bg-white border-t border-gray-200 sticky bottom-0 z-30">
                     <div class="max-w-4xl mx-auto">
                         <div class="flex items-center justify-around py-3">
                             <!-- 홈 -->
                             <a href="{{ route('home') }}" class="flex flex-col items-center p-2 rounded-lg transition-all duration-200 {{ request()->routeIs('home') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                                 <span class="text-lg mb-1">🏠</span>
                                 <span class="text-xs font-medium">홈</span>
                             </a>

                             <!-- 팀 -->
                             <a href="{{ route('teams.index') }}" class="flex flex-col items-center p-2 rounded-lg transition-all duration-200 {{ request()->routeIs('teams.*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                                 <span class="text-lg mb-1">👥</span>
                                 <span class="text-xs font-medium">팀</span>
                             </a>

                             <!-- 경기 -->
                             <a href="{{ route('matches.index') }}" class="flex flex-col items-center p-2 rounded-lg transition-all duration-200 {{ request()->routeIs('matches.*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                                 <span class="text-lg mb-1">⚽</span>
                                 <span class="text-xs font-medium">경기</span>
                             </a>

                             <!-- 랭킹 -->
                             <a href="{{ route('rankings.index') }}" class="flex flex-col items-center p-2 rounded-lg transition-all duration-200 {{ request()->routeIs('rankings.*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                                 <span class="text-lg mb-1">🏆</span>
                                 <span class="text-xs font-medium">랭킹</span>
                             </a>

                             <!-- 마이페이지 -->
                             <a href="{{ route('mypage') }}" class="flex flex-col items-center p-2 rounded-lg transition-all duration-200 {{ request()->routeIs('mypage') || request()->routeIs('profile.*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                                 <span class="text-lg mb-1">👤</span>
                                 <span class="text-xs font-medium">마이</span>
                             </a>
                         </div>
                     </div>
                 </nav>

                 <!-- Tablet Content -->
                 <main class="bg-gray-50 min-h-screen pb-24">
                    <!-- Page Heading -->
                    @hasSection('header')
                        <div class="bg-white/50 backdrop-blur-sm border-b border-gray-200">
                            <div class="max-w-4xl mx-auto py-6 px-4">
                                @yield('header')
                            </div>
                        </div>
                    @endif

                    <div class="max-w-4xl mx-auto px-4 py-6">
                        @yield('content')
                    </div>
                </main>
            </div>

            <!-- Desktop Layout (lg 이상) -->
            <div class="hidden lg:block">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @hasSection('header')
                    <header class="bg-white/50 backdrop-blur-sm shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

                 <!-- Desktop Content -->
                 <main class="bg-gray-50 min-h-screen">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @yield('content')
                    </div>
            </main>
            </div>
        </div>


        @stack('scripts')

        <!-- PWA Scripts -->
    </body>
</html>
