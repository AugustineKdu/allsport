# 🔄 데이터 영속성 배포 가이드

## 문제 해결: 배포 시 데이터 롤백 방지

배포할 때마다 데이터가 사라지는 문제를 해결하기 위한 여러 가지 방법을 제공합니다.

## 🎯 권장 해결책

### 방법 1: CloudType MySQL 사용 (가장 권장)

1. **cloudtype.yml 설정 확인**
   ```yaml
   mysql:
     env:
       - name: MYSQL_ROOT_PASSWORD
         value: "allsports_password_2024"
       - name: MYSQL_DATABASE
         value: allsports
     storage:
       size: 10Gi
       class: ssd
   ```

2. **환경변수 설정**
   - `DB_CONNECTION=mysql`
   - `DB_HOST=mysql`
   - `DB_DATABASE=allsports`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=${MYSQL_ROOT_PASSWORD}`

3. **배포 후 확인**
   - CloudType 대시보드에서 MySQL 서비스가 실행 중인지 확인
   - 데이터베이스 연결 테스트

### 방법 2: Docker 볼륨 사용 (로컬 배포)

```bash
# 데이터 보존 배포 스크립트 실행
./deploy-with-data.sh
```

이 스크립트는:
- ✅ 배포 전 데이터 백업
- ✅ Docker 볼륨으로 데이터 영속성 보장
- ✅ 배포 후 데이터 복원
- ✅ 캐시 최적화

### 방법 3: 수동 백업/복원

```bash
# 배포 전 백업
./scripts/backup-data.sh

# 배포 후 복원
./scripts/restore-data.sh
```

## 🛠 기술적 세부사항

### 데이터 보존 대상
- **데이터베이스**: SQLite 파일 또는 MySQL 데이터
- **업로드 파일**: `storage/app/public` 디렉토리
- **환경 설정**: `.env` 파일 (선택적)

### 볼륨 마운트 설정
```yaml
volumes:
  - allsports_database:/var/www/html/database
  - allsports_storage:/var/www/html/storage
  - allsports_logs:/var/www/html/storage/logs
```

## 📋 배포 체크리스트

### CloudType 배포 전
- [ ] `cloudtype.yml`에서 MySQL 설정 확인
- [ ] 환경변수가 올바르게 설정되었는지 확인
- [ ] MySQL 서비스가 활성화되었는지 확인

### 로컬 Docker 배포 전
- [ ] `deploy-with-data.sh` 실행 권한 확인
- [ ] 기존 컨테이너 정리
- [ ] 백업 디렉토리 공간 확인

### 배포 후 확인
- [ ] 데이터베이스 연결 테스트
- [ ] 기존 데이터 유지 확인
- [ ] 새 데이터 입력 테스트
- [ ] 파일 업로드 기능 확인

## 🚨 문제 해결

### 데이터가 여전히 사라지는 경우
1. **MySQL 연결 확인**
   ```bash
   docker exec -it allsports php artisan tinker
   >>> DB::connection()->getPdo();
   ```

2. **볼륨 마운트 확인**
   ```bash
   docker volume ls | grep allsports
   ```

3. **백업 파일 확인**
   ```bash
   ls -la /tmp/allsports-backup/
   ```

### 성능 최적화
- MySQL 사용 시 인덱스 최적화
- 캐시 설정 확인
- 로그 레벨 조정

## 🎉 완료!

이제 배포할 때마다 데이터가 보존됩니다!

**추천**: CloudType에서 MySQL 서비스를 사용하는 것이 가장 안정적입니다.
