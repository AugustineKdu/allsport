# AllSports Web Platform

This document describes the architecture, data model, user flows and key logic for the **AllSports** web platform. It is designed as a mobile‑friendly web application built with **Laravel** and **Blade**.  The goal is to offer a lightweight yet functional portal for amateur sports teams to manage memberships, record matches and view rankings across regions.

## 데이터 백업 및 복원 시스템

### 자동 백업
- 매일 오전 2시(KST)에 자동으로 데이터베이스 백업
- SQLite 파일과 JSON 형식으로 이중 백업
- `backups/database_latest.sqlite` 및 `backups/database_latest.json` 파일로 저장

### 수동 백업
```bash
# SQLite 파일 백업
php artisan backup:database

# JSON 형식 백업
php artisan backup:database --json

# GitHub에 백업 푸시
./scripts/push-backup.sh
```

### 데이터 복원
```bash
# 최신 백업에서 복원
php artisan restore:database

# 특정 백업 파일에서 복원
php artisan restore:database 2025-09-28_120000

# JSON 파일에서 복원
php artisan restore:database --json

# 재배포 시 전체 초기화 및 복원
./scripts/init-restore.sh
```

### 고정 어드민 계정
- developer@allsports.com / password
- owner@allsports.com / password

## 1. Goals and Scope

- Deliver an **app‑like mobile web experience**: a fixed bottom tab bar with five sections (Home, Teams, Matches, Rankings and My Page) while allowing only the central content to change.
- Support **user authentication and onboarding**: users create an account, set their nickname, region and preferred sport, then start using the platform.
- Allow users without a team to **search and apply to join local teams**, or create their own team with minimal friction.
- Provide **match management** (both manual and automatic matchmaking), results recording and a ranking system filtered by nation, city and district.
- Enable **admin management** of regions (cities and districts), teams and matches, with simple toggles for activation.

The system currently focuses on **football (soccer)** and **futsal**.  Additional sports can be added through the `sports` table.

## 2. User Flow

1. **Registration & Login** – Users sign up with email and password (Laravel Breeze is recommended for scaffolding).  Email verification can be deferred to later versions for faster launch.
2. **Onboarding** – New users complete a two‑step process:
   - Enter a nickname.
   - Select their region (`city` → `district`) and sport (currently “축구” or “풋살”).
   The onboarding flag (`onboarding_done`) prevents access to core features until completed.
3. **Team Search** – After onboarding, users land on the **Teams** tab:
   - If they do **not** belong to a team: they see a search bar, filters for city/district/sport, and a list of teams with a **Join** button.  Clicking Join opens a form to submit an application (optional message).
   - If they **do** belong to a team: a **My Team** card appears at the top showing team stats, rank in their district, upcoming matches and currently online members.  The list below remains available for browsing other teams.
4. **Join Requests** – Team membership requests are stored with status `pending`.  Team owners (or admins) can approve or reject them.  Approved members see the team in their profile.  Users may leave a team, setting their membership to `left`.
5. **Match Management** – Matches can be created by team owners or admins.  A match has a status (`예정`, `진행중`, `완료`, `취소`).  When completed, scores are recorded and each team’s win/draw/loss counters and points are updated (3 points per win, 1 per draw).  A safeguard ensures that points are updated only once (`finalized_at` guard).
6. **Rankings** – The **Rankings** tab provides three scopes:
   - **All**: aggregated ranking across all regions.
   - **City**: ranking filtered by a specific city (e.g., Seoul).
   - **District**: ranking within a selected city/district (e.g., Seoul‑Songpa).
   On the district view, users can quickly switch to see the parent city’s overall ranking.
7. **Automatic Matchmaking (MVP)** – A background job suggests opponents for teams based on proximity and competitive similarity:
   - *Region proximity*: Teams in the same district are preferred over teams in the same city, which in turn are preferred over other areas.
   - *Points difference*: Smaller differences are favoured to ensure fair matches.
   - *Recent meetings*: Teams that have not faced each other recently rank higher in the queue.
   The system sends a proposal to both team owners.  If both confirm within a configurable time window (e.g. 24 hours), the match is created automatically.  If either declines or the window expires, the next best pairing is attempted.

