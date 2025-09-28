#!/bin/bash

echo "🚀 데이터 보존 배포 시작..."

# 1. 현재 데이터 백업
echo "📦 현재 데이터 백업 중..."
./scripts/backup-data.sh

# 2. Docker 이미지 빌드
echo "🔨 Docker 이미지 빌드 중..."
docker build -t allsports:latest .

# 3. 기존 컨테이너 중지 및 제거
echo "🛑 기존 컨테이너 중지 중..."
docker stop allsports 2>/dev/null || true
docker rm allsports 2>/dev/null || true

# 4. 새 컨테이너 실행 (볼륨 마운트로 데이터 보존)
echo "🏃 새 컨테이너 실행 중..."
docker run -d \
  --name allsports \
  -p 8080:80 \
  -v allsports_data:/var/www/html/database \
  -v allsports_storage:/var/www/html/storage \
  --restart unless-stopped \
  allsports:latest

# 5. 컨테이너가 완전히 시작될 때까지 대기
echo "⏳ 컨테이너 시작 대기 중..."
sleep 10

# 6. 데이터 복원
echo "🔄 데이터 복원 중..."
docker exec allsports /usr/local/bin/restore-data.sh

# 7. 캐시 클리어 및 최적화
echo "🧹 캐시 최적화 중..."
docker exec allsports php artisan config:cache
docker exec allsports php artisan route:cache
docker exec allsports php artisan view:cache

echo "✅ 배포 완료!"
echo "🌐 애플리케이션: http://localhost:8080"
echo "📊 데이터가 보존되었습니다!"

# 컨테이너 로그 표시
echo "📋 컨테이너 로그:"
docker logs allsports --tail 20
