#!/bin/bash

# 색상 정의
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 경로 설정
PERSISTENT_DIR="/app/persistent"
PERSISTENT_DB="${PERSISTENT_DIR}/database.sqlite"
PERSISTENT_STORAGE="${PERSISTENT_DIR}/storage"
APP_DB="/tmp/database.sqlite"
APP_STORAGE="storage/app"

echo -e "${YELLOW}🔍 AllSports 자동 복구 시스템 시작...${NC}"

# 영구 저장소 디렉토리 생성
mkdir -p "${PERSISTENT_DIR}"
mkdir -p "/tmp"

# 데이터베이스 복구
if [ -f "$PERSISTENT_DB" ]; then
    echo -e "${GREEN}✅ 영구 저장소에서 데이터베이스 발견!${NC}"
    cp "$PERSISTENT_DB" "$APP_DB"
    echo -e "${GREEN}✅ 사용자 데이터 복구 완료!${NC}"
else
    echo -e "${YELLOW}⚠️  영구 저장소에 데이터베이스 없음. 백업에서 복구 시도...${NC}"
    
    # Git에 저장된 백업에서 복구
    if [ -f "backups/database_latest.sqlite" ]; then
        echo -e "${GREEN}📋 백업 파일에서 복구 중...${NC}"
        cp "backups/database_latest.sqlite" "$APP_DB"
        cp "$APP_DB" "$PERSISTENT_DB"
        echo -e "${GREEN}✅ 백업에서 복구 완료!${NC}"
    else
        echo -e "${YELLOW}🔄 새로운 데이터베이스 초기화...${NC}"
        
        # 빈 데이터베이스 생성
        touch "$APP_DB"
        
        # 마이그레이션 실행
        php artisan migrate --force
        
        # 기본 시더 실행
        php artisan db:seed --force
        
        # 영구 저장소에 저장
        cp "$APP_DB" "$PERSISTENT_DB"
        
        echo -e "${GREEN}✅ 초기 설정 완료!${NC}"
    fi
fi

# 스토리지 디렉토리 복구
if [ -d "$PERSISTENT_STORAGE" ]; then
    echo -e "${GREEN}📁 영구 저장소의 스토리지 복구 중...${NC}"
    rsync -av "$PERSISTENT_STORAGE/" "$APP_STORAGE/"
else
    echo -e "${YELLOW}📁 스토리지 디렉토리 초기화...${NC}"
    mkdir -p "$PERSISTENT_STORAGE"
fi

# 심볼릭 링크 생성 (public storage)
php artisan storage:link --force

# 캐시 정리 및 최적화
echo -e "${YELLOW}🔧 캐시 최적화 중...${NC}"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 스케줄러 설정 (백업 자동화)
echo -e "${YELLOW}⏰ 자동 백업 스케줄러 설정...${NC}"
(crontab -l 2>/dev/null; echo "* * * * * cd /app && php artisan schedule:run >> /dev/null 2>&1") | crontab -

# 매 시간마다 영구 저장소에 백업
(crontab -l 2>/dev/null; echo "0 * * * * cp /tmp/database.sqlite /app/persistent/database.sqlite") | crontab -

echo -e "${GREEN}🎯 AllSports 준비 완료!${NC}"
echo -e "${GREEN}   - 데이터베이스: $APP_DB${NC}"
echo -e "${GREEN}   - 영구 저장소: $PERSISTENT_DIR${NC}"
echo -e "${GREEN}   - 자동 백업: 매일 오전 2시${NC}"
