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

            <!-- Matching Section (Hidden by default) -->
            @if($currentTeam)
            <div id="matchingSection" class="hidden mb-8">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column: Available Teams & My Requests -->
                        <div class="space-y-6">
                            <!-- Available Teams -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">매칭 가능한 팀들</h3>
                                @if($availableTeams->count() > 0)
                                    <div class="space-y-4 max-h-64 overflow-y-auto">
                                        @foreach($availableTeams as $team)
                                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-gray-900">{{ $team->team_name }}</h4>
                                                        <p class="text-sm text-gray-600">{{ $team->city }} {{ $team->district }}</p>
                                                        <p class="text-sm text-blue-600">{{ $team->sport }}</p>
                                                    </div>
                                                    <button
                                                        onclick="openRequestModal({{ $team->id }}, '{{ $team->team_name }}')"
                                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                                        매칭 요청
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-8">매칭 가능한 팀이 없습니다.</p>
                                @endif
                            </div>

                            <!-- My Requests -->
                            @if($myRequests->count() > 0)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">내가 보낸 매칭 요청</h3>
                                <div class="space-y-4 max-h-64 overflow-y-auto">
                                    @foreach($myRequests as $request)
                                        <div class="border rounded-lg p-4">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900">{{ $request->requestedTeam->team_name }}</h4>
                                                    <p class="text-sm text-gray-600">{{ $request->match_date->format('Y-m-d') }} {{ $request->match_time->format('H:i') }}</p>
                                                    @if($request->venue)
                                                        <p class="text-sm text-gray-500">📍 {{ $request->venue }}</p>
                                                    @endif
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        대기중
                                                    </span>
                                                    <form action="{{ route('matches.cancel-match-request', $request) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm"
                                                                onclick="return confirm('정말 취소하시겠습니까?')">
                                                            취소
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Right Column: Received Requests -->
                        <div class="space-y-6">
                            @if($receivedRequests->count() > 0)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">받은 매칭 요청</h3>
                                <div class="space-y-4 max-h-64 overflow-y-auto">
                                    @foreach($receivedRequests as $request)
                                        <div class="border rounded-lg p-4">
                                            <div class="mb-3">
                                                <h4 class="font-semibold text-gray-900">{{ $request->requestingTeam->team_name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $request->match_date->format('Y-m-d') }} {{ $request->match_time->format('H:i') }}</p>
                                                @if($request->venue)
                                                    <p class="text-sm text-gray-500">📍 {{ $request->venue }}</p>
                                                @endif
                                                @if($request->message)
                                                    <p class="text-sm text-gray-700 mt-2 bg-gray-50 p-2 rounded">{{ $request->message }}</p>
                                                @endif
                                                @if($request->contact_phone)
                                                    <p class="text-sm text-blue-600 mt-1">📞 {{ $request->contact_phone }}</p>
                                                @endif
                                            </div>
                                            <div class="flex space-x-2">
                                                <form action="{{ route('matches.accept-match-request', $request) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                                        수락
                                                    </button>
                                                </form>
                                                <form action="{{ route('matches.reject-match-request', $request) }}" method="POST" class="flex-1">
                                                    @csrf
                                                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                                                            onclick="return confirm('정말 거절하시겠습니까?')">
                                                        거절
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">받은 매칭 요청</h3>
                                <p class="text-gray-500 text-center py-8">아직 받은 매칭 요청이 없습니다.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Matches List -->
            <div class="space-y-4">
                @forelse($matches as $match)
                    <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                            <div class="flex items-center space-x-3 mb-2 sm:mb-0">
                                <span class="px-3 py-1 rounded-lg text-sm font-medium
                                    @if($match->status === '예정') bg-blue-100 text-blue-800
                                    @elseif($match->status === '진행중') bg-green-100 text-green-800
                                    @elseif($match->status === '완료') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @if($match->status === '예정') 📅 {{ $match->status }}
                                    @elseif($match->status === '진행중') ⏱️ {{ $match->status }}
                                    @elseif($match->status === '완료') ✅ {{ $match->status }}
                                    @else ❌ {{ $match->status }}
                                    @endif
                                </span>
                                <span class="text-gray-600">
                                    🗓️ {{ $match->match_date->format('m월 d일') }}
                                    @if($match->match_time)
                                        🕐 {{ $match->match_time->format('H:i') }}
                                    @endif
                                </span>
                            </div>
                            <div class="text-gray-600">
                                📍 {{ $match->city }} {{ $match->district }} ·
                                @if($match->sport == '축구') ⚽
                                @elseif($match->sport == '풋살') 🥅
                                @elseif($match->sport == '농구') 🏀
                                @elseif($match->sport == '배드민턴') 🏸
                                @elseif($match->sport == '탁구') 🏓
                                @elseif($match->sport == '테니스') 🎾
                                @elseif($match->sport == '야구') ⚾
                                @else 🏃
                                @endif
                                {{ $match->sport }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex-1 text-center sm:text-right">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end sm:space-x-4">
                                    <div class="mb-2 sm:mb-0">
                                        <h3 class="text-gray-900 text-lg sm:text-xl font-bold mb-1">{{ $match->home_team_name }}</h3>
                                        <p class="text-gray-500 text-sm">🏠 홈</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mx-6 sm:mx-8">
                                @if($match->status === '완료')
                                    <div class="bg-gray-100 rounded-lg p-4 text-center">
                                        <div class="text-gray-900 text-2xl sm:text-3xl font-bold">
                                            {{ $match->home_score ?? '-' }} : {{ $match->away_score ?? '-' }}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center text-gray-400">
                                        <div class="text-2xl sm:text-3xl font-bold">VS</div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 text-center sm:text-left">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
                                    <div class="mb-2 sm:mb-0">
                                        <h3 class="text-gray-900 text-lg sm:text-xl font-bold mb-1">{{ $match->away_team_name }}</h3>
                                        <p class="text-gray-500 text-sm">✈️ 원정</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($match->status === '완료' && $match->finalized_at)
                            <div class="mt-4 pt-4 border-t border-gray-200 text-center text-gray-500 text-sm">
                                ⏰ 경기 종료: {{ $match->finalized_at->format('m.d H:i') }}
                            </div>
                        @elseif($match->status === '예정')
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    @if(auth()->user()->currentTeam())
                                        @php
                                            $currentTeam = auth()->user()->currentTeam();
                                            $canApply = $currentTeam &&
                                                       $currentTeam->sport === $match->sport &&
                                                       $currentTeam->city === $match->city &&
                                                       $currentTeam->district === $match->district &&
                                                       $currentTeam->id !== $match->home_team_id &&
                                                       $currentTeam->id !== $match->away_team_id;
                                        @endphp

                                        @if($canApply)
                                            <form action="{{ route('matches.apply', $match->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full bg-gray-200 text-black px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors font-semibold border border-gray-400">
                                                    ⚽ 경기 신청
                                                </button>
                                            </form>
                                        @else
                                            <div class="flex-1 bg-gray-100 text-gray-500 px-4 py-2 rounded-lg text-center font-medium border border-gray-300">
                                                @if(!$currentTeam)
                                                    팀이 없어서 신청할 수 없습니다
                                                @elseif($currentTeam->sport !== $match->sport)
                                                    다른 스포츠 종목입니다
                                                @elseif($currentTeam->city !== $match->city || $currentTeam->district !== $match->district)
                                                    다른 지역입니다
                                                @else
                                                    이미 참여하는 경기입니다
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex-1 bg-gray-100 text-gray-500 px-4 py-2 rounded-lg text-center font-medium border border-gray-300">
                                            팀 가입 후 경기 신청이 가능합니다
                                        </div>
                                    @endif

                                    <a href="{{ route('matches.show', $match->id) }}"
                                       class="bg-gray-100 text-black px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-semibold border border-gray-300 text-center">
                                        상세보기
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                        <div class="text-6xl mb-4">📅</div>
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

            <!-- Pagination -->
            @if($matches->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $matches->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Match Request Modal -->
    @if($currentTeam)
    <div id="requestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <form id="requestForm" method="POST" action="{{ route('matches.store-match-request') }}">
                    @csrf
                    <input type="hidden" id="requested_team_id" name="requested_team_id">

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">매칭 요청</h3>
                            <button type="button" onclick="closeRequestModal()" class="text-gray-400 hover:text-gray-600">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">요청할 팀</label>
                            <p id="teamName" class="text-gray-900 font-semibold"></p>
                        </div>

                        <div class="mb-4">
                            <label for="match_date" class="block text-sm font-medium text-gray-700 mb-2">경기 날짜</label>
                            <input type="date" id="match_date" name="match_date"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="match_time" class="block text-sm font-medium text-gray-700 mb-2">경기 시간</label>
                            <input type="time" id="match_time" name="match_time"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div class="mb-4">
                            <label for="venue" class="block text-sm font-medium text-gray-700 mb-2">경기장 (선택사항)</label>
                            <input type="text" id="venue" name="venue"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="경기장 이름을 입력하세요">
                        </div>

                        <div class="mb-4">
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">연락처</label>
                            <input type="text" id="contact_phone" name="contact_phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="연락 가능한 전화번호" value="{{ auth()->user()->phone }}">
                        </div>

                        <div class="mb-6">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">메시지 (선택사항)</label>
                            <textarea id="message" name="message" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="상대 팀에게 전할 메시지를 입력하세요"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                        <button type="button" onclick="closeRequestModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            취소
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            요청 보내기
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script>
    function toggleMatchingSection() {
        const section = document.getElementById('matchingSection');
        const button = event.target;

        if (section.classList.contains('hidden')) {
            section.classList.remove('hidden');
            button.textContent = '✅ 매칭 닫기';
            button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            button.classList.add('bg-gray-600', 'hover:bg-gray-700');
        } else {
            section.classList.add('hidden');
            button.textContent = '⚽ 매칭 요청하기';
            button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
            button.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
    }

    function openRequestModal(teamId, teamName) {
        document.getElementById('requested_team_id').value = teamId;
        document.getElementById('teamName').textContent = teamName;
        document.getElementById('requestModal').classList.remove('hidden');
    }

    function closeRequestModal() {
        document.getElementById('requestModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('requestModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRequestModal();
        }
    });

    function toggleInvitationForm() {
        const form = document.getElementById('invitationForm');
        if (form) {
            form.classList.toggle('hidden');
        }
    }
    </script>
</x-app-layout>
