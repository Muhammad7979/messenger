Our Goal

Build a production-ready messaging platform using

Laravel 
React
WebSockets
MySQL
Redis
Queues
Object Storage (S3/MinIO)

Features

Private Chat
Group Chat
Channels
Threads
Read Receipts
Typing Indicator
Presence
Mentions
Reactions
Attachments
Voice Notes
Calls
Notifications
Multi-device login
Search
Permissions


Final Database

Around 32 tables

Authentication
--------------
users
devices
sessions
user_tokens

Profile
-------
profiles
contacts
blocked_users

Conversation
------------
conversations
conversation_members
conversation_roles
conversation_invitations

Messages
--------
messages
message_versions
message_reads
message_deliveries
message_reactions
message_mentions
message_bookmarks
message_pins
message_deletes

Attachments
-----------
attachments
attachment_chunks

Calls
-----
calls
call_participants

Presence
--------
user_presence
typing_status

Notifications
-------------
notifications
notification_reads

Groups
------
groups
group_permissions

Channels
--------
channels
channel_members

Workspace (Slack Style)
-----------------------
workspaces
workspace_members


Relationship Diagram

User
 │
 ├── Profile
 │
 ├── Devices
 │
 ├── Contacts
 │
 ├── Sessions
 │
 ├── Notifications
 │
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


Phase 1 (Core Chat)

Only 6 tables

users

conversations

conversation_members

messages

message_reads

attachments


This is enough to build

- WhatsApp
- Messenger
- Telegram
- Signal

Phase 2

Add

- message_reactions
- message_deliveries
- typing_status
- user_presence

Now you have

✓ Blue ticks
✓ Online
✓ Typing...
✓ Emoji reactions

Phase 3

- groups
- group_roles
- conversation_invitations

Now

✓ Groups
✓ Admin
✓ Moderator
✓ Invite Links

Phase 4

channels
threads
mentions
bookmarks
pins

Slack features.

Phase 5

calls
call_participants
devices
sessions

Now

Multiple devices
Voice Calls
Video Calls

Production ER Diagram
Users
-----
id PK

email UNIQUE

password

created_at



Profiles
--------
id PK

user_id FK -> users.id

avatar

bio

status



Conversations
-------------
id PK

type
private
group
channel

created_by FK users.id

name

last_message_id

created_at



ConversationMembers
-------------------
id PK

conversation_id FK

user_id FK

role

joined_at

left_at

last_read_message_id

is_muted

is_archived

UNIQUE(conversation_id,user_id)



Messages
--------
id PK

conversation_id FK

sender_id FK

parent_message_id FK

reply_to_id FK

body

message_type

status

sent_at

edited_at

deleted_at

INDEX(conversation_id,sent_at)

INDEX(sender_id)

FULLTEXT(body)

Attachments
Attachments

id

message_id

storage

path

mime_type

size

width

height

duration

checksum

INDEX(message_id)

Reads
MessageReads

message_id FK

user_id FK

read_at

PRIMARY(message_id,user_id)

Composite primary keys avoid duplicate read receipts.


Reactions
MessageReactions

message_id FK

user_id FK

emoji

created_at

UNIQUE(message_id,user_id,emoji)
Presence
UserPresence

user_id PK

status

last_seen

socket_id

device_id
Typing
TypingStatus

conversation_id

user_id

started_at

expires_at

UNIQUE(conversation_id,user_id)
Devices
Devices

id

user_id

device_uuid

platform

device_name

push_token

last_login
Sessions
Sessions

id

device_id

token_hash

ip

user_agent

expires_at
Messages Table (Production)
Messages

id BIGINT

uuid

conversation_id

sender_id

reply_to_id

forwarded_from_id

body

message_type

metadata JSON

edited_at

deleted_at

sent_at

created_at

updated_at
Indexes
users

email UNIQUE



conversation_members

(conversation_id,user_id)

(user_id)



messages

(conversation_id,id)

(conversation_id,sent_at)

(sender_id)

(reply_to_id)



attachments

(message_id)



message_reads

(message_id,user_id)



message_reactions

(message_id)



devices

(user_id)



sessions

(token_hash)