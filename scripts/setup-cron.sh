#!/bin/bash

# AllSports 자동 백업 CRON 설정 스크립트
# CloudType 환경에서 일일 백업 자동화

echo "⏰ AllSports 자동 백업 CRON 설정 중..."

# 현재 디렉토리 저장
SCRIPT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
APP_DIR=$(dirname "$SCRIPT_DIR")

# 백업 스크립트 경로
BACKUP_SCRIPT="$SCRIPT_DIR/backup.sh"

# 백업 스크립트 실행 권한 설정
chmod +x "$BACKUP_SCRIPT"

# CRON 작업 내용
CRON_JOB="0 2 * * * cd $APP_DIR && $BACKUP_SCRIPT >> /tmp/allsports_backup.log 2>&1"

# 기존 AllSports 백업 CRON 작업 제거
echo "🧹 기존 백업 CRON 작업 정리 중..."
(crontab -l 2>/dev/null | grep -v "allsports.*backup" | grep -v "$BACKUP_SCRIPT") | crontab -

# 새 CRON 작업 추가
echo "📅 새 백업 CRON 작업 추가 중..."
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -

# CRON 서비스 상태 확인
if command -v systemctl >/dev/null 2>&1; then
    echo "🔄 CRON 서비스 재시작 중..."
    systemctl restart cron 2>/dev/null || systemctl restart crond 2>/dev/null || echo "⚠️ CRON 서비스 재시작 실패"
fi

# 설정 확인
echo "✅ CRON 작업 설정 완료!"
echo ""
echo "📋 설정된 백업 일정:"
echo "   - 시간: 매일 오전 2시"
echo "   - 명령: $BACKUP_SCRIPT"
echo "   - 로그: /tmp/allsports_backup.log"
echo ""
echo "📋 현재 CRON 작업 목록:"
crontab -l 2>/dev/null | grep -E "(allsports|backup)" || echo "   설정된 백업 작업이 없습니다."
echo ""

# 즉시 백업 테스트 제안
read -p "지금 백업 테스트를 실행하시겠습니까? (y/N): " TEST_BACKUP
if [ "$TEST_BACKUP" = "y" ] || [ "$TEST_BACKUP" = "Y" ]; then
    echo "🧪 백업 테스트 실행 중..."
    cd "$APP_DIR"
    "$BACKUP_SCRIPT"
else
    echo "ℹ️ 백업 테스트를 건너뛰었습니다."
fi

echo ""
echo "✨ 자동 백업 설정 완료!"
echo ""
echo "📚 유용한 명령어들:"
echo "   백업 실행:        $BACKUP_SCRIPT"
echo "   복구 실행:        $SCRIPT_DIR/restore.sh latest"
echo "   백업 로그 확인:    tail -f /tmp/allsports_backup.log"
echo "   CRON 작업 확인:    crontab -l"
echo "   백업 파일 목록:    ls -la /tmp/allsports-backups/"