<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📋 매칭 신청 관리
        </h2>
    </x-slot>

    <div class="py-6 pb-32 lg:pb-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Match Info -->
            <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">경기 정보</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <div class="text-sm text-gray-600">팀명</div>
                        <div class="font-semibold text-gray-900">{{ $match->home_team_name }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">경기 일시</div>
                        <div class="font-semibold text-gray-900">
                            {{ $match->match_date->format('m월 d일') }} {{ $match->match_time->format('H:i') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">지역</div>
                        <div class="font-semibold text-gray-900">
                            @if($match->city && $match->district)
                                {{ $match->city }} {{ $match->district }}
                            @else
                                전국
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">매칭 상태</div>
                        <div class="font-semibold">
                            @if($match->away_team_id)
                                <span class="text-green-600">✅ 매칭 확정</span>
                            @elseif($match->is_matching_open)
                                <span class="text-blue-600">🔍 매칭 모집 중</span>
                            @else
                                <span class="text-red-600">❌ 매칭 마감</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications List -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">신청 팀 목록</h3>
                    <div class="text-sm text-gray-600">
                        총 {{ $applications->count() }}개 신청
                    </div>
                </div>

                @forelse($applications as $application)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                            <!-- Application Info -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-4 mb-3">
                                    <div class="flex items-center space-x-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            @if($application->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($application->status === 'accepted') bg-green-100 text-green-800
                                            @elseif($application->status === 'rejected') bg-red-100 text-red-800
                                            @elseif($application->status === 'withdrawn') bg-gray-100 text-gray-800
                                            @endif">
                                            @if($application->status === 'pending') 대기중
                                            @elseif($application->status === 'accepted') 수락됨
                                            @elseif($application->status === 'rejected') 거절됨
                                            @elseif($application->status === 'withdrawn') 철회됨
                                            @endif
                                        </span>
                                        <span class="text-lg font-bold text-gray-900">{{ $application->team->team_name }}</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <div class="text-sm text-gray-600">팀 정보</div>
                                        <div class="text-sm text-gray-900">
                                            {{ $application->team->city }} {{ $application->team->district }} |
                                            멤버 {{ $application->team->approvedMembers->count() }}명
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">신청일</div>
                                        <div class="text-sm text-gray-900">
                                            {{ $application->applied_at->format('m월 d일 H:i') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">연락 담당자</div>
                                        <div class="text-sm text-gray-900">{{ $application->contact_person }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">연락처</div>
                                        <div class="text-sm text-gray-900">
                                            <a href="tel:{{ $application->contact_phone }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $application->contact_phone }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                @if($application->message)
                                    <div class="mb-3">
                                        <div class="text-sm text-gray-600 mb-1">메시지</div>
                                        <div class="text-sm text-gray-900 bg-gray-50 rounded-lg p-3">
                                            {{ $application->message }}
                                        </div>
                                    </div>
                                @endif

                                @if($application->availability)
                                    <div class="mb-3">
                                        <div class="text-sm text-gray-600 mb-1">가능한 시간대</div>
                                        <div class="text-sm text-gray-900">
                                            {{ implode(', ', $application->availability) }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="lg:ml-6 mt-4 lg:mt-0">
                                @if($application->status === 'pending' && !$match->away_team_id)
                                    <div class="flex flex-col space-y-2">
                                        <form action="{{ route('matches.applications.accept', [$match->id, $application->id]) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('{{ $application->team->team_name }}팀과의 매칭을 수락하시겠습니까?')"
                                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm font-semibold">
                                                ✅ 수락
                                            </button>
                                        </form>
                                        <form action="{{ route('matches.applications.reject', [$match->id, $application->id]) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('{{ $application->team->team_name }}팀의 신청을 거절하시겠습니까?')"
                                                    class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm font-semibold">
                                                ❌ 거절
                                            </button>
                                        </form>
                                    </div>
                                @elseif($application->status === 'accepted')
                                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg text-center">
                                        <div class="font-semibold">✅ 매칭 확정</div>
                                        <div class="text-sm">경기 상대팀으로 확정</div>
                                    </div>
                                @elseif($application->status === 'rejected')
                                    <div class="bg-red-100 text-red-800 px-4 py-2 rounded-lg text-center">
                                        <div class="font-semibold">❌ 거절됨</div>
                                        <div class="text-sm">{{ $application->responded_at->format('m월 d일 H:i') }}</div>
                                    </div>
                                @elseif($application->status === 'withdrawn')
                                    <div class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg text-center">
                                        <div class="font-semibold">🔄 철회됨</div>
                                        <div class="text-sm">{{ $application->responded_at->format('m월 d일 H:i') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-4xl mb-4">📝</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">신청이 없습니다</h3>
                        <p class="text-gray-600">아직 매칭 신청이 없습니다.</p>
                    </div>
                @endforelse
            </div>

            <!-- Back Button -->
            <div class="mt-6 text-center">
                <a href="{{ route('matches.matching.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-700">
                    ← 매칭 관리로 돌아가기
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
