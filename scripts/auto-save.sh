#!/bin/bash

# 색상 정의
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 경로 설정
PERSISTENT_DIR="/app/persistent"
PERSISTENT_DB="${PERSISTENT_DIR}/database.sqlite"
PERSISTENT_STORAGE="${PERSISTENT_DIR}/storage"
APP_DB="/tmp/database.sqlite"
APP_STORAGE="storage/app"

echo -e "${YELLOW}💾 데이터베이스 영구 저장소에 저장 중...${NC}"

# 영구 저장소 디렉토리 확인
mkdir -p "${PERSISTENT_DIR}"

# 데이터베이스 저장
if [ -f "$APP_DB" ]; then
    cp "$APP_DB" "$PERSISTENT_DB"
    echo -e "${GREEN}✅ 데이터베이스가 영구 저장소에 저장되었습니다!${NC}"
    echo -e "${GREEN}   경로: $PERSISTENT_DB${NC}"
else
    echo -e "${RED}❌ 데이터베이스 파일을 찾을 수 없습니다: $APP_DB${NC}"
fi

# 스토리지 디렉토리 저장
if [ -d "$APP_STORAGE" ]; then
    echo -e "${YELLOW}📁 스토리지 파일 저장 중...${NC}"
    mkdir -p "$PERSISTENT_STORAGE"
    rsync -av "$APP_STORAGE/" "$PERSISTENT_STORAGE/"
    echo -e "${GREEN}✅ 스토리지 파일이 영구 저장소에 저장되었습니다!${NC}"
fi

echo -e "${GREEN}💾 모든 데이터가 안전하게 저장되었습니다!${NC}"
