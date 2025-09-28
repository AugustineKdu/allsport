#!/bin/bash

# AllSports 백업 자동 푸시 스크립트
# 이 스크립트는 매일 자동으로 실행되어 백업 파일을 GitHub에 푸시합니다.

cd "$(dirname "$0")/.."

# 백업 실행
echo "Starting database backup..."
php artisan backup:database
php artisan backup:database --json

# Git 설정
git config user.name "AllSports Backup Bot"
git config user.email "backup@allsports.com"

# 백업 파일 추가
git add backups/database_latest.sqlite
git add backups/database_latest.json

# 커밋
TIMESTAMP=$(date +"%Y-%m-%d %H:%M:%S")
git commit -m "자동 백업: $TIMESTAMP

- SQLite 데이터베이스 백업
- JSON 형식 데이터 백업
- 자동 백업 시스템에 의해 생성됨"

# 푸시
git push origin $(git branch --show-current)

echo "Backup pushed to GitHub successfully!"
