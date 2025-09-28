<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🛠️ 관리자 대시보드
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 통계 카드들 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-lg">👥</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">전체 사용자</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-lg">🏆</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">전체 팀</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_teams']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-lg">⚽</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">전체 경기</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_matches']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                                    <span class="text-white text-lg">📅</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">예정된 경기</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['scheduled_matches']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 시스템 상태 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- 데이터베이스 상태 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">📊 데이터베이스 상태</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">사용자</span>
                                <span class="text-sm font-medium">{{ number_format($databaseStatus['users']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">팀</span>
                                <span class="text-sm font-medium">{{ number_format($databaseStatus['teams']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">경기</span>
                                <span class="text-sm font-medium">{{ number_format($databaseStatus['matches']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">지역</span>
                                <span class="text-sm font-medium">{{ number_format($databaseStatus['regions']) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">스포츠</span>
                                <span class="text-sm font-medium">{{ number_format($databaseStatus['sports']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- JSON 백업 상태 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">💾 JSON 백업 상태</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">백업 활성화</span>
                                <span class="text-sm font-medium">
                                    @if($jsonBackupStatus['enabled'])
                                        <span class="text-green-600">✓ 활성화</span>
                                    @else
                                        <span class="text-red-600">✗ 비활성화</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">백업 파일 수</span>
                                <span class="text-sm font-medium">{{ $jsonBackupStatus['backup_count'] }}개</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">최신 백업</span>
                                <span class="text-sm font-medium">
                                    @if($jsonBackupStatus['latest_backup'])
                                        {{ $jsonBackupStatus['latest_backup'] }}
                                    @else
                                        없음
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button onclick="backupData()"
                                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                💾 지금 백업하기
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 최근 활동 -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- 최근 사용자 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">👤 최근 가입 사용자</h3>
                        <div class="space-y-3">
                            @forelse($recentUsers as $user)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                            <span class="text-gray-600 text-sm">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($user->role === 'admin') bg-red-100 text-red-800
                                            @elseif($user->role === 'team_owner') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $user->role === 'admin' ? '관리자' : ($user->role === 'team_owner' ? '팀장' : '일반') }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">최근 가입한 사용자가 없습니다.</p>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.users.index') }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                전체 사용자 보기 →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 최근 팀 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">🏆 최근 생성된 팀</h3>
                        <div class="space-y-3">
                            @forelse($recentTeams as $team)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <span class="text-green-600 text-sm">🏆</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $team->team_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $team->city }} {{ $team->district }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <p class="text-xs text-gray-500">{{ $team->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">최근 생성된 팀이 없습니다.</p>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('teams.index') }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                전체 팀 보기 →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 최근 경기 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">⚽ 최근 경기</h3>
                        <div class="space-y-3">
                            @forelse($recentMatches as $match)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <span class="text-purple-600 text-sm">⚽</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $match->home_team_name }} vs {{ $match->away_team_name ?? 'TBD' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $match->match_date->format('m/d') }}
                                            @if($match->match_time) {{ $match->match_time->format('H:i') }} @endif
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($match->status === '예정') bg-blue-100 text-blue-800
                                            @elseif($match->status === '진행중') bg-green-100 text-green-800
                                            @elseif($match->status === '완료') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $match->status }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">최근 경기가 없습니다.</p>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('matches.index') }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                전체 경기 보기 →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 빠른 작업 -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">⚡ 빠른 작업</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="text-center">
                                <div class="text-2xl mb-2">👥</div>
                                <div class="text-sm font-medium">사용자 관리</div>
                            </div>
                        </a>

                        <a href="{{ route('admin.regions.index') }}"
                           class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="text-center">
                                <div class="text-2xl mb-2">📍</div>
                                <div class="text-sm font-medium">지역 관리</div>
                            </div>
                        </a>

                        <a href="{{ route('admin.sports.index') }}"
                           class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="text-center">
                                <div class="text-2xl mb-2">⚽</div>
                                <div class="text-sm font-medium">스포츠 관리</div>
                            </div>
                        </a>

                        <a href="{{ route('admin.settings') }}"
                           class="flex items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="text-center">
                                <div class="text-2xl mb-2">⚙️</div>
                                <div class="text-sm font-medium">시스템 설정</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function backupData() {
            if (confirm('데이터를 백업하시겠습니까?')) {
                fetch('{{ route("admin.backup") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('백업이 완료되었습니다.');
                        location.reload();
                    } else {
                        alert('백업 실패: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('백업 중 오류가 발생했습니다.');
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
