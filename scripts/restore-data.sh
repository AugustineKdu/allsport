#!/bin/bash

# 데이터 복원 스크립트
# 배포 후에 실행하여 데이터를 복원합니다

echo "🔄 데이터 복원 시작..."

BACKUP_DIR="/tmp/allsports-backup"

# 백업 디렉토리 확인
if [ ! -d "$BACKUP_DIR" ]; then
    echo "⚠️ 백업 디렉토리를 찾을 수 없습니다: $BACKUP_DIR"
    echo "새로운 데이터베이스를 초기화합니다..."
    php artisan migrate --force
    php artisan db:seed --force
    exit 0
fi

# 데이터베이스 복원
if [ -f "$BACKUP_DIR/database.sqlite" ]; then
    cp "$BACKUP_DIR/database.sqlite" database/database.sqlite
    chmod 666 database/database.sqlite
    echo "✅ 데이터베이스 복원 완료"
else
    echo "⚠️ 백업된 데이터베이스를 찾을 수 없습니다. 새로 초기화합니다..."
    php artisan migrate --force
    php artisan db:seed --force
fi

# 업로드 파일 복원
if [ -d "$BACKUP_DIR/public" ]; then
    cp -r "$BACKUP_DIR/public" storage/app/
    echo "✅ 업로드 파일 복원 완료"
fi

# 환경 설정 복원 (선택적)
if [ -f "$BACKUP_DIR/.env" ]; then
    echo "⚠️ 백업된 환경 설정이 있습니다. 수동으로 확인하세요."
fi

# 권한 설정
chown -R www-data:www-data storage
chmod -R 755 storage

echo "🎉 데이터 복원 완료!"
