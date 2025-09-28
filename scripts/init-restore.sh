#!/bin/bash

# AllSports 재배포 시 데이터 복원 스크립트
# 이 스크립트는 새로운 환경에서 백업된 데이터를 복원합니다.

echo "🚀 AllSports 데이터 복원 시작..."

cd "$(dirname "$0")/.."

# 1. 환경 설정 확인
if [ ! -f .env ]; then
    echo "⚠️ .env 파일이 없습니다. .env.example에서 복사합니다..."
    cp .env.example .env
    php artisan key:generate
fi

# 2. 데이터베이스 마이그레이션
echo "📊 데이터베이스 마이그레이션 실행..."
php artisan migrate --force

# 3. 백업 파일 확인
if [ -f backups/database_latest.sqlite ]; then
    echo "✅ SQLite 백업 파일 발견!"
    php artisan restore:database latest
elif [ -f backups/database_latest.json ]; then
    echo "✅ JSON 백업 파일 발견!"
    php artisan restore:database latest --json
else
    echo "❌ 백업 파일이 없습니다. 기본 시더 실행..."
    php artisan db:seed --class=RegionSeeder
    php artisan db:seed --class=SportSeeder
fi

# 4. 캐시 정리
echo "🧹 캐시 정리..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "✅ 데이터 복원 완료!"
echo "📌 어드민 계정:"
echo "   - developer@allsports.com / password"
echo "   - owner@allsports.com / password"
