<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.regions.index') }}"
               class="mr-4 p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                새 지역 추가
            </h2>
        </div>
    </x-slot>

    <!-- Mobile Layout -->
    <div class="sm:hidden p-4">
        <form method="POST" action="{{ route('admin.regions.store') }}">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-4">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="space-y-4">
                    <div>
                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                            시/도
                        </label>
                        <input type="text"
                               id="city"
                               name="city"
                               value="{{ old('city') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="예: 서울"
                               required>
                    </div>

                    <div>
                        <label for="district" class="block text-sm font-semibold text-gray-700 mb-2">
                            구/군
                        </label>
                        <input type="text"
                               id="district"
                               name="district"
                               value="{{ old('district') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="예: 송파구"
                               required>
                    </div>
                </div>

                <div class="mt-6 flex space-x-3">
                    <button type="submit"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-xl font-semibold transition-colors">
                        지역 추가
                    </button>
                    <a href="{{ route('admin.regions.index') }}"
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-3 rounded-xl font-semibold text-center transition-colors">
                        취소
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Desktop & Tablet Layout -->
    <div class="hidden sm:block">
        <div class="py-6">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                    <form method="POST" action="{{ route('admin.regions.store') }}">
                        @csrf

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                                        시/도 *
                                    </label>
                                    <input type="text"
                                           id="city"
                                           name="city"
                                           value="{{ old('city') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                           placeholder="예: 서울"
                                           required>
                                </div>

                                <div>
                                    <label for="district" class="block text-sm font-semibold text-gray-700 mb-2">
                                        구/군 *
                                    </label>
                                    <input type="text"
                                           id="district"
                                           name="district"
                                           value="{{ old('district') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                           placeholder="예: 송파구"
                                           required>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                            <a href="{{ route('admin.regions.index') }}"
                               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold transition-colors">
                                취소
                            </a>
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
                                지역 추가
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>