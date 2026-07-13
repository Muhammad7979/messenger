# Database Architecture Review

Laravel Messenger — schema review, improvements applied, factories/seeders, and scaling notes.

> Framework note: this project uses **Laravel 13** (`composer.json`). Practices below match current Laravel conventions.

---

## Current Architecture

### Domain overview

```
User
 ├── ConversationMember ──► Conversation ──► Message
 │                              │               ├── Attachment
 │                              │               ├── MessageRead
 │                              │               ├── MessageDelivery
 │                              │               ├── MessageReaction
 │                              │               └── MessagePin
 ├── Device ──► DeviceSession
 ├── UserPresence
 ├── TypingStatus
 └── BlockedUser
```

### Tables

#### users
- **Purpose:** Authentication and identity.
- **PK:** `id`
- **Unique:** `email`
- **Related:** memberships, devices, presence, blocks, sent messages.
- **Notes:** Includes Fortify 2FA columns and passkeys (separate table).

#### conversations
- **Purpose:** Private, group, or channel chat containers.
- **PK:** `id`; public **UUID** `uuid` (unique).
- **FK:** `created_by` → users (**restrict** on delete); `last_message_id` → messages (**nullOnDelete**, deferred migration).
- **Indexes:** `type`, `created_by`, `last_message_id`, unique `uuid`.
- **Type storage:** `unsignedTinyInteger` via `ConversationType` enum (1=private, 2=group, 3=channel).

#### conversation_members
- **Purpose:** Membership, roles, mute/archive, read cursor.
- **PK:** `id`
- **FK:** `conversation_id`, `user_id` (cascade); `last_read_message_id` → messages (nullOnDelete, deferred).
- **Unique:** `(conversation_id, user_id)`
- **Indexes:** `role`, `joined_at`, `(user_id, is_archived)`, `(conversation_id, last_read_message_id)`
- **Roles:** string-backed `MemberRole` enum (`owner`, `admin`, `moderator`, `member`).

#### messages
- **Purpose:** Chat content with reply/forward/thread pointers.
- **PK:** `id`; public **UUID** `uuid`
- **FK:** conversation, sender, optional self-FKs for parent/reply/forward.
- **Indexes:** `(conversation_id, id)`, `(conversation_id, sent_at)`, sender/reply/parent/forward, `message_type`
- **FULLTEXT:** `body` on MySQL/MariaDB/PostgreSQL (skipped on SQLite).
- **Soft deletes:** `deleted_at`
- **Types:** `MessageType` int enum (text, image, video, audio, file, voice_note, system, gif).

#### attachments
- **Purpose:** Media/files linked to a message.
- **FK:** `message_id` cascade.
- **Indexes:** `mime_type`, `storage`
- **Storage:** `AttachmentStorage` enum (`local`, `s3`, `minio`).

#### message_reads
- **Purpose:** Per-user read receipts (blue ticks when paired with deliveries).
- **Composite PK:** `(message_id, user_id)`
- **Indexes:** `user_id`, `read_at`

#### message_deliveries
- **Purpose:** Delivered status (message reached device/client).
- **Composite PK:** `(message_id, user_id)`
- **Indexes:** `user_id`, `delivered_at`

#### message_reactions
- **Purpose:** Emoji reactions.
- **Unique:** `(message_id, user_id, emoji)`
- **Timestamps:** `created_at` only.

#### message_pins
- **Purpose:** Pinned messages within a conversation.
- **Unique:** `(conversation_id, message_id)`
- **FK:** conversation, message, `pinned_by` → users.

#### blocked_users
- **Purpose:** User-to-user blocks.
- **Unique:** `(blocker_id, blocked_id)`
- **FK:** both → users cascade.

#### devices
- **Purpose:** Multi-device registration and push metadata.
- **Unique:** `device_uuid`
- **Platform:** DB enum + `DevicePlatform` PHP enum.

#### device_sessions
- **Purpose:** Hashed auth tokens for devices (API/mobile sessions).
- **Table name:** `device_sessions` (does **not** collide with Laravel HTTP `sessions`).
- **Unique:** `token_hash`

#### user_presences
- **Purpose:** Online/offline/away/busy presence (one row per user).
- **Unique:** `user_id`
- **Optional FK:** `device_id`

#### typing_statuses
- **Purpose:** Ephemeral typing indicators.
- **Unique:** `(conversation_id, user_id)`
- **Index:** `expires_at` for cleanup jobs.

#### Framework tables
- Laravel `sessions`, `cache`, `jobs`, `passkeys`, etc. remain unchanged.

---

## Issues Found

| Severity | Issue |
|----------|--------|
| Critical | Device auth table named `sessions` collided with Laravel HTTP sessions |
| Critical | FKs to `messages` created before `messages` existed |
| Critical | FKs to `devices` from presence/typing before `devices` existed |
| High | Singular table names `user_presence` / `typing_status` vs Eloquent plural defaults |
| High | `Conversation` used `HasUuids` without `uniqueIds(['uuid'])` (wrong PK behavior) |
| High | String type constants vs integer DB columns (broken scopes/helpers) |
| Medium | `Message` missing `HasFactory` |
| Medium | `created_by` cascade deleted conversations when creator deleted |
| Medium | Empty factories/seeders |
| Medium | Missing blocked users, deliveries, pins |
| Low | Duplicate indexes on some `foreignId` columns |

---

## Improvements Applied