## 3. Data Model

### 3.1 Users

| Column         | Type    | Notes                                       |
|---------------|---------|---------------------------------------------|
| `id`          | integer | Primary key                                 |
| `email`       | string  | Unique identifier for login                 |
| `password`    | string  | Hashed password (Bcrypt)                    |
| `nickname`    | string  | Display name                                |
| `city`        | string  | Selected city (`regions` table)             |
| `district`    | string  | Selected district                           |
| `selected_sport` | string | Sport preference (e.g., `축구`)             |
| `onboarding_done` | bool | Completed onboarding?                      |
| `role`        | enum    | `user`, `team_owner`, `admin` (default `user`) |
| `created_at`  | timestamp |                                           |

### 3.2 Teams

| Column          | Type    | Notes                                                   |
|----------------|---------|---------------------------------------------------------|
| `id`           | integer | Primary key                                             |
| `team_name`    | string  | Display name                                            |
| `team_name_canon` | string | Normalised name for uniqueness enforcement             |
| `slug`         | string  | URL‑friendly identifier (city‑district‑canon)           |
| `sport`        | string  | Sport (`축구` or `풋살`)                                |
| `city`         | string  | City (must exist in `regions`)                          |
| `district`     | string  | District (must exist in `regions`)                      |
| `owner_user_id` | integer | User ID of the team owner                               |
| `wins`         | integer | Default `0`                                             |
| `draws`        | integer | Default `0`                                             |
| `losses`       | integer | Default `0`                                             |
| `points`       | integer | Default `0`; computed as `3*wins + draws`               |
| `join_code`    | string  | Optional code for quick join (6 alphanumeric)           |
| `created_at`   | timestamp |                                                       |

Teams have a **unique constraint** on `(sport, city, district, team_name_canon)` to prevent duplicate names within the same region and sport.  The canonical name is generated by lowering case, removing whitespace and punctuation, and replacing common suffixes such as “fc/에프씨/ＦＣ” or “united/유나이티드” with a standard form.

### 3.3 Team Members

| Column        | Type    | Notes                                     |
|--------------|---------|-------------------------------------------|
| `id`         | integer | Primary key                               |
| `team_id`    | integer | References `teams.id`                     |
| `user_id`    | integer | References `users.id`                     |
| `role`       | enum    | `owner` or `member`                       |
| `status`     | enum    | `pending`, `approved`, `rejected`, `left` |
| `message`    | string  | Application message (optional)            |
| `joined_at`  | timestamp | When membership was approved             |
| `last_active_at` | timestamp | Updated via heartbeat for online status |

This table tracks membership and application status.  A unique constraint on `(team_id, user_id)` ensures a user cannot apply multiple times simultaneously.

### 3.4 Matches

| Column           | Type    | Notes                                                  |
|-----------------|---------|--------------------------------------------------------|
| `id`            | integer | Primary key                                            |
| `sport`         | string  | Sport (`축구`/`풋살`)                                  |
| `city`          | string  | City                                                   |
| `district`      | string  | District                                               |
| `home_team_id`  | integer | FK to `teams.id`                                       |
| `away_team_id`  | integer | FK to `teams.id`                                       |
| `home_team_name`| string  | Cached for display                                     |
| `away_team_name`| string  | Cached for display                                     |
| `match_date`    | date    | Date of play                                           |
| `match_time`    | time    | Time of play                                           |
| `status`        | enum    | `예정`, `진행중`, `완료`, `취소`                         |
| `home_score`    | integer | Nullable; set when completed                           |
| `away_score`    | integer | Nullable; set when completed                           |
| `finalized_at`  | timestamp | Non‑null if match results already applied            |
| `created_by`    | integer | User ID who scheduled the match (owner or admin)       |
| `created_at`    | timestamp |                                                     |

### 3.5 Sports and Regions

| Table    | Purpose                              |
|---------|--------------------------------------|
| `sports` | Seed data for available sports.  Fields: `sport_name` (PK), `icon`, `is_active` (bool), `status` (text). |
| `regions`| Admin‑managed list of cities and districts.  Fields: `id`, `city`, `district`, `is_active` (bool).  Unique index on `(city, district)` prevents duplicates.  Only active entries can be selected in forms. |

## 4. Permissions and Roles

The platform uses a minimal role system:

- **User** – Default role; can view teams, submit join requests, leave teams, view matches and rankings.
- **Team Owner** – A user who owns a team; can create and edit their team, approve or reject membership requests, schedule matches for their team and update match results.
- **Admin** – Site administrator; can manage regions, sports, teams and matches, including editing any match or team.  Admins can also approve membership on behalf of team owners if desired.

Middleware enforce access to sensitive operations:

- `auth` – Required for any operation beyond browsing.  Guests may view listings but must log in to apply or create.
- `team.owner` – Ensures the current user is the owner of the team referenced by the route.
- `admin` – Grants access to admin CRUD interfaces for regions, teams and matches.

## 5. Onboarding and Profiles

During onboarding, users must provide:

1. **Nickname** – A display name; stored in `users.nickname`.
2. **Region** – Select a city then district from active `regions`.  These values populate `users.city` and `users.district`.
3. **Sport** – Choose a sport from active `sports`; stored in `users.selected_sport`.

Users can later edit these details in the **My Page** section.  Changing the region will affect default filters on teams and rankings.

## 6. Team Management

### Creation & Editing

- Only authenticated users may create teams.  On creation, the creator becomes the **owner** and their `role` is upgraded to `team_owner`.
- Team names undergo **canonical normalisation**: lowercasing, removing whitespace and punctuation and mapping common suffixes (e.g., FC ↦ `fc`, United ↦ `united`).  A unique constraint ensures no two teams share the same `(sport, city, district, canon)` combination.  Before final insertion, the system checks for similar names (using Levenshtein distance) and warns the user to avoid near‑duplicates.
- Owners can update team name, sport, region and other details but cannot violate the uniqueness rule.  Members do not have edit rights.

### Membership Requests

- Users apply to join by clicking **Join** on a team card or page.  This creates a `team_members` record with `status = pending` and an optional message.
- Team owners (and optionally admins) can view pending requests and approve or reject them.  Approval sets `status = approved` and populates `joined_at`.  Rejection sets `status = rejected`.
- Approved members appear in the team roster.  They can later leave the team (`status = left`), preserving the history.

### Online Presence

To show which members are currently active, the front‑end can ping an endpoint every minute (e.g. `/presence/ping`).  This updates `team_members.last_active_at`.  A member is considered **online** if `now – last_active_at ≤ 2 minutes`.  Offline members are shown without the green indicator.

## 7. Match Management

### Manual Scheduling

- Only team owners and admins can create matches.  The form includes sport, region (city and district), home and away teams, date and time.  Only teams within the selected region and sport are eligible.
- Matches start with `status = 예정`.  When the day arrives, owners or admins can change to `진행중` and later to `완료` with final scores.  Points are applied automatically upon first change to `완료` via the algorithm:

  ```php
  // Points: 3×wins + 1×draws
  if ($match->finalized_at) return; // already applied
  if ($homeScore > $awayScore) { $homeTeam->wins++; $awayTeam->losses++; }
  elseif ($homeScore < $awayScore) { $awayTeam->wins++; $homeTeam->losses++; }
  else { $homeTeam->draws++; $awayTeam->draws++; }
  $homeTeam->points = 3*$homeTeam->wins + $homeTeam->draws;
  $awayTeam->points = 3*$awayTeam->wins + $awayTeam->draws;
  $match->finalized_at = now();
  ```

- Editing a finalized match does not re‑apply points unless explicitly rolled back by an admin.

### Automatic Matchmaking (Optional MVP)

Automatic matchmaking aims to suggest fair and convenient opponents for teams that opt in.  The algorithm considers:

1. **Region Score** – Teams in the same district score highest, followed by the same city but different district, then other cities.  For example: +30 if same district, +15 if same city, +0 otherwise.
2. **Point Difference** – Smaller absolute differences in points yield higher scores.  Example weighting: difference ≤50 → +15, difference ≤100 → +10, else +0.
3. **Recent Encounters** – If teams have not played each other recently (e.g. within the last 3 months) add +5; else 0.

Each candidate pair receives a total match score.  Teams with the highest scores are proposed first.  When a match is proposed:

- Both team owners receive a notification in their dashboard (and optionally by email).  The proposal includes suggested date/time and venue (which may be predefined or left blank for negotiation).
- Each owner may **accept** or **decline**.  If both accept within a preset validity window (e.g., 24 hours), the match is created with status `예정`.  If one declines or the timer expires, the proposal is invalidated and the algorithm looks for the next best pairing.
- Owners can specify availability blocks or preferred venues to refine proposals.  This is optional for the MVP and can be expanded later.

Auto matchmaking should run as a scheduled job (e.g. hourly) to update suggestions.  Administrators can adjust the weights and validity window via configuration.

## 8. Rankings

Ranking lists sort teams by **points** (descending), with **wins** as a tiebreaker.  Each ranking scope is obtained via simple queries:

- **National (All)** – `WHERE sport = :sport`.
- **City** – `WHERE sport = :sport AND city = :city`.
- **District** – `WHERE sport = :sport AND city = :city AND district = :district`.

On the district page, a summary of the top 5 teams in the parent city is shown to provide context.  Users can click a button to switch to the city ranking.

## 9. Region Management

Administrators manage the `regions` table via an interface under `/admin/regions`.  They can add new cities or districts, edit names and toggle their active status.  The combination `(city, district)` must be unique.  Deactivated regions cannot be selected in team or match forms but remain visible for historical data.

## 10. API Endpoints (Example)

Here is a simplified list of routes demonstrating the main functionality.  Actual implementation may vary with controllers, middleware and request validation:

- **Authentication**
  - `POST /register` – Create a new user.
  - `POST /login` – Authenticate a user.

- **Onboarding**
  - `GET /onboarding` – View onboarding form.
  - `POST /onboarding` – Submit nickname, region and sport.

- **Teams**
  - `GET /teams` – List teams with filters (`city`, `district`, `sport`, `search`).
  - `POST /teams` – Create a team (authenticated).
  - `GET /teams/{slug}` – View team details.
  - `POST /teams/{slug}/apply` – Submit membership request.
  - `POST /teams/{slug}/leave` – Leave the team.

- **Team Membership Management**
  - `GET /owner/teams/{slug}/members` – View pending and approved members (owner only).
  - `POST /owner/teams/{slug}/members/{memberId}/approve` – Approve a request.
  - `POST /owner/teams/{slug}/members/{memberId}/reject` – Reject a request.

- **Matches**
  - `GET /matches` – List matches filtered by status and region.
  - `POST /owner/teams/{slug}/matches` – Create a match (owner).
  - `PATCH /owner/matches/{id}` – Update match (scores, status) (owner/admin).

- **Rankings**
  - `GET /rankings` – Query with `scope` (`all`, `city`, `district`), `city`, `district`, `sport`.

- **Admin**
  - `GET/POST/PATCH /admin/regions` – Manage cities and districts.
  - `GET/POST/PATCH /admin/teams` – Override team data.
  - `GET/POST/PATCH /admin/matches` – Override match data and results.

- **Presence (Optional)**
  - `POST /presence/ping` – Update `last_active_at` for current user.

## 11. Future Enhancements

While the current scope focuses on establishing a functional core, several enhancements can be added later:

- **Email verification and notifications** – Confirm emails and notify users about membership approvals and match proposals.
- **Push notifications** – For real‑time alerts via web push or mobile.
- **Granular roles** – Assistant coaches or moderators with limited privileges.
- **Elo‑style ranking** – Replace the simple 3‑1‑0 point system with an Elo or Glicko algorithm to better reflect team performance.
- **Venue and scheduling** – Integrate with sports facilities for real‑time booking and availability management.
- **Gamification** – Badges or achievements for participation to encourage engagement.

---

This README should provide Cruzer with a comprehensive blueprint for implementing the AllSports platform.  The outlined data models, flows and permissions ensure a clear separation of concerns, protect against duplicate data, and provide a foundation for both manual and automated match management.