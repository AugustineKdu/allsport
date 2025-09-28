<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                👥 사용자 관리
            </h2>
            <a href="{{ route('admin.dashboard') }}"
               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                ← 대시보드로 돌아가기
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 필터 및 검색 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- 검색 -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">검색</label>
                                <input type="text" name="search" id="search"
                                       value="{{ request('search') }}"
                                       placeholder="이름, 이메일, 닉네임"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <!-- 역할 필터 -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">역할</label>
                                <select name="role" id="role"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">전체</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                                            {{ $role === 'admin' ? '관리자' : ($role === 'team_owner' ? '팀장' : '일반 사용자') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 상태 필터 -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">상태</label>
                                <select name="status" id="status"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">전체</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                            {{ $status === 'active' ? '활성 사용자' : ($status === 'inactive' ? '비활성 사용자' : '팀장') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 버튼 -->
                            <div class="flex items-end">
                                <button type="submit"
                                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                    검색
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 사용자 목록 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    사용자
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    역할
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    지역
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    가입일
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    상태
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    작업
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-gray-600 font-medium">{{ substr($user->name, 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->nickname }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($user->role === 'admin') bg-red-100 text-red-800
                                            @elseif($user->role === 'team_owner') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $user->role === 'admin' ? '관리자' : ($user->role === 'team_owner' ? '팀장' : '일반') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($user->city && $user->district)
                                            {{ $user->city }} {{ $user->district }}
                                        @else
                                            <span class="text-gray-400">미설정</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->email_verified_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                활성
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                비활성
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.users.show', $user) }}"
                                           class="text-blue-600 hover:text-blue-900">보기</a>
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="text-green-600 hover:text-green-900">편집</a>
                                        @if($user->id !== auth()->id())
                                            <button onclick="sendWarning({{ $user->id }})"
                                                    class="text-yellow-600 hover:text-yellow-900">경고</button>
                                            <button onclick="toggleStatus({{ $user->id }})"
                                                    class="text-purple-600 hover:text-purple-900">
                                                {{ $user->email_verified_at ? '비활성화' : '활성화' }}
                                            </button>
                                            <button onclick="deleteUser({{ $user->id }})"
                                                    class="text-red-600 hover:text-red-900">삭제</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        사용자를 찾을 수 없습니다.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 페이지네이션 -->
            @if($users->hasPages())
                <div class="mt-6">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- 경고 모달 -->
    <div id="warningModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">사용자에게 경고 전송</h3>
                <form id="warningForm">
                    @csrf
                    <div class="mb-4">
                        <label for="warning_type" class="block text-sm font-medium text-gray-700">경고 유형</label>
                        <select id="warning_type" name="warning_type" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="minor">경미한 경고</option>
                            <option value="major">심각한 경고</option>
                            <option value="severe">매우 심각한 경고</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="warning_message" class="block text-sm font-medium text-gray-700">경고 메시지</label>
                        <textarea id="warning_message" name="warning_message" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="경고 메시지를 입력하세요..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeWarningModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            취소
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                            경고 전송
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentUserId = null;

        function sendWarning(userId) {
            currentUserId = userId;
            document.getElementById('warningModal').classList.remove('hidden');
        }

        function closeWarningModal() {
            document.getElementById('warningModal').classList.add('hidden');
            document.getElementById('warningForm').reset();
            currentUserId = null;
        }

        document.getElementById('warningForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch(`/admin/users/${currentUserId}/warning`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('경고가 전송되었습니다.');
                    closeWarningModal();
                } else {
                    alert('경고 전송 실패: ' + data.message);
                }
            })
            .catch(error => {
                alert('오류가 발생했습니다.');
            });
        });

        function toggleStatus(userId) {
            if (confirm('사용자 상태를 변경하시겠습니까?')) {
                fetch(`/admin/users/${userId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('상태 변경 실패: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('오류가 발생했습니다.');
                });
            }
        }

        function deleteUser(userId) {
            if (confirm('정말로 이 사용자를 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.')) {
                fetch(`/admin/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('사용자가 삭제되었습니다.');
                        location.reload();
                    } else {
                        alert('삭제 실패: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('오류가 발생했습니다.');
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
