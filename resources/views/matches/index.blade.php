<x-app-layout>
     <div class="min-h-screen bg-gray-50 pb-32 lg:pb-0">
         <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">경기</h1>
                <p class="text-lg text-gray-600">경기 일정을 확인하고 새로운 경기를 생성하세요</p>

                @if($currentTeam)
                    <div class="mt-4 flex flex-wrap justify-center gap-3">
                        <button onclick="toggleInvitationForm()"
                           class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold shadow-lg">
                            📨 경기 초대하기
                        </button>
                        <a href="{{ route('matches.create') }}"
                           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold shadow-lg">
                            ⚽ 직접 경기 생성
                        </a>
                    </div>
                @else
                    <div class="mt-4 flex justify-center">
                        <a href="{{ route('teams.index') }}"
                           class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-semibold shadow-lg">
                            👥 팀에 가입하기
                        </a>
                    </div>
                @endif
            </div>

            <!-- Status Tabs -->
            <div class="bg-white rounded-lg shadow-sm border p-2 mb-6">
                <nav class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <a href="{{ route('matches.index') }}"
                       class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ !request('status') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        📋 전체
                    </a>
                    <a href="{{ route('matches.index', ['status' => '예정']) }}"
                       class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ request('status') === '예정' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        📅 예정
                    </a>
                    <a href="{{ route('matches.index', ['status' => '진행중']) }}"
                       class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ request('status') === '진행중' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        ⏱️ 진행중
                    </a>
                    <a href="{{ route('matches.index', ['status' => '완료']) }}"
                       class="py-3 px-4 text-center font-semibold rounded-lg transition-colors {{ request('status') === '완료' ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                        ✅ 완료
                    </a>
                </nav>
            </div>

            <!-- Match Invitation Form (Hidden by default) -->
            @if($currentTeam)
            <div id="invitationForm" class="hidden mb-8">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">📨 경기 초대하기</h2>
                        <button onclick="toggleInvitationForm()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form action="{{ route('matches.invite') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">상대 팀 선택</label>
                            <select name="invited_team_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">팀을 선택하세요</option>
                                @foreach($availableTeams as $team)
                                    <option value="{{ $team->id }}">
                                        {{ $team->team_name }} ({{ $team->city }} {{ $team->district }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">경기 날짜</label>
                                <input type="date" name="proposed_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">경기 시간</label>
                                <input type="time" name="proposed_time" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">경기 장소</label>
                            <input type="text" name="proposed_venue" required placeholder="예: 서울특별시 송파구 잠실체육관" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">연락처</label>
                            <input type="tel" name="contact_phone" value="{{ Auth::user()->phone }}" placeholder="연락 가능한 전화번호" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">메시지 (선택사항)</label>
                            <textarea name="message" rows="3" placeholder="상대팀에게 전하고 싶은 메시지를 입력하세요..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                                📨 초대 보내기
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Received Invitations -->
            @if($currentTeam && $receivedInvitations->count() > 0)
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">📥 받은 경기 초대</h2>
                    <div class="space-y-4">
                        @foreach($receivedInvitations as $invitation)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-lg font-semibold text-gray-900">{{ $invitation->invitingTeam->team_name }}</span>
                                        <span class="text-sm text-gray-500">({{ $invitation->invitingTeam->city }} {{ $invitation->invitingTeam->district }})</span>
                                    </div>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p>📅 {{ $invitation->proposed_date->format('Y년 m월 d일') }} {{ $invitation->proposed_time->format('H:i') }}</p>
                                        <p>📍 {{ $invitation->proposed_venue }}</p>
                                        @if($invitation->contact_phone)
                                            <p>📞 {{ $invitation->contact_phone }}</p>
                                        @endif
                                        @if($invitation->message)
                                            <p class="mt-2 p-2 bg-gray-50 rounded">💬 {{ $invitation->message }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <form action="{{ route('matches.invitations.accept', $invitation) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm font-semibold">
                                            ✅ 수락
                                        </button>
                                    </form>
                                    <form action="{{ route('matches.invitations.reject', $invitation) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm font-semibold">
                                            ❌ 거절
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Sent Invitations -->
            @if($currentTeam && $sentInvitations->count() > 0)
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">📤 보낸 경기 초대</h2>
                    <div class="space-y-4">
                        @foreach($sentInvitations as $invitation)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-lg font-semibold text-gray-900">{{ $invitation->invitedTeam->team_name }}</span>
                                        <span class="text-sm text-gray-500">({{ $invitation->invitedTeam->city }} {{ $invitation->invitedTeam->district }})</span>
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">대기중</span>
                                    </div>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p>📅 {{ $invitation->proposed_date->format('Y년 m월 d일') }} {{ $invitation->proposed_time->format('H:i') }}</p>
                                        <p>📍 {{ $invitation->proposed_venue }}</p>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <form action="{{ route('matches.invitations.cancel', $invitation) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm font-semibold">
                                            🗑️ 취소
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Matches List -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">경기 일정</h2>
                    </div>

                    @forelse($matches as $match)
                        <div class="border-b border-gray-100 last:border-b-0 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-4 mb-2">
                                        <div class="text-center">
                                            <div class="text-sm font-semibold text-gray-900">{{ $match->home_team_name }}</div>
                                            @if($match->status === '완료')
                                                <div class="text-2xl font-bold text-blue-600">{{ $match->home_score ?? 0 }}</div>
                                            @endif
                                        </div>
                                        
                                        <div class="text-center px-4">
                                            <div class="text-xs text-gray-500 mb-1">{{ $match->match_date->format('m/d') }}</div>
                                            <div class="text-lg font-bold text-gray-400">VS</div>
                                            <div class="text-xs text-gray-500 mt-1">{{ $match->match_time->format('H:i') }}</div>
                                        </div>
                                        
                                        <div class="text-center">
                                            <div class="text-sm font-semibold text-gray-900">{{ $match->away_team_name }}</div>
                                            @if($match->status === '완료')
                                                <div class="text-2xl font-bold text-red-600">{{ $match->away_score ?? 0 }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-4 text-xs text-gray-500">
                                        <span>📍 {{ $match->venue ?? $match->city . ' ' . $match->district }}</span>
                                        <span>⚽ {{ $match->sport }}</span>
                                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                                            {{ $match->status === '완료' ? 'bg-green-100 text-green-800' : 
                                               ($match->status === '진행중' ? 'bg-yellow-100 text-yellow-800' : 
                                                'bg-blue-100 text-blue-800') }}">
                                            {{ $match->status }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('matches.show', $match) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        상세보기
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="text-gray-400 text-6xl mb-4">⚽</div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                @if(request('status'))
                                    {{ request('status') }} 상태의 경기가 없습니다
                                @else
                                    등록된 경기가 없습니다
                                @endif
                            </h3>
                            <p class="text-gray-600">새로운 경기가 등록되면 여기에 표시됩니다</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            @if($matches->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $matches->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
    function toggleInvitationForm() {
        const form = document.getElementById('invitationForm');
        if (form) {
            form.classList.toggle('hidden');
        }
    }
    </script>
</x-app-layout>