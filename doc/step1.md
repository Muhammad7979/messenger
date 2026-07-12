# Production-Ready Messaging Platform

> **Stack:** Laravel • React • WebSockets • MySQL • Redis • Queues •
> S3/MinIO

## Table of Contents

1.  Overview
2.  Technology Stack
3.  Features
4.  Development Roadmap
5.  Database (32 Tables)
6.  Core Relationships
7.  Production Indexes
8.  Scaling Notes

# 1. Overview

Build a production-ready messaging platform supporting private chat,
groups, channels, threads, reactions, read receipts, presence, typing
indicators, attachments, calls, notifications, search, permissions, and
multi-device login.

# 2. Technology Stack

  Layer            Technology
  ---------------- -------------------
  Backend          Laravel
  Frontend         React
  Database         MySQL
  Cache            Redis
  Queue            Redis Queue
  Realtime         WebSockets
  Object Storage   Amazon S3 / MinIO

# 3. Features

-   Private Chat
-   Group Chat
-   Channels
-   Threads
-   Read Receipts
-   Typing Indicator
-   Presence
-   Mentions
-   Reactions
-   Attachments
-   Voice Notes
-   Calls
-   NotificationsREADME
-   Multi-device Login
-   Search
-   Permissions

# 4. Development Roadmap

## Phase 1 (Core)

Tables: - users - conversations - conversation_members - messages -
message_reads - attachments

Supports: - WhatsApp - Messenger - Telegram - Signal

## Phase 2

Add: - message_reactions - message_deliveries - typing_status -
user_presence

Features: - Blue ticks - Online status - Typing - Emoji reactions

## Phase 3

Add: - groups - group_permissions - conversation_invitations

Features: - Groups - Admins - Moderators - Invite Links

## Phase 4

Add: - channels - channel_members - message_mentions -
message_bookmarks - message_pins

Slack-style collaboration.

## Phase 5

Add: - calls - call_participants - devices - sessions - user_tokens

Features: - Multi-device login - Voice calls - Video calls

# 5. Database (32 Tables)

## Authentication

  Table         Purpose
  ------------- --------------------
  users         User accounts
  devices       Registered devices
  sessions      Login sessions
  user_tokens   API/auth tokens

## Profile

profiles, contacts, blocked_users

## Conversation

conversations, conversation_members, conversation_roles,
conversation_invitations

## Messages

messages, message_versions, message_reads, message_deliveries,
message_reactions, message_mentions, message_bookmarks, message_pins,
message_deletes

## Attachments

attachments, attachment_chunks

## Calls

calls, call_participants

## Presence

user_presence, typing_status

## Notifications

notifications, notification_reads

## Groups

groups, group_permissions

## Channels

channels, channel_members

## Workspace

workspaces, workspace_members

# Core Table Schemas

## users

-   id (PK)
-   name
-   email (UNIQUE)
-   password
-   created_at
-   updated_at

## conversations

-   id (PK)
-   uuid
-   type (private/group/channel)
-   created_by (FK users.id)
-   name
-   last_message_id
-   created_at Indexes:
-   (type)
-   (created_by)

## conversation_members

-   id (PK)
-   conversation_id (FK)
-   user_id (FK)
-   role
-   joined_at
-   left_at
-   last_read_message_id
-   is_muted
-   is_archived Constraints:
-   UNIQUE(conversation_id,user_id) Indexes:
-   (user_id)

## messages

-   id BIGINT PK
-   uuid
-   conversation_id (FK)
-   sender_id (FK)
-   parent_message_id (FK)
-   reply_to_id (FK)
-   forwarded_from_id
-   body
-   message_type
-   metadata JSON
-   edited_at
-   deleted_at
-   sent_at
-   created_at
-   updated_at

Indexes: - (conversation_id,id) - (conversation_id,sent_at) -
(sender_id) - (reply_to_id) - FULLTEXT(body)

## attachments

-   id
-   message_id (FK)
-   storage
-   path
-   mime_type
-   size
-   width
-   height
-   duration
-   checksum Index:
-   (message_id)

## message_reads

Composite PK: - (message_id,user_id) Columns: - message_id (FK) -
user_id (FK) - read_at

## message_reactions

-   message_id
-   user_id
-   emoji
-   created_at Constraint:
-   UNIQUE(message_id,user_id,emoji)

## user_presence

-   user_id (PK)
-   status
-   last_seen
-   socket_id
-   device_id

## typing_status

-   conversation_id
-   user_id
-   started_at
-   expires_at Constraint:
-   UNIQUE(conversation_id,user_id)

## devices

-   id
-   user_id
-   device_uuid
-   platform
-   device_name
-   push_token
-   last_login

## sessions

-   id
-   device_id
-   token_hash
-   ip
-   user_agent
-   expires_at Index:
-   (token_hash)

# Relationship Diagram

``` text
User
 │
 ├── Profile
 ├── Devices
 ├── Contacts
 ├── Sessions
 ├── Notifications
 └── ConversationMember
             │
             ▼
      Conversation
             │
      ┌──────┴────────┐
      │               │
 Members          Messages
                      │
        ┌─────────────┼────────────┐
        │             │            │
   Reactions      Reads      Attachments
        │             │            │
      Mentions    Deliveries   Pins
```

# Production Notes

-   BIGINT keys for high scale.
-   UUID for public identifiers.
-   Foreign keys enforce integrity.
-   Composite keys prevent duplicates.
-   Redis for cache, presence, queues.
-   WebSockets for realtime events.
-   Store media in S3/MinIO.
-   Queue notifications and media processing.
-   Full-text search on message body.
-   Archive instead of hard delete where possible.
-   Add partitioning/sharding as data grows.