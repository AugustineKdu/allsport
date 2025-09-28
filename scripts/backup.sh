#!/bin/bash

# AllSports 자동 백업 스크립트
# CloudType 환경에서 SQLite 데이터베이스 백업

set -e  # 오류 발생 시 스크립트 중단

echo "🔄 [$(date '+%Y-%m-%d %H:%M:%S')] AllSports 백업 시작..."

# 설정 변수
BACKUP_DIR="/tmp/allsports-backups"
APP_DIR="/app"
DB_FILE="/tmp/database.sqlite"
TIMESTAMP=$(date '+%Y%m%d_%H%M%S')
BACKUP_FILE="allsports_backup_${TIMESTAMP}.tar.gz"

# 백업 디렉토리 생성
mkdir -p "$BACKUP_DIR"

# 임시 작업 디렉토리 생성
TEMP_DIR=$(mktemp -d)
cd "$TEMP_DIR"

echo "📦 데이터베이스 백업 중..."

# SQLite 데이터베이스 백업
if [ -f "$DB_FILE" ]; then
    echo "✅ SQLite 데이터베이스 발견: $DB_FILE"

    # 데이터베이스 무결성 검사
    if sqlite3 "$DB_FILE" "PRAGMA integrity_check;" | grep -q "ok"; then
        echo "✅ 데이터베이스 무결성 검사 통과"

        # SQLite 백업 (덤프 방식)
        sqlite3 "$DB_FILE" ".backup backup_${TIMESTAMP}.sqlite"

        # SQL 덤프도 생성 (추가 안전장치)
        sqlite3 "$DB_FILE" ".dump" > "database_dump_${TIMESTAMP}.sql"

        echo "✅ 데이터베이스 백업 완료"
    else
        echo "❌ 데이터베이스 무결성 검사 실패"
        exit 1
    fi
else
    echo "⚠️ SQLite 데이터베이스 파일을 찾을 수 없음: $DB_FILE"
fi

echo "📄 애플리케이션 파일 백업 중..."

# 중요 설정 파일들 백업
IMPORTANT_FILES=(
    ".env"
    "storage/logs"
    "storage/framework/sessions"
    "storage/app"
)

# 애플리케이션 디렉토리로 이동
cd "$APP_DIR"

# 백업에 포함할 파일들 확인 및 복사
for file in "${IMPORTANT_FILES[@]}"; do
    if [ -e "$file" ]; then
        echo "📁 백업 중: $file"
        mkdir -p "$TEMP_DIR/$(dirname "$file")"
        cp -r "$file" "$TEMP_DIR/$file" 2>/dev/null || echo "⚠️ $file 백업 실패"
    else
        echo "⚠️ 파일을 찾을 수 없음: $file"
    fi
done

# 백업 정보 파일 생성
cd "$TEMP_DIR"
cat > backup_info.txt << EOF
AllSports 백업 정보
===================
백업 일시: $(date '+%Y-%m-%d %H:%M:%S')
백업 타입: 자동 일일 백업
Git 커밋: $(cd "$APP_DIR" && git rev-parse HEAD 2>/dev/null || echo "Unknown")
Git 브랜치: $(cd "$APP_DIR" && git branch --show-current 2>/dev/null || echo "Unknown")
PHP 버전: $(php -v | head -n1)
환경: CloudType Production

백업 내용:
- SQLite 데이터베이스 (.sqlite 파일)
- 데이터베이스 SQL 덤프
- 환경 설정 파일 (.env)
- 스토리지 파일들
- 세션 데이터
- 로그 파일

복구 방법:
1. 백업 파일 압축 해제
2. 데이터베이스 파일 복원
3. 환경 설정 파일 복원
4. 스토리지 디렉토리 복원
EOF

echo "🗜️ 백업 파일 압축 중..."

# 백업 파일 압축
tar -czf "$BACKUP_DIR/$BACKUP_FILE" .

echo "✅ 백업 압축 완료: $BACKUP_DIR/$BACKUP_FILE"

# 임시 디렉토리 정리
cd /
rm -rf "$TEMP_DIR"

# 백업 크기 확인
BACKUP_SIZE=$(du -h "$BACKUP_DIR/$BACKUP_FILE" | cut -f1)
echo "📊 백업 파일 크기: $BACKUP_SIZE"

# 오래된 백업 파일 정리 (7일 이상된 파일 삭제)
echo "🧹 오래된 백업 파일 정리 중..."
find "$BACKUP_DIR" -name "allsports_backup_*.tar.gz" -mtime +7 -delete
REMAINING_BACKUPS=$(ls -1 "$BACKUP_DIR"/allsports_backup_*.tar.gz 2>/dev/null | wc -l)
echo "📊 남은 백업 파일 수: $REMAINING_BACKUPS"

# 백업 로그 기록
LOG_FILE="/tmp/allsports_backup.log"
echo "[$(date '+%Y-%m-%d %H:%M:%S')] 백업 성공: $BACKUP_FILE (크기: $BACKUP_SIZE)" >> "$LOG_FILE"

# 백업 성공 로그
echo "🎉 [$(date '+%Y-%m-%d %H:%M:%S')] AllSports 백업 완료!"
echo "   백업 파일: $BACKUP_DIR/$BACKUP_FILE"
echo "   파일 크기: $BACKUP_SIZE"
echo "   남은 백업: $REMAINING_BACKUPS개"

# 백업 무결성 검증
echo "🔍 백업 무결성 검증 중..."
if tar -tzf "$BACKUP_DIR/$BACKUP_FILE" > /dev/null 2>&1; then
    echo "✅ 백업 파일 무결성 확인 완료"
else
    echo "❌ 백업 파일 손상 감지!"
    exit 1
fi

echo "✨ 백업 프로세스 완료!"