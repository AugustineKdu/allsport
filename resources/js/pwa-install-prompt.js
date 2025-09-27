// PWA 설치 프롬프트 관리
class PWAInstallPrompt {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.isStandalone = false;
        this.installButton = null;

        this.init();
    }

    init() {
        // 현재 앱이 설치되어 있는지 확인
        this.checkInstallStatus();

        // 설치 프롬프트 이벤트 리스너
        window.addEventListener('beforeinstallprompt', (e) => {
            console.log('[PWA] beforeinstallprompt fired');
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallButton();
        });

        // 앱 설치 완료 이벤트
        window.addEventListener('appinstalled', () => {
            console.log('[PWA] App installed successfully');
            this.isInstalled = true;
            this.hideInstallButton();
            this.showInstallSuccess();
        });

        // 설치 버튼 생성
        this.createInstallButton();

        // 주기적으로 설치 상태 확인
        setInterval(() => {
            this.checkInstallStatus();
        }, 5000);
    }

    checkInstallStatus() {
        // PWA가 설치되어 있는지 확인
        this.isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
            window.navigator.standalone ||
            document.referrer.includes('android-app://');

        if (this.isStandalone) {
            this.isInstalled = true;
            this.hideInstallButton();
        }
    }

    createInstallButton() {
        // 설치 버튼 HTML 생성
        const installButtonHTML = `
            <div id="pwa-install-banner" class="fixed bottom-20 left-4 right-4 z-50 bg-white rounded-2xl shadow-2xl border border-gray-200 p-4 transform translate-y-full transition-transform duration-300 ease-out" style="display: none;">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-sm">
                            <span class="text-white font-bold text-lg">AS</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-gray-900">AllSports 앱 설치</h3>
                        <p class="text-xs text-gray-600">홈 화면에 추가하여 더 빠르게 접근하세요</p>
                    </div>
                    <div class="flex space-x-2">
                        <button id="pwa-install-btn" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                            설치
                        </button>
                        <button id="pwa-dismiss-btn" class="text-gray-400 hover:text-gray-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;

        // DOM에 추가
        document.body.insertAdjacentHTML('beforeend', installButtonHTML);

        this.installButton = document.getElementById('pwa-install-banner');

        // 이벤트 리스너 추가
        document.getElementById('pwa-install-btn').addEventListener('click', () => {
            this.installApp();
        });

        document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
            this.dismissInstallBanner();
        });

        // 자동으로 사라지기 (10초 후)
        setTimeout(() => {
            if (this.installButton && this.installButton.style.display !== 'none') {
                this.dismissInstallBanner();
            }
        }, 10000);
    }

    showInstallButton() {
        // 이미 설치되었거나 프롬프트가 없으면 표시하지 않음
        if (this.isInstalled || !this.deferredPrompt) {
            return;
        }

        // 사용자가 이전에 거부했는지 확인
        const dismissed = localStorage.getItem('pwa-install-dismissed');
        const dismissedTime = localStorage.getItem('pwa-install-dismissed-time');

        if (dismissed && dismissedTime) {
            const daysSinceDismissed = (Date.now() - parseInt(dismissedTime)) / (1000 * 60 * 60 * 24);
            // 7일 후에 다시 표시
            if (daysSinceDismissed < 7) {
                return;
            }
        }

        // 모바일에서만 표시 (데스크톱에서는 브라우저 설치 안내)
        if (window.innerWidth > 768) {
            this.showDesktopInstallInfo();
            return;
        }

        if (this.installButton) {
            this.installButton.style.display = 'block';
            // 애니메이션으로 나타나기
            setTimeout(() => {
                this.installButton.classList.remove('translate-y-full');
            }, 100);
        }
    }

    showDesktopInstallInfo() {
        // 데스크톱용 설치 안내
        const desktopInfo = `
            <div id="pwa-desktop-info" class="fixed bottom-4 right-4 z-50 bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-sm shadow-lg">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-blue-900">앱으로 설치하기</h3>
                        <p class="text-xs text-blue-700 mt-1">
                            브라우저 주소창 오른쪽의 <span class="font-semibold">설치</span> 버튼을 클릭하여 앱으로 설치하세요.
                        </p>
                        <button onclick="document.getElementById('pwa-desktop-info').remove()" class="text-blue-600 hover:text-blue-800 text-xs mt-2">
                            닫기
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', desktopInfo);

        // 5초 후 자동으로 사라지기
        setTimeout(() => {
            const info = document.getElementById('pwa-desktop-info');
            if (info) {
                info.remove();
            }
        }, 5000);
    }

    async installApp() {
        if (!this.deferredPrompt) {
            return;
        }

        try {
            // 설치 프롬프트 표시
            this.deferredPrompt.prompt();

            // 사용자 선택 결과 확인
            const { outcome } = await this.deferredPrompt.userChoice;

            if (outcome === 'accepted') {
                console.log('[PWA] User accepted the install prompt');
                this.trackInstallEvent('accepted');
            } else {
                console.log('[PWA] User dismissed the install prompt');
                this.trackInstallEvent('dismissed');
                this.dismissInstallBanner();
            }

            // 프롬프트 정리
            this.deferredPrompt = null;
            this.hideInstallButton();

        } catch (error) {
            console.error('[PWA] Install error:', error);
        }
    }

    dismissInstallBanner() {
        if (this.installButton) {
            this.installButton.classList.add('translate-y-full');
            setTimeout(() => {
                this.installButton.style.display = 'none';
            }, 300);
        }

        // 거부 상태 저장
        localStorage.setItem('pwa-install-dismissed', 'true');
        localStorage.setItem('pwa-install-dismissed-time', Date.now().toString());
    }

    hideInstallButton() {
        if (this.installButton) {
            this.installButton.style.display = 'none';
        }
    }

    showInstallSuccess() {
        // 설치 성공 토스트 메시지
        const successToast = `
            <div id="pwa-install-success" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium">AllSports 앱이 설치되었습니다!</span>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', successToast);

        // 3초 후 사라지기
        setTimeout(() => {
            const toast = document.getElementById('pwa-install-success');
            if (toast) {
                toast.remove();
            }
        }, 3000);
    }

    trackInstallEvent(action) {
        // 설치 이벤트 추적 (Google Analytics 등에 전송 가능)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'pwa_install', {
                'event_category': 'PWA',
                'event_label': action,
                'value': action === 'accepted' ? 1 : 0
            });
        }

        console.log(`[PWA] Install event tracked: ${action}`);
    }

    // 수동으로 설치 버튼 표시 (메뉴에서 호출 가능)
    showManualInstallPrompt() {
        if (this.deferredPrompt) {
            this.showInstallButton();
        } else {
            // 프롬프트가 없으면 브라우저별 안내 표시
            this.showBrowserSpecificInfo();
        }
    }

    showBrowserSpecificInfo() {
        const userAgent = navigator.userAgent.toLowerCase();
        let instructions = '';

        if (userAgent.includes('chrome')) {
            instructions = '주소창 오른쪽의 <strong>설치</strong> 버튼을 클릭하세요.';
        } else if (userAgent.includes('safari')) {
            instructions = '하단 메뉴에서 <strong>홈 화면에 추가</strong>를 탭하세요.';
        } else if (userAgent.includes('firefox')) {
            instructions = '주소창 오른쪽의 <strong>설치</strong> 버튼을 클릭하세요.';
        } else {
            instructions = '브라우저 메뉴에서 앱 설치 옵션을 찾아보세요.';
        }

        const infoModal = `
            <div id="pwa-browser-info" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl p-6 max-w-sm w-full">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">앱 설치 방법</h3>
                    <p class="text-gray-600 text-sm mb-4">${instructions}</p>
                    <button onclick="document.getElementById('pwa-browser-info').remove()" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-medium">
                        확인
                    </button>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', infoModal);
    }
}

// PWA 설치 프롬프트 초기화
document.addEventListener('DOMContentLoaded', () => {
    window.pwaInstallPrompt = new PWAInstallPrompt();
});

// 전역 함수로 노출
window.showPWAInstallPrompt = () => {
    if (window.pwaInstallPrompt) {
        window.pwaInstallPrompt.showManualInstallPrompt();
    }
};
