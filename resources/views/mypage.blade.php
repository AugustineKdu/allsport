<x-app-layout>
     <div class="min-h-screen bg-gray-50 pb-32 lg:pb-0">
         <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">마이페이지</h1>
                <p class="text-lg text-gray-600">내 정보와 팀 현황을 확인하세요</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- User Profile Card -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-gray-900 text-lg font-semibold">👤 내 정보</h3>
                            <a href="{{ route('profile.edit') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                ✏️ 편집
                            </a>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">🏷️ 닉네임</span>
                                <span class="text-gray-900 font-semibold">{{ $user->nickname }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">📧 이메일</span>
                                <span class="text-gray-900 font-semibold text-sm">{{ $user->email }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">📍 지역</span>
                                <span class="text-gray-900 font-semibold">{{ $user->city }} {{ $user->district }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">📞 전화번호</span>
                                <span class="text-gray-900 font-semibold">{{ $user->phone ?? '미등록' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">
                                    @if($user->selected_sport == '축구') ⚽
                                    @elseif($user->selected_sport == '풋살') 🥅
                                    @elseif($user->selected_sport == '농구') 🏀
                                    @elseif($user->selected_sport == '배드민턴') 🏸
                                    @elseif($user->selected_sport == '탁구') 🏓
                                    @elseif($user->selected_sport == '테니스') 🎾
                                    @elseif($user->selected_sport == '야구') ⚾
                                    @else 🏃
                                    @endif
                                    선호 스포츠
                                </span>
                                <span class="text-gray-900 font-semibold">{{ $user->selected_sport }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">📅 가입일</span>
                                <span class="text-gray-900 font-semibold">{{ $user->created_at->format('Y.m.d') }}</span>
                            </div>
                            @if($user->role === 'admin')
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">👑 역할</span>
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-medium">관리자</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h3 class="text-gray-900 text-lg font-semibold mb-4">⚡ 빠른 메뉴</h3>
                        <div class="space-y-2">
                            <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-300">
                                <div class="flex items-center">
                                    <span class="text-gray-700 mr-3">✏️</span>
                                    <span class="text-black font-semibold text-base">프로필 수정</span>
                                </div>
                                <span class="text-gray-400">→</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-300">
                                    <div class="flex items-center">
                                        <span class="text-gray-700 mr-3">🚪</span>
                                        <span class="text-black font-semibold text-base">로그아웃</span>
                                    </div>
                                    <span class="text-gray-400">→</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Current Team -->
                    @if($currentTeam)
                        <div class="bg-white rounded-lg shadow-sm border p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-gray-900 text-xl font-semibold">⚽ 소속 팀</h3>
                                <a href="{{ route('teams.show', $currentTeam->slug) }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                                    👁️ 팀 페이지로
                                </a>
                            </div>

                            <div class="mb-6">
                                <h4 class="text-gray-900 text-2xl font-bold mb-2">{{ $currentTeam->team_name }}</h4>
                                <div class="flex items-center text-gray-600 text-sm mb-2">
                                    @if($teamMembership->role === 'owner')
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded mr-2">👑 팀 소유자</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded mr-2">👥 팀원</span>
                                    @endif
                                    <span>📅 {{ $teamMembership->joined_at->format('Y.m.d') }} 가입</span>
                                </div>
                                <p class="text-gray-600">{{ $currentTeam->city }} {{ $currentTeam->district }} · {{ $currentTeam->sport }}</p>
                            </div>

                            <div class="grid grid-cols-4 gap-4 bg-gray-50 rounded-lg p-4 mb-6">
                                <div class="text-center">
                                    <div class="text-gray-900 text-xl font-bold">{{ $currentTeam->points }}</div>
                                    <div class="text-gray-500 text-sm">포인트</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-green-600 text-xl font-bold">{{ $currentTeam->wins }}</div>
                                    <div class="text-gray-500 text-sm">승</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-yellow-600 text-xl font-bold">{{ $currentTeam->draws }}</div>
                                    <div class="text-gray-500 text-sm">무</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-red-600 text-xl font-bold">{{ $currentTeam->losses }}</div>
                                    <div class="text-gray-500 text-sm">패</div>
                                </div>
                            </div>

                            @if($teamMembership->role !== 'owner')
                                <div class="pt-6 border-t border-gray-200">
                                    <form action="{{ route('teams.leave', $currentTeam->slug) }}" method="POST"
                                          onsubmit="return confirm('정말로 팀을 떠나시겠습니까?');">
                                        @csrf
                                        <button type="submit" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition-colors font-medium">
                                            🚪 팀 탈퇴
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                            <div class="text-6xl mb-4">🔍</div>
                            <h3 class="text-gray-900 text-xl font-semibold mb-2">소속된 팀이 없습니다</h3>
                            <p class="text-gray-600 mb-6">팀에 가입하거나 새로운 팀을 만들어보세요</p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <a href="{{ route('teams.index') }}" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                    🔍 팀 찾기
                                </a>
                                @if(!$user->ownedTeams()->exists())
                                    <a href="{{ route('teams.create') }}" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                        ➕ 팀 만들기
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
