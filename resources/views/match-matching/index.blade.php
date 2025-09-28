@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">팀 매칭</h1>
            <p class="text-gray-600">다른 팀과 매칭하여 경기를 만들어보세요!</p>
        </div>

        <!-- Current Team Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $currentTeam->team_name }}</h2>
                    <p class="text-gray-600">{{ $currentTeam->city }} {{ $currentTeam->district }} • {{ $currentTeam->sport }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        팀 오너
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column: Available Teams & My Requests -->
            <div class="space-y-6">
                <!-- Available Teams -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">매칭 가능한 팀들</h3>

                    @if($availableTeams->count() > 0)
                        <div class="space-y-4">
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
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">내가 보낸 매칭 요청</h3>
                    <div class="space-y-4">
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
                                        <form action="{{ route('match-matching.cancel', $request) }}" method="POST" class="inline">
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
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">받은 매칭 요청</h3>
                    <div class="space-y-4">
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
                                    <form action="{{ route('match-matching.accept', $request) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                            수락
                                        </button>
                                    </form>
                                    <form action="{{ route('match-matching.reject', $request) }}" method="POST" class="flex-1">
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
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">받은 매칭 요청</h3>
                    <p class="text-gray-500 text-center py-8">아직 받은 매칭 요청이 없습니다.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Match Request Modal -->
<div id="requestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <form id="requestForm" method="POST" action="{{ route('match-matching.store') }}">
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

<script>
function openRequestModal(teamId, teamName) {
    document.getElementById('requested_team_id').value = teamId;
    document.getElementById('teamName').textContent = teamName;
    document.getElementById('requestModal').classList.remove('hidden');
}

function closeRequestModal() {
    document.getElementById('requestModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('requestModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRequestModal();
    }
});
</script>
@endsection
