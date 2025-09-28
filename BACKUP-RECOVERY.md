# AllSports 백업 및 복구 시스템 가이드

## 🔄 개요

AllSports는 데이터 안전성을 위해 자동 백업 및 복구 시스템을 제공합니다.

- **자동 일일 백업**: 매일 오전 2시 자동 실행
- **수동 백업**: 언제든 실행 가능
- **자동 복구**: 재배포 시 선택적 실행
- **수동 복구**: 특정 백업으로 언제든 복구 가능

## 🗂️ 백업 구성 요소

### 백업에 포함되는 데이터
- SQLite 데이터베이스 (`.sqlite` 파일)
- 데이터베이스 SQL 덤프 (안전장치)
- 환경 설정 파일 (`.env`)
- 스토리지 파일들 (`storage/`)
- 세션 데이터
- 로그 파일

### 백업 파일 위치
- **백업 디렉토리**: `/tmp/allsports-backups/`
- **파일명 형식**: `allsports_backup_YYYYMMDD_HHMMSS.tar.gz`
- **로그 파일**: `/tmp/allsports_backup.log`

## 📅 자동 백업 설정

### CRON 작업 설정
```bash
# 자동 백업 CRON 설정
./scripts/setup-cron.sh
```

### 백업 일정
- **시간**: 매일 오전 2시
- **보존 기간**: 7일 (이후 자동 삭제)
- **로그**: `/tmp/allsports_backup.log`

## 🔧 수동 백업 실행

### 즉시 백업 실행
```bash
# 현재 데이터 백업
./scripts/backup.sh
```

### 백업 상태 확인
```bash
# 백업 파일 목록 확인
ls -la /tmp/allsports-backups/

# 백업 로그 확인
tail -f /tmp/allsports_backup.log

# 최신 백업 정보
ls -lt /tmp/allsports-backups/ | head -5
```

## 🔄 복구 시스템

### 자동 복구 (재배포 시)

CloudType 환경 변수 설정:
```yaml
- name: AUTO_RESTORE_ON_DEPLOY
  value: "true"  # 자동 복구 활성화
```

### 수동 복구

#### 최신 백업으로 복구
```bash
./scripts/restore.sh latest
```

#### 특정 백업으로 복구
```bash
# 사용 가능한 백업 확인
ls /tmp/allsports-backups/

# 특정 백업으로 복구
./scripts/restore.sh allsports_backup_20241128_143000.tar.gz
```

## ⚠️ 중요 안전 수칙

### DB 구조 변경 금지
- **절대로 데이터베이스 스키마를 직접 수정하지 마세요**
- 모든 스키마 변경은 Laravel 마이그레이션을 통해서만 진행
- 백업/복구 시스템은 기존 DB 구조를 완전히 보존합니다

### 복구 전 확인사항
1. 복구는 **현재 데이터를 완전히 덮어씁니다**
2. 복구 전 현재 데이터가 자동으로 임시 백업됩니다
3. 복구 후 애플리케이션 재시작을 권장합니다

## 🔍 모니터링 및 문제 해결

### 백업 상태 모니터링
```bash
# 백업 실행 상태 확인
tail -f /tmp/allsports_backup.log

# CRON 작업 확인
crontab -l | grep allsports

# 디스크 사용량 확인
df -h /tmp
```

### 문제 해결

#### 백업 실패 시
1. 로그 파일 확인: `tail -20 /tmp/allsports_backup.log`
2. 디스크 공간 확인: `df -h /tmp`
3. 권한 확인: `ls -la scripts/`
4. SQLite 파일 확인: `ls -la /tmp/database.sqlite`

#### 복구 실패 시
1. 백업 파일 무결성 확인: `tar -tzf [백업파일]`
2. 임시 백업 파일 확인: `ls -la /tmp/current_backup_*`
3. 데이터베이스 권한 확인: `ls -la /tmp/database.sqlite`

## 📋 유지보수 명령어

### 정기 점검
```bash
# 백업 파일 상태 점검
ls -lah /tmp/allsports-backups/

# 데이터베이스 무결성 검사
sqlite3 /tmp/database.sqlite "PRAGMA integrity_check;"

# 로그 파일 정리 (용량 부족 시)
echo "" > /tmp/allsports_backup.log
```

### 수동 정리
```bash
# 오래된 백업 파일 수동 삭제 (30일 이상)
find /tmp/allsports-backups/ -name "*.tar.gz" -mtime +30 -delete

# 임시 백업 파일 정리
rm -f /tmp/current_backup_*
```

## 🚀 베스트 프랙티스

### 백업 전략
1. **정기 백업**: 매일 자동 백업으로 기본 안전성 확보
2. **중요 작업 전**: 수동 백업으로 추가 안전장치
3. **배포 전**: 현재 상태 백업 후 배포 진행
4. **복구 테스트**: 정기적으로 복구 테스트 실행

### 보안 고려사항
1. 백업 파일에는 민감한 정보가 포함되므로 적절한 권한 관리 필요
2. `.env` 파일 백업 시 보안 키 등 민감 정보 주의
3. 백업 파일의 정기적인 무결성 검사 수행

### 성능 최적화
1. 백업은 트래픽이 적은 시간대(오전 2시)에 실행
2. 백업 파일은 압축하여 저장 공간 절약
3. 오래된 백업 파일 자동 정리로 디스크 공간 관리

## 📞 지원 및 문의

백업/복구 시스템 관련 문제나 질문이 있으시면:

1. 로그 파일을 먼저 확인해주세요
2. 문제 상황과 에러 메시지를 정확히 기록해주세요
3. 가능한 경우 복구 전 임시 백업 파일을 보존해주세요

---

**⚠️ 주의**: 이 백업 시스템은 CloudType 환경에 최적화되어 있습니다. 다른 환경에서 사용 시 경로 및 설정 조정이 필요할 수 있습니다.