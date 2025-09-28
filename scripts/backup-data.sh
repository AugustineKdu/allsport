#!/bin/bash

# 데이터 백업 스크립트
# 배포 전에 실행하여 데이터를 백업합니다

echo "🔄 데이터 백업 시작..."

# 백업 디렉토리 생성
mkdir -p /tmp/allsports-backup

# SQLite 데이터베이스 백업
if [ -f "database/database.sqlite" ]; then
    cp database/database.sqlite /tmp/allsports-backup/database.sqlite
    echo "✅ 데이터베이스 백업 완료"
else
    echo "⚠️ 데이터베이스 파일을 찾을 수 없습니다"
fi

# 업로드된 파일 백업 (storage/app/public)
if [ -d "storage/app/public" ]; then
    cp -r storage/app/public /tmp/allsports-backup/
    echo "✅ 업로드 파일 백업 완료"
fi

# 환경 설정 백업
if [ -f ".env" ]; then
    cp .env /tmp/allsports-backup/
    echo "✅ 환경 설정 백업 완료"
fi

# JSON 데이터 백업 (Laravel 명령어 사용)
if command -v php &> /dev/null; then
    echo "📦 JSON 데이터 백업 중..."
    php artisan json:backup
    echo "✅ JSON 백업 완료"
else
    echo "⚠️ PHP를 찾을 수 없어 JSON 백업을 건너뜁니다"
fi

echo "🎉 백업 완료: /tmp/allsports-backup/"
