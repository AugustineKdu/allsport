<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AllSports') }}</title>

    <!-- TailwindCSS CDN -->
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
        .btn-primary {
            @apply bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200;
        }
        .btn-secondary {
            @apply bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-200;
        }
        .card {
            @apply bg-white shadow-lg rounded-lg p-6 border border-gray-200;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    @if (Route::has('login'))
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">AllSports</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                                로그인
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                    회원가입
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <!-- Hero Section -->
    <div class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-gray-50 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">AllSports에</span>
                            <span class="block text-blue-600 xl:inline">오신 것을 환영합니다</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            아마추어 스포츠 팀을 위한 플랫폼입니다.<br>
                            팀을 만들고, 경기를 관리하고, 랭킹을 확인하세요.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                                    시작하기
                                </a>
                            </div>
                            <div class="mt-3 sm:mt-0 sm:ml-3">
                                <a href="{{ route('login') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 md:py-4 md:text-lg md:px-10">
                                    로그인
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <div class="h-56 w-full bg-gradient-to-r from-blue-500 to-indigo-600 sm:h-72 md:h-96 lg:w-full lg:h-full flex items-center justify-center">
                <div class="text-center text-white">
                    <div class="text-6xl mb-4">⚽</div>
                    <h3 class="text-2xl font-bold mb-2">스포츠를 즐기세요</h3>
                    <p class="text-lg">팀을 만들고 함께 경기해보세요!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">기능</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    스포츠 팀 관리의 모든 것
                </p>
            </div>

            <div class="mt-10">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <span class="text-xl">👥</span>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">팀 관리</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            팀을 만들고 멤버를 관리하세요. 회원가입으로 시작하세요.
                        </p>
                        <div class="mt-3 ml-16">
                            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                                회원가입 →
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <span class="text-xl">📅</span>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">경기 관리</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            경기 일정을 관리하고 결과를 기록하세요. 로그인하여 시작하세요.
                        </p>
                        <div class="mt-3 ml-16">
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                                로그인 →
                            </a>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <span class="text-xl">🏆</span>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">랭킹 시스템</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            지역별 팀 랭킹을 확인하고 경쟁하세요.
                        </p>
                    </div>

                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <span class="text-xl">🎯</span>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">통계 분석</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            팀과 개인의 경기 통계를 분석하세요.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-600">
        <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                <span class="block">지금 시작하세요!</span>
                <span class="block">무료로 팀을 만들어보세요.</span>
            </h2>
            <p class="mt-4 text-lg leading-6 text-blue-200">
                몇 분만에 팀을 만들고 첫 경기를 등록할 수 있습니다.
            </p>
            <a href="{{ route('register') }}" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 sm:w-auto">
                무료로 시작하기
            </a>
        </div>
    </div>
</body>
</html>