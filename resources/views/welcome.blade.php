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
                        <div class="flex items-center mb-4">
                            <span class="text-5xl mr-3">🏆</span>
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                                AllSports
                            </h1>
                        </div>
                        <div class="space-y-4">
                            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-800 leading-tight">
                                지역을 대표하고 전국을 제패하라
                            </h2>
                            <p class="text-xl sm:text-2xl font-semibold text-blue-600 leading-relaxed">
                                랭킹과 기록이 만들어가는<br>
                                <span class="text-gray-700">나와 팀의 성장스토리</span>
                            </p>
                        </div>

                        <!-- Service Notice -->
                        <div class="mt-8 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg max-w-2xl">
                            <div class="flex items-start space-x-3">
                                <div class="text-2xl">⚡</div>
                                <div>
                                    <h3 class="font-semibold text-blue-800 mb-2">전국 아마추어 스포츠의 새로운 시작</h3>
                                    <p class="text-sm text-blue-700 mb-2">
                                        지역별 팀 매칭부터 전국 랭킹까지! <strong>축구와 풋살</strong>을 시작으로
                                        다양한 스포츠 종목을 지원합니다.
                                    </p>
                                    <p class="text-xs text-blue-600">
                                        🎯 팀을 만들고, 경기를 하고, 랭킹을 올려보세요!
                                    </p>
                                </div>
                            </div>
                        </div>
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
                    <div class="flex justify-center space-x-4 mb-6">
                        <span class="text-6xl">🏆</span>
                        <span class="text-6xl">⚽</span>
                        <span class="text-6xl">🥅</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">전국을 향한 도전</h3>
                    <p class="text-lg mb-2">지역을 대표하는 팀이 되어</p>
                    <p class="text-lg">전국 랭킹의 정상에 오르세요</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">AllSports의 핵심 기능</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    지역부터 전국까지, 팀의 성장을 지원합니다
                </p>
            </div>

            <div class="mt-10">
                <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 md:gap-x-8 md:gap-y-10">
                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <span class="text-xl">👥</span>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">팀 만들기</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            지역을 대표하는 팀을 만들고 멤버를 모집하세요.
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
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">전국 매칭</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            지역부터 전국까지 아무 곳이나 팀과 매칭하여 경기하세요.
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
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">전국 랭킹</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            지역/전국 랭킹에서 팀의 실력을 확인하고 전국 제패에 도전하세요.
                        </p>
                    </div>

                    <div class="relative">
                        <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <span class="text-xl">🎯</span>
                        </div>
                        <p class="ml-16 text-lg leading-6 font-medium text-gray-900">성장 기록</p>
                        <p class="mt-2 ml-16 text-base text-gray-500">
                            모든 경기와 승부를 기록으로 남겨 팀의 성장 스토리를 만들어가세요.
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
                <span class="block">🏆 전국 제패의 꿈</span>
                <span class="block">지금 시작하세요!</span>
            </h2>
            <p class="mt-4 text-lg leading-6 text-blue-200">
                지역을 대표하는 팀이 되어 전국 랭킹의 정상에 오르는<br>
                나와 팀의 성장 스토리를 만들어보세요.
            </p>
            <a href="{{ route('register') }}" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 sm:w-auto">
                🚀 전국 제패 시작하기
            </a>
        </div>
    </div>
</body>
</html>