1. Renamed device auth table to **`device_sessions`**; updated `DeviceSession` model.
2. Deferred `last_message_id` / `last_read_message_id` FKs to migration `2026_07_12_063200_...`.
3. Reordered migrations: **devices → user_presences → typing_statuses → device_sessions**.
4. Pluralized **`user_presences`** and **`typing_statuses`**.
5. Fixed `HasUuids` on `Conversation` and `Message` (`uniqueIds(['uuid'])`).
6. Introduced backed enums under `app/Enums/` and cast them on models.
7. `created_by` now **`restrictOnDelete()`**.
8. Added tables: **`blocked_users`**, **`message_deliveries`**, **`message_pins`**.
9. Composite indexes for inbox/archive and unread cursor queries.
10. Implemented factories and dependency-ordered seeders with realistic demo volume.
11. FULLTEXT on messages only when the driver supports it.

---

## Remaining Recommendations

### Document only (not implemented — out of scope 1B)

- Laravel `notifications` table / custom notification reads
- Draft messages / user settings tables
- Profiles, contacts, mentions, bookmarks, message versions
- Calls / call_participants
- Soft deletes on conversations, members, attachments
- Unread counter cache table or Redis counters
- Check constraint `blocker_id <> blocked_id` (app-level enforced in seeder/factory; DB check optional per driver)
- Dedicated MinIO disk in `config/filesystems.php`

### Manual review before production

- Confirm **`restrictOnDelete`** on `created_by` matches product policy (vs soft-delete users).
- Decide whether **dual read tracking** (`message_reads` + `last_read_message_id`) stays long-term.
- Production DB should be **MySQL 8+** or **PostgreSQL** for FULLTEXT and scale (SQLite is fine for local).

---

## Index Recommendations

Already present / added:

| Query pattern | Index |
|---------------|-------|
| Conversation timeline | `(conversation_id, sent_at)`, `(conversation_id, id)` |
| Inbox / archived | `(user_id, is_archived)` on members |
| Unread estimate | `(conversation_id, last_read_message_id)` |
| Presence cleanup | `expires_at` on typing; `last_seen` on presence |
| Device session auth | unique `token_hash`; `expires_at` |

Future (at ~10M messages):

- Partition or shard `messages` by `conversation_id` or time
- Covering index for list preview: `(conversation_id, sent_at DESC)` including `sender_id`, `message_type` (MySQL covering / PG INCLUDE)
- Separate hot/cold message storage

---

## Performance Suggestions

Assumptions: 100k users, 10M messages, ~1000 concurrent.

1. Always paginate messages with **keyset pagination** on `(sent_at, id)` — avoid `OFFSET` deep pages.
2. Conversation list: join members + `last_message_id` (denormalized) — do not `MAX(messages.id)` per row.
3. Unread counts: prefer member cursor + optional Redis counter; avoid counting `message_reads` on every list load.
4. Reactions: aggregate via cache or `GROUP BY emoji` for open conversation only.
5. Attachments: serve via CDN/object storage; store only metadata in DB.
6. Presence/typing: keep DB as source of truth for reconnect; use **Redis + Reverb** for hot path.
7. Search: FULLTEXT for MVP; move to Meilisearch/Typesense/OpenSearch at scale.

---

## Future Scaling Ideas

- Message archive tables / cold storage for old conversations
- Outbox pattern for realtime fan-out
- Rate limits and idempotency keys for send API
- Horizontal read replicas for timeline queries
- Workspace/tenant isolation if multi-org product is required (`doc/doc.md` Phase 5+)

---

## Feature Support Matrix

| Feature | Status |
|---------|--------|
| Private / group chat | Supported |
| Members, roles, owner/admins | Supported |
| Mute / archive | Supported |
| Edit / soft delete | Supported |
| Replies / forward / threads | Supported |
| Read receipts | Supported (`message_reads` + member cursor) |
| Delivered status | Supported (`message_deliveries`) |
| Reactions | Supported |
| Attachments / voice / gif types | Supported |
| Pins | Supported (`message_pins`) |
| Blocked users | Supported |
| Typing / presence / devices | Supported |
| Drafts / notification DB / settings | Not yet (documented) |

---

## Seeder Structure

Order in `DatabaseSeeder`:

1. `UserSeeder` — 200 users + `test@example.com`
2. `DeviceSeeder`
3. `UserPresenceSeeder`
4. `ConversationSeeder` — 100 private + 40 group
5. `ConversationMemberSeeder` — private=2; groups=3–25
6. `MessageSeeder` — ~12k–18k chronological messages
7. `AttachmentSeeder`
8. `MessageDeliverySeeder`
9. `MessageReadSeeder`
10. `MessageReactionSeeder`
11. `PinnedMessageSeeder`
12. `BlockedUserSeeder`
13. `TypingStatusSeeder`
14. `DeviceSessionSeeder`

Run:

```bash
php artisan migrate:fresh --seed
```

Requires **PHP >= 8.4.1** (Laravel 13 lockfile).

---

## Factory Structure

Factories exist for every messenger model, including:

- `ConversationFactory` — `private()`, `group()`, `channel()` states
- `MessageFactory` — type states (`text`, `image`, `voiceNote`, …)
- `AttachmentFactory` — `forType(MessageType)`
- `DeviceFactory`, `DeviceSessionFactory`, `UserPresenceFactory`, `TypingStatusFactory`
- `MessageReadFactory`, `MessageDeliveryFactory`, `MessageReactionFactory`
- `MessagePinFactory`, `BlockedUserFactory`

---

## Suggested Next Steps

1. Add application services for send/read/pin/block (keep models thin).
2. Policies for conversation membership and admin actions.
3. Observers to maintain `last_message_id` and optional unread counters.
4. Feature tests for migrate + seed smoke and relationship integrity.
5. Add Laravel notifications migration when push/in-app notifications are built.
6. Wire Reverb events for message/typing/presence.
