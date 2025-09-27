<x-app-layout>
     <div class="min-h-screen bg-gray-50 pb-32 lg:pb-0">
         <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">팀 찾기</h1>
                <p class="text-lg text-gray-600">나와 맞는 팀을 찾아 함께 스포츠를 즐겨보세요</p>
            </div>

            <!-- My Team Info (if exists) -->
            @if($currentTeam && $currentTeam->slug)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-blue-900 mb-1">내 팀</h3>
                            <p class="text-2xl font-bold text-blue-900 mb-2">{{ $currentTeam->team_name }}</p>
                            <p class="text-blue-700">
                                📍 {{ $currentTeam->city }} {{ $currentTeam->district }} •
                                🏃 {{ $currentTeam->sport }}
                            </p>
                        </div>
                        <a href="{{ route('teams.show', $currentTeam->slug) }}"
                           class="bg-gray-200 text-black px-4 py-3 rounded-lg hover:bg-gray-300 transition-colors font-semibold border border-gray-400">
                            팀 페이지로
                        </a>
                    </div>
                </div>
            @endif

            <!-- Search & Filters -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 sm:mb-0">팀 검색</h2>
                     <a href="{{ route('teams.create') }}"
                        class="bg-gray-200 text-black px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors font-semibold border border-gray-400">
                         새 팀 만들기
                     </a>
                </div>

                <form method="GET" action="{{ route('teams.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">지역</label>
                            <select name="city" id="city" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">전체 지역</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="district" class="block text-sm font-medium text-gray-700 mb-1">구/군</label>
                            <select name="district" id="district" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">전체 구/군</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district }}" {{ request('district') == $district ? 'selected' : '' }}>
                                        {{ $district }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="sport" class="block text-sm font-medium text-gray-700 mb-1">스포츠</label>
                            <select name="sport" id="sport" class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">전체 스포츠</option>
                                @foreach($sports as $sport)
                                    <option value="{{ $sport }}" {{ request('sport') == $sport ? 'selected' : '' }}>
                                        {{ $sport }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">팀 이름</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="팀 이름 검색"
                                   class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                     <div class="flex flex-col sm:flex-row gap-2">
                         <button type="submit"
                                 class="bg-gray-200 text-black px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors font-semibold border border-gray-400">
                             검색
                         </button>
                         <a href="{{ route('teams.index') }}"
                            class="bg-gray-100 text-black px-6 py-2 rounded-lg hover:bg-gray-200 transition-colors text-center font-semibold border border-gray-300">
                             초기화
                         </a>
                     </div>
                </form>
            </div>

            <!-- Teams Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($teams as $team)
                    <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <!-- Team Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-2xl">
                                            @if($team->sport == '축구') ⚽
                                            @elseif($team->sport == '풋살') 🥅
                                            @elseif($team->sport == '농구') 🏀
                                            @elseif($team->sport == '배드민턴') 🏸
                                            @elseif($team->sport == '탁구') 🏓
                                            @elseif($team->sport == '테니스') 🎾
                                            @elseif($team->sport == '야구') ⚾
                                            @else 🏃
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $team->team_name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $team->city }} {{ $team->district }}</p>
                                    </div>
                                </div>
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">
                                    {{ $team->sport }}
                                </span>
                            </div>

                            <!-- Team Stats -->
                            <div class="grid grid-cols-4 gap-2 mb-4 text-center">
                                <div class="bg-gray-50 rounded p-2">
                                    <div class="text-sm font-semibold text-gray-900">{{ $team->approvedMembers->count() }}</div>
                                    <div class="text-xs text-gray-600">멤버</div>
                                </div>
                                <div class="bg-gray-50 rounded p-2">
                                    <div class="text-sm font-semibold text-green-600">{{ $team->wins }}</div>
                                    <div class="text-xs text-gray-600">승</div>
                                </div>
                                <div class="bg-gray-50 rounded p-2">
                                    <div class="text-sm font-semibold text-yellow-600">{{ $team->draws }}</div>
                                    <div class="text-xs text-gray-600">무</div>
                                </div>
                                <div class="bg-gray-50 rounded p-2">
                                    <div class="text-sm font-semibold text-red-600">{{ $team->losses }}</div>
                                    <div class="text-xs text-gray-600">패</div>
                                </div>
                            </div>

                            <div class="text-center mb-4">
                                <div class="text-sm text-gray-600 mb-1">{{ $team->sport }}</div>
                                <span class="text-lg font-bold text-blue-600">{{ $team->points }}점</span>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                @if($team->slug)
                                    <a href="{{ route('teams.show', $team->slug) }}"
                                       class="flex-1 text-center bg-gray-100 text-black py-3 rounded-lg hover:bg-gray-200 transition-colors font-semibold border border-gray-300">
                                        상세보기
                                    </a>
                                @else
                                    <div class="flex-1 text-center bg-gray-100 text-gray-500 py-3 rounded-lg border border-gray-300">
                                        상세보기
                                    </div>
                                @endif
                                @if(!auth()->user()->currentTeam() && $team->owner_user_id !== auth()->id() && $team->slug)
                                    <form action="{{ route('teams.apply', $team->slug) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit"
                                                class="w-full bg-gray-200 text-black py-3 rounded-lg hover:bg-gray-300 transition-colors font-semibold border border-gray-400">
                                            가입신청
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">검색 조건에 맞는 팀이 없습니다</h3>
                        <p class="text-gray-600 mb-6">다른 조건으로 검색해보거나 새로운 팀을 만들어보세요!</p>
                        @if(!auth()->user()->currentTeam() && !auth()->user()->ownedTeams()->exists())
                            <a href="{{ route('teams.create') }}"
                               class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                새 팀 만들기
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($teams->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $teams->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
