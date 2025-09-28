<x-app-layout>
    <div class="min-h-screen bg-gray-50 pb-32 lg:pb-0">
        <div class="max-w-6xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-400 to-blue-500 rounded-full mb-4">
                    <span class="text-3xl">🤝</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">경기 매칭</h1>
                <p class="text-lg text-gray-600">다른 팀과 경기를 요청하고 매칭하세요!</p>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-lg shadow-sm border p-2 mb-6">
                <nav class="grid grid-cols-3 gap-2">
                    <button onclick="showTab('available')" id="tab-available" class="tab-button py-3 px-4 text-center font-semibold rounded-lg transition-colors bg-blue-600 text-white">
                        🏃‍♂️ 매칭 가능 팀
                    </button>
                    <button onclick="showTab('requests')" id="tab-requests" class="tab-button py-3 px-4 text-center font-semibold rounded-lg transition-colors text-gray-700 hover:bg-gray-100">
                        📥 받은 요청
                    </button>
                    <button onclick="showTab('my-requests')" id="tab-my-requests" class="tab-button py-3 px-4 text-center font-semibold rounded-lg transition-colors text-gray-700 hover:bg-gray-100">
                        📤 보낸 요청
                    </button>
                </nav>
            </div>

            <!-- Available Teams Tab -->
            <div id="content-available" class="tab-content">
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">매칭 가능한 팀들</h2>
                    <p class="text-gray-600 mb-6">같은 스포츠와 지역의 팀들과 경기를 요청할 수 있습니다.</p>

                    @if($availableTeams->count() > 0)
                        <div class="grid gap-4">
                            @foreach($availableTeams as $team)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-lg">
                                                    {{ mb_substr($team->team_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">{{ $team->team_name }}</h3>
                                                <p class="text-gray-600 text-sm">{{ $team->city }} {{ $team->district }}</p>
                                                <p class="text-gray-500 text-sm">팀장: {{ $team->owner->nickname ?? $team->owner->name }}</p>
                                            </div>
                                        </div>
                                        <button onclick="openRequestModal({{ $team->id }}, '{{ $team->team_name }}')"
                                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                            경기 요청
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">🤷‍♂️</div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">매칭 가능한 팀이 없습니다</h3>
                            <p class="text-gray-600">같은 스포츠와 지역의 다른 팀들이 가입하면 매칭할 수 있습니다.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pending Requests Tab -->
            <div id="content-requests" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">받은 경기 요청</h2>
                    <p class="text-gray-600 mb-6">다른 팀에서 보낸 경기 요청을 확인하고 수락/거절할 수 있습니다.</p>

                    @if($pendingRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($pendingRequests as $request)
                                <div class="border rounded-lg p-6 bg-blue-50">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-lg">
                                                    {{ mb_substr($request->requestingTeam->team_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">{{ $request->requestingTeam->team_name }}</h3>
                                                <p class="text-gray-600 text-sm">팀장: {{ $request->requestingTeam->owner->nickname ?? $request->requestingTeam->owner->name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-600">{{ $request->created_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                    </div>

                                    <div class="bg-white rounded-lg p-4 mb-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">경기 정보</h4>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-600">희망 날짜:</span>
                                                <span class="font-semibold">{{ $request->preferred_date->format('Y년 m월 d일') }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">희망 시간:</span>
                                                <span class="font-semibold">{{ $request->preferred_time }}</span>
                                            </div>
                                        </div>
                                        @if($request->message)
                                            <div class="mt-3">
                                                <span class="text-gray-600">메시지:</span>
                                                <p class="text-gray-900 mt-1">{{ $request->message }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex space-x-3">
                                        <form method="POST" action="{{ route('match-matching.accept', $request) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-semibold">
                                                ✅ 수락
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('match-matching.reject', $request) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-semibold">
                                                ❌ 거절
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📭</div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">받은 요청이 없습니다</h3>
                            <p class="text-gray-600">다른 팀에서 경기 요청을 보내면 여기에 표시됩니다.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- My Requests Tab -->
            <div id="content-my-requests" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">보낸 경기 요청</h2>
                    <p class="text-gray-600 mb-6">내가 보낸 경기 요청의 상태를 확인할 수 있습니다.</p>

                    @if($myRequests->count() > 0)
                        <div class="space-y-4">
                            @foreach($myRequests as $request)
                                <div class="border rounded-lg p-6 {{ $request->status === 'pending' ? 'bg-yellow-50' : ($request->status === 'accepted' ? 'bg-green-50' : 'bg-red-50') }}">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold text-lg">
                                                    {{ mb_substr($request->homeTeam->team_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">{{ $request->homeTeam->team_name }}</h3>
                                                <p class="text-gray-600 text-sm">팀장: {{ $request->homeTeam->owner->nickname ?? $request->homeTeam->owner->name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                                @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($request->status === 'accepted') bg-green-100 text-green-800
                                                @elseif($request->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                @if($request->status === 'pending') 대기중
                                                @elseif($request->status === 'accepted') 수락됨
                                                @elseif($request->status === 'rejected') 거절됨
                                                @else 취소됨
                                                @endif
                                            </span>
                                            <p class="text-sm text-gray-600 mt-1">{{ $request->created_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                    </div>

                                    <div class="bg-white rounded-lg p-4 mb-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">경기 정보</h4>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-600">희망 날짜:</span>
                                                <span class="font-semibold">{{ $request->preferred_date->format('Y년 m월 d일') }}</span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">희망 시간:</span>
                                                <span class="font-semibold">{{ $request->preferred_time }}</span>
                                            </div>
                                        </div>
                                        @if($request->message)
                                            <div class="mt-3">
                                                <span class="text-gray-600">메시지:</span>
                                                <p class="text-gray-900 mt-1">{{ $request->message }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($request->status === 'pending')
                                        <form method="POST" action="{{ route('match-matching.cancel', $request) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors font-semibold">
                                                취소
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📤</div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">보낸 요청이 없습니다</h3>
                            <p class="text-gray-600">다른 팀에게 경기 요청을 보내면 여기에 표시됩니다.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Request Modal -->
    <div id="requestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">경기 요청</h3>
                    <form id="requestForm" method="POST" action="{{ route('match-matching.store') }}">
                        @csrf
                        <input type="hidden" id="home_team_id" name="home_team_id">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">희망 날짜</label>
                            <input type="date" name="preferred_date" required
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">희망 시간</label>
                            <input type="time" name="preferred_time" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">메시지 (선택사항)</label>
                            <textarea name="message" rows="3" maxlength="500"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="경기에 대한 간단한 메시지를 남겨주세요..."></textarea>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors font-semibold">
                                요청 보내기
                            </button>
                            <button type="button" onclick="closeRequestModal()" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 transition-colors font-semibold">
                                취소
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('bg-blue-600', 'text-white');
                button.classList.add('text-gray-700', 'hover:bg-gray-100');
            });

            // Show selected tab content
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Add active class to selected tab button
            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.add('bg-blue-600', 'text-white');
            activeButton.classList.remove('text-gray-700', 'hover:bg-gray-100');
        }

        function openRequestModal(teamId, teamName) {
            document.getElementById('home_team_id').value = teamId;
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
</x-app-layout>
