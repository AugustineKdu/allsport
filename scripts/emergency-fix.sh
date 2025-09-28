#!/bin/bash

echo "🚨 긴급 데이터베이스 복구 시작..."

# 데이터베이스 파일 생성
mkdir -p /tmp
touch /tmp/database.sqlite
chmod 777 /tmp/database.sqlite

# 마이그레이션 실행
php artisan migrate --force

# 관리자 계정 생성
php artisan db:seed --force

echo "✅ 긴급 복구 완료!"
