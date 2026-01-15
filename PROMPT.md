# Birthday Mass Notification System - Build Instructions

## Purpose
Build a system that automatically notifies multiple people when someone's birthday occurs.

**Example:**
- When John's birthday happens → notify contacts: 123-456-7890, 234-567-8901, etc.
- When Amy's birthday happens → notify contacts: 345-678-9012, 456-789-0123, etc.

## Tech Stack
- **Laravel 11** (PHP framework)
- **Filament 3.2** (admin panel for management)
- **Blade** templates
- **Tailwind CSS** (styling)
- **Vite** (build tool)
- **MySQL/PostgreSQL** (database)
- **Alpine.js** (client-side interactivity)

## Core Features

### 1. Person Management
Track people whose birthdays will trigger notifications.

**Fields:**
- First name
- Last name
- Birth date (MM-DD-YYYY)
- Notes

### 2. Notification Lists
Each person has a list of phone numbers/contacts to notify on their birthday.

**Fields per notification contact:**
- Phone number/email
- Contact name (optional)
- Notification method (SMS/Email/Both)
- Enabled/disabled toggle

### 3. Birthday Monitoring
System checks daily for birthdays and sends mass notifications.

**Behavior:**
- Run daily at 8 AM
- Find people with birthdays today
- For each person, send notification to ALL their notification contacts
- Log all sent notifications
- Handle failures gracefully

### 4. Filament Admin Interface
- Dashboard showing upcoming birthdays
- CRUD for people
- Manage notification lists per person
- View notification logs
- Manual trigger for testing

## Database Schema

### `people` table
```sql
id, first_name, last_name, birth_date, notes, created_at, updated_at
```

### `notification_contacts` table
```sql
id, person_id (FK), contact_name, phone, email, method (enum: sms/email/both), enabled, created_at, updated_at
```

### `notification_logs` table
```sql
id, person_id (FK), notification_contact_id (FK), sent_at, status, error_message, created_at
```

## Key Functionality

### Birthday Check Command
```bash
php artisan birthdays:notify
```

**Logic:**
1. Get today's date (MM-DD)
2. Find all people with matching birth_date (ignore year)
3. For each person:
   - Get all enabled notification_contacts
   - Send notification to each contact
   - Log success/failure
   - Calculate age from birth year

### Notification Message Template
```
🎉 Happy Birthday to [First Name] [Last Name]!
[He/She] turns [Age] today.
```

## Deliverables

1. **Migrations:**
   - 2024_01_01_000001_create_people_table.php
   - 2024_01_01_000002_create_notification_contacts_table.php
   - 2024_01_01_000003_create_notification_logs_table.php

2. **Models:**
   - Person.php (with relationships to NotificationContact and NotificationLog)
   - NotificationContact.php
   - NotificationLog.php

3. **Filament Resources:**
   - PersonResource.php (with notification contacts relation manager)
   - NotificationLogResource.php (read-only)

4. **Console Command:**
   - BirthdayNotificationCommand.php

5. **Dashboard Widget:**
   - UpcomingBirthdaysWidget.php (shows next 30 days)

6. **Configuration:**
   - composer.json, package.json, vite.config.js, tailwind.config.js
   - .env.example

7. **Documentation:**
   - README.md with setup instructions

## Requirements

- Multi-tenancy not required (single admin instance)
- Simple authentication (Filament built-in)
- Notification sending can be stubbed (log only for now, real SMS/email later)
- Follow Laravel best practices
- Use Filament conventions
- Clean, documented code

## Build This Now

Create all files, migrations, models, Filament resources, commands, and documentation.
Make it production-ready and immediately runnable after `composer install && npm install && php artisan migrate`.
