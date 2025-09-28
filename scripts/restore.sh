#!/bin/bash

# AllSports 자동 복구 스크립트
# CloudType 환경에서 백업으로부터 데이터 복구

set -e  # 오류 발생 시 스크립트 중단

echo "🔄 [$(date '+%Y-%m-%d %H:%M:%S')] AllSports 복구 시작..."

# 설정 변수
BACKUP_DIR="/tmp/allsports-backups"
APP_DIR="/app"
DB_FILE="/tmp/database.sqlite"

# 사용법 표시 함수
show_usage() {
    echo "사용법: $0 [백업파일명 또는 'latest']"
    echo ""
    echo "예시:"
    echo "  $0 latest                           # 최신 백업으로 복구"
    echo "  $0 allsports_backup_20241128_143000.tar.gz  # 특정 백업으로 복구"
    echo ""
    echo "사용 가능한 백업 파일:"
    ls -1t "$BACKUP_DIR"/allsports_backup_*.tar.gz 2>/dev/null | head -5 || echo "  백업 파일이 없습니다."
}

# 매개변수 확인
if [ $# -eq 0 ]; then
    echo "❌ 백업 파일을 지정해주세요."
    show_usage
    exit 1
fi

RESTORE_TARGET="$1"

# 복구할 백업 파일 결정
if [ "$RESTORE_TARGET" = "latest" ]; then
    BACKUP_FILE=$(ls -1t "$BACKUP_DIR"/allsports_backup_*.tar.gz 2>/dev/null | head -1)
    if [ -z "$BACKUP_FILE" ]; then
        echo "❌ 백업 파일을 찾을 수 없습니다."
        exit 1
    fi
    echo "📂 최신 백업 파일 사용: $(basename "$BACKUP_FILE")"
else
    BACKUP_FILE="$BACKUP_DIR/$RESTORE_TARGET"
    if [ ! -f "$BACKUP_FILE" ]; then
        echo "❌ 백업 파일을 찾을 수 없습니다: $BACKUP_FILE"
        show_usage
        exit 1
    fi
    echo "📂 지정된 백업 파일 사용: $(basename "$BACKUP_FILE")"
fi

# 백업 파일 무결성 검증
echo "🔍 백업 파일 무결성 검증 중..."
if ! tar -tzf "$BACKUP_FILE" > /dev/null 2>&1; then
    echo "❌ 백업 파일이 손상되었습니다!"
    exit 1
fi
echo "✅ 백업 파일 무결성 확인 완료"

# 복구 확인
echo "⚠️ 경고: 현재 데이터가 모두 덮어씌워집니다!"
echo "복구할 백업: $(basename "$BACKUP_FILE")"

# 자동 복구 모드 (재배포 시)
if [ "${AUTO_RESTORE:-false}" = "true" ]; then
    echo "🤖 자동 복구 모드 활성화"
    CONFIRM="y"
else
    read -p "계속하시겠습니까? (y/N): " CONFIRM
fi

if [ "$CONFIRM" != "y" ] && [ "$CONFIRM" != "Y" ]; then
    echo "❌ 복구가 취소되었습니다."
    exit 1
fi

# 현재 데이터 백업 (안전장치)
echo "💾 현재 데이터 임시 백업 중..."
CURRENT_BACKUP="/tmp/current_backup_$(date +%s).tar.gz"
if [ -f "$DB_FILE" ]; then
    tar -czf "$CURRENT_BACKUP" -C "$(dirname "$DB_FILE")" "$(basename "$DB_FILE")" 2>/dev/null || true
    echo "✅ 현재 데이터 임시 백업: $CURRENT_BACKUP"
fi

# 임시 복구 디렉토리 생성
TEMP_RESTORE_DIR=$(mktemp -d)
cd "$TEMP_RESTORE_DIR"

echo "📦 백업 파일 압축 해제 중..."
tar -xzf "$BACKUP_FILE"

# 백업 정보 표시
if [ -f "backup_info.txt" ]; then
    echo "📊 백업 정보:"
    cat backup_info.txt | head -10
    echo ""
fi

echo "🔄 데이터베이스 복구 중..."

# SQLite 데이터베이스 복구
SQLITE_BACKUP=$(ls backup_*.sqlite 2>/dev/null | head -1)
if [ -n "$SQLITE_BACKUP" ]; then
    echo "✅ SQLite 백업 파일 발견: $SQLITE_BACKUP"

    # 기존 데이터베이스 백업 및 삭제
    if [ -f "$DB_FILE" ]; then
        mv "$DB_FILE" "${DB_FILE}.old.$(date +%s)" 2>/dev/null || true
    fi

    # SQLite 데이터베이스 복원
    cp "$SQLITE_BACKUP" "$DB_FILE"
    chmod 664 "$DB_FILE" 2>/dev/null || true

    # 데이터베이스 무결성 검사
    if sqlite3 "$DB_FILE" "PRAGMA integrity_check;" | grep -q "ok"; then
        echo "✅ 복구된 데이터베이스 무결성 검사 통과"
    else
        echo "❌ 복구된 데이터베이스 무결성 검사 실패"

        # SQL 덤프로 복구 시도
        SQL_DUMP=$(ls database_dump_*.sql 2>/dev/null | head -1)
        if [ -n "$SQL_DUMP" ]; then
            echo "🔄 SQL 덤프로 복구 시도 중..."
            rm -f "$DB_FILE"
            sqlite3 "$DB_FILE" < "$SQL_DUMP"

            if sqlite3 "$DB_FILE" "PRAGMA integrity_check;" | grep -q "ok"; then
                echo "✅ SQL 덤프 복구 성공"
            else
                echo "❌ SQL 덤프 복구도 실패"
                exit 1
            fi
        else
            exit 1
        fi
    fi
else
    echo "⚠️ SQLite 백업 파일을 찾을 수 없음"
fi

echo "📁 애플리케이션 파일 복구 중..."

# 환경 설정 파일 복구
if [ -f ".env" ]; then
    echo "📄 환경 설정 파일 복구 중..."
    cp ".env" "$APP_DIR/.env" 2>/dev/null || echo "⚠️ .env 파일 복구 실패"
fi

# 스토리지 파일 복구
if [ -d "storage" ]; then
    echo "📁 스토리지 파일 복구 중..."
    cd "$APP_DIR"

    # 스토리지 디렉토리 백업
    if [ -d "storage" ]; then
        mv storage "storage.old.$(date +%s)" 2>/dev/null || true
    fi

    # 스토리지 복원
    cp -r "$TEMP_RESTORE_DIR/storage" . 2>/dev/null || echo "⚠️ 스토리지 복구 실패"

    # 권한 설정
    chmod -R 755 storage 2>/dev/null || true
fi

# 임시 파일 정리
cd /
rm -rf "$TEMP_RESTORE_DIR"

# Laravel 캐시 클리어
echo "🧹 Laravel 캐시 클리어 중..."
cd "$APP_DIR"
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# 복구 완료 로그
RESTORE_LOG_FILE="/tmp/allsports_restore.log"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] 복구 완료: $(basename "$BACKUP_FILE")" >> "$RESTORE_LOG_FILE"

