const CACHE_NAME = 'allsports-v1.0.0';
const STATIC_CACHE = 'allsports-static-v1';
const DYNAMIC_CACHE = 'allsports-dynamic-v1';

// 캐시할 정적 자산들
const STATIC_ASSETS = [
    '/',
    '/home',
    '/teams',
    '/matches',
    '/rankings',
    '/mypage',
    '/manifest.json',
    '/offline.html',
    // CSS와 JS는 Vite가 빌드할 때 생성되는 파일들
];

// API 엔드포인트들 (동적 캐싱)
const API_ENDPOINTS = [
    '/api/regions',
];

// 캐시 전략: Network First (API), Cache First (정적 자산)
self.addEventListener('install', (event) => {
    console.log('[SW] Installing...');
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('[SW] Static assets cached');
                return self.skipWaiting();
            })
    );
});

self.addEventListener('activate', (event) => {
    console.log('[SW] Activating...');
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                            console.log('[SW] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('[SW] Activated');
                return self.clients.claim();
            })
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // API 요청 처리 (Network First)
    if (url.pathname.startsWith('/api/') || API_ENDPOINTS.some(endpoint => url.pathname.startsWith(endpoint))) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // 성공한 응답을 동적 캐시에 저장
                    if (response.status === 200) {
                        const responseClone = response.clone();
                        caches.open(DYNAMIC_CACHE)
                            .then((cache) => {
                                cache.put(request, responseClone);
                            });
                    }
                    return response;
                })
                .catch(() => {
                    // 네트워크 실패 시 캐시에서 응답
                    return caches.match(request)
                        .then((cachedResponse) => {
                            if (cachedResponse) {
                                return cachedResponse;
                            }
                            // API 응답이 캐시에 없으면 기본 응답 반환
                            return new Response(
                                JSON.stringify({
                                    error: '오프라인 상태입니다. 인터넷 연결을 확인해주세요.',
                                    offline: true
                                }),
                                {
                                    status: 503,
                                    statusText: 'Service Unavailable',
                                    headers: { 'Content-Type': 'application/json' }
                                }
                            );
                        });
                })
        );
        return;
    }

    // HTML 페이지 요청 (Network First with Cache Fallback)
    if (request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // 성공한 응답을 동적 캐시에 저장
                    if (response.status === 200) {
                        const responseClone = response.clone();
                        caches.open(DYNAMIC_CACHE)
                            .then((cache) => {
                                cache.put(request, responseClone);
                            });
                    }
                    return response;
                })
                .catch(() => {
                    // 네트워크 실패 시 캐시에서 응답
                    return caches.match(request)
                        .then((cachedResponse) => {
                            if (cachedResponse) {
                                return cachedResponse;
                            }
                            // 오프라인 페이지 반환
                            return caches.match('/offline.html');
                        });
                })
        );
        return;
    }

    // 정적 자산 (CSS, JS, 이미지 등) - Cache First
    event.respondWith(
        caches.match(request)
            .then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }

                // 캐시에 없으면 네트워크에서 가져와서 캐시에 저장
                return fetch(request)
                    .then((response) => {
                        if (response.status === 200) {
                            const responseClone = response.clone();
                            caches.open(STATIC_CACHE)
                                .then((cache) => {
                                    cache.put(request, responseClone);
                                });
                        }
                        return response;
                    })
                    .catch(() => {
                        // 이미지나 폰트 등이 실패하면 기본 이미지 반환
                        if (request.destination === 'image') {
                            return new Response(
                                '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#f3f4f6"/><text x="100" y="100" text-anchor="middle" dy=".3em" fill="#9ca3af" font-family="Arial, sans-serif" font-size="14">이미지를 불러올 수 없습니다</text></svg>',
                                {
                                    headers: { 'Content-Type': 'image/svg+xml' }
                                }
                            );
                        }
                        throw new Error('Network request failed');
                    });
            })
    );
});

// 백그라운드 동기화 (향후 구현)
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync:', event.tag);

    if (event.tag === 'background-sync') {
        event.waitUntil(
            // 오프라인에서 저장된 데이터 동기화
            syncOfflineData()
        );
    }
});

// 푸시 알림 처리 (향후 구현)
self.addEventListener('push', (event) => {
    console.log('[SW] Push received');

    const options = {
        body: event.data ? event.data.text() : '새로운 알림이 있습니다',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/badge-72x72.png',
        vibrate: [200, 100, 200],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: '확인하기',
                icon: '/icons/checkmark.png'
            },
            {
                action: 'close',
                title: '닫기',
                icon: '/icons/xmark.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('AllSports', options)
    );
});

// 알림 클릭 처리
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification click received');

    event.notification.close();

    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// 오프라인 데이터 동기화 함수
async function syncOfflineData() {
    try {
        // IndexedDB에서 오프라인 데이터 가져와서 서버에 동기화
        console.log('[SW] Syncing offline data...');
        // 실제 구현에서는 IndexedDB와 서버 API 연동
    } catch (error) {
        console.error('[SW] Sync failed:', error);
    }
}
