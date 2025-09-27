<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $team->team_name }} 관리
            </h2>
            <a href="{{ route('teams.show', $team->slug) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-400 rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:bg-gray-300 transition-colors">
                팀 페이지로 돌아가기
            </a>
        </div>
    </x-slot>

    <!-- Mobile Layout -->
    <div class="sm:hidden">
        <div class="px-4 py-4 pb-32 space-y-4">
            <!-- Pending Applications -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    가입 신청 대기
                    @if($pendingApplications->count() > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $pendingApplications->count() }}건
                        </span>
                    @endif
                </h3>

                @if($pendingApplications->count() > 0)
                    <div class="space-y-3">
                        @foreach($pendingApplications as $application)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-600">
                                                {{ mb_substr($application->user->nickname, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $application->user->nickname }}</p>
                                            <p class="text-xs text-gray-500">{{ $application->created_at->format('m월 d일 H:i') }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($application->message)
                                    <div class="mb-3 p-3 bg-white rounded-lg border">
                                        <p class="text-sm text-gray-700">{{ $application->message }}</p>
                                    </div>
                                @endif

                                <div class="flex space-x-2">
                                    <form action="{{ route('teams.approve', [$team->slug, $application->id]) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-2 bg-gray-200 text-black text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors border border-gray-400">
                                            승인
                                        </button>
                                    </form>
                                    <form action="{{ route('teams.reject', [$team->slug, $application->id]) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-2 bg-gray-100 text-black text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors border border-gray-300">
                                            거부
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">새로운 가입 신청이 없습니다.</p>
                @endif
            </div>

            <!-- Team Members -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    팀 멤버 관리 ({{ $team->approvedMembers->count() }}명)
                </h3>

                <div class="space-y-3">
                    @foreach($team->approvedMembers as $member)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-xs font-medium text-gray-600">
                                        {{ mb_substr($member->user->nickname, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $member->user->nickname }}
                                        @if($member->role === 'owner')
                                            <span class="ml-1 text-xs text-indigo-600">(팀장)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $member->joined_at->format('Y.m.d') }} 가입</p>
                                </div>
                                @if($member->isOnline())
                                    <span class="inline-block h-2 w-2 rounded-full bg-green-400"></span>
                                @endif
                            </div>

                            @if($member->role !== 'owner')
                                <form action="{{ route('teams.kick', [$team->slug, $member->id]) }}" method="POST"
                                      onsubmit="return confirm('{{ $member->user->nickname }}님을 정말로 팀에서 퇴출하시겠습니까?');">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-gray-100 text-black text-xs font-medium rounded-lg hover:bg-gray-200 transition-colors border border-gray-300">
                                        퇴출
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop & Tablet Layout -->
    <div class="hidden sm:block">
        <div class="py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Pending Applications -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold">가입 신청 대기</h3>
                                @if($pendingApplications->count() > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ $pendingApplications->count() }}건
                                    </span>
                                @endif
                            </div>

                            @if($pendingApplications->count() > 0)
                                <div class="space-y-4">
                                    @foreach($pendingApplications as $application)
                                        <div class="border rounded-lg p-4">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex items-center space-x-3">
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-600">
                                                            {{ mb_substr($application->user->nickname, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $application->user->nickname }}</p>
                                                        <p class="text-xs text-gray-500">{{ $application->created_at->format('Y년 m월 d일 H:i') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($application->message)
                                                <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                                    <p class="text-sm text-gray-700">{{ $application->message }}</p>
                                                </div>
                                            @endif

                                            <div class="flex space-x-3">
                                                <form action="{{ route('teams.approve', [$team->slug, $application->id]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                                        승인
                                                    </button>
                                                </form>
                                                <form action="{{ route('teams.reject', [$team->slug, $application->id]) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                                        거부
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-sm text-gray-500">새로운 가입 신청이 없습니다.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Team Members -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">팀 멤버 관리 ({{ $team->approvedMembers->count() }}명)</h3>

                            <div class="space-y-3">
                                @foreach($team->approvedMembers as $member)
                                    <div class="flex items-center justify-between p-3 border rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600">
                                                    {{ mb_substr($member->user->nickname, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $member->user->nickname }}
                                                    @if($member->role === 'owner')
                                                        <span class="ml-1 text-xs text-indigo-600">(팀장)</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-500">{{ $member->joined_at->format('Y년 m월 d일') }} 가입</p>
                                            </div>
                                            @if($member->isOnline())
                                                <span class="inline-block h-2 w-2 rounded-full bg-green-400"></span>
                                            @endif
                                        </div>

                                        @if($member->role !== 'owner')
                                            <form action="{{ route('teams.kick', [$team->slug, $member->id]) }}" method="POST"
                                                  onsubmit="return confirm('{{ $member->user->nickname }}님을 정말로 팀에서 퇴출하시겠습니까?');">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs leading-4 font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
                                                    퇴출
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