echo "🎉 [$(date '+%Y-%m-%d %H:%M:%S')] AllSports 복구 완료!"
echo "   복구된 백업: $(basename "$BACKUP_FILE")"
echo "   현재 데이터 임시 백업: $CURRENT_BACKUP"

# 데이터베이스 상태 확인
if [ -f "$DB_FILE" ]; then
    echo "📊 복구된 데이터베이스 상태:"
    echo "   파일 크기: $(du -h "$DB_FILE" | cut -f1)"

    # 테이블 수 확인
    TABLE_COUNT=$(sqlite3 "$DB_FILE" ".tables" | wc -w)
    echo "   테이블 수: $TABLE_COUNT"

    # 사용자 수 확인 (users 테이블이 존재하는 경우)
    if sqlite3 "$DB_FILE" ".tables" | grep -q "users"; then
        USER_COUNT=$(sqlite3 "$DB_FILE" "SELECT COUNT(*) FROM users;" 2>/dev/null || echo "0")
        echo "   사용자 수: $USER_COUNT"
    fi
fi

echo "✨ 복구 프로세스 완료!"
echo ""
echo "⚠️ 참고:"
echo "   - 현재 데이터가 임시 백업되었습니다: $CURRENT_BACKUP"
echo "   - 문제가 발생하면 이 파일로 되돌릴 수 있습니다"
echo "   - 애플리케이션을 다시 시작하는 것을 권장합니다"