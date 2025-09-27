<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                지역 관리
            </h2>
            <a href="{{ route('admin.regions.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                새 지역 추가
            </a>
        </div>
    </x-slot>

    <!-- Mobile Layout -->
    <div class="sm:hidden p-4">
        <div class="mb-4">
            <a href="{{ route('admin.regions.create') }}"
               class="w-full flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-xl text-sm font-semibold transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                새 지역 추가
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="space-y-3">
            @forelse($regions as $region)
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="font-semibold text-gray-800">
                                {{ $region->city }} {{ $region->district }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $region->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $region->is_active ? '활성' : '비활성' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <form method="POST" action="{{ route('admin.regions.toggle', $region) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="p-2 rounded-lg {{ $region->is_active ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} transition-colors">
                                    @if($region->is_active)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                </button>
                            </form>

                            <a href="{{ route('admin.regions.edit', $region) }}"
                               class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>

                            <form method="POST" action="{{ route('admin.regions.destroy', $region) }}" class="inline"
                                  onsubmit="return confirm('정말 이 지역을 삭제하시겠습니까?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p class="text-gray-500 mb-4">등록된 지역이 없습니다.</p>
                    <a href="{{ route('admin.regions.create') }}"
                       class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        첫 번째 지역 추가
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($regions->hasPages())
            <div class="mt-6">
                {{ $regions->links() }}
            </div>
        @endif
    </div>

    <!-- Desktop & Tablet Layout -->
    <div class="hidden sm:block">
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        지역
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        상태
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        생성일
                                    </th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        작업
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($regions as $region)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-gray-800">
                                                {{ $region->city }} {{ $region->district }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                {{ $region->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $region->is_active ? '활성' : '비활성' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $region->created_at->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <form method="POST" action="{{ route('admin.regions.toggle', $region) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="p-2 rounded-lg {{ $region->is_active ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} transition-colors"
                                                            title="{{ $region->is_active ? '비활성화' : '활성화' }}">
                                                        @if($region->is_active)
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        @endif
                                                    </button>
                                                </form>

                                                <a href="{{ route('admin.regions.edit', $region) }}"
                                                   class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                                   title="수정">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>

                                                <form method="POST" action="{{ route('admin.regions.destroy', $region) }}" class="inline"
                                                      onsubmit="return confirm('정말 이 지역을 삭제하시겠습니까?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                            title="삭제">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <p class="text-gray-500 mb-4">등록된 지역이 없습니다.</p>
                                            <a href="{{ route('admin.regions.create') }}"
                                               class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                                                첫 번째 지역 추가
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($regions->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $regions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>