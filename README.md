# Birthday Mass Notification System

A Laravel + Filament application that automatically notifies multiple contacts when someone's birthday occurs.

## Purpose

This system tracks people's birthdays and sends mass notifications to their associated contacts on their special day.

**Example:**
- When John's birthday arrives → System notifies his 5 contacts via SMS/Email
- When Amy's birthday arrives → System notifies her 10 contacts via SMS/Email

Perfect for organizations, communities, or groups who want to ensure everyone gets notified about birthdays.

## Features

- ✅ **Person Management** - Track people with their birth dates
- ✅ **Notification Contacts** - Each person has a list of contacts to notify on their birthday
- ✅ **Automated Notifications** - Daily check for birthdays and send mass notifications
- ✅ **Multiple Methods** - Support for SMS, Email, or Both
- ✅ **Dashboard** - View upcoming birthdays (next 30 days)
- ✅ **Notification Logs** - Track all sent notifications with status
- ✅ **Filament Admin** - Beautiful admin interface for managing everything

## Tech Stack

- **Laravel 11** - PHP framework
- **Filament 3.2** - Admin panel
- **SQLite** - Database (default, can use MySQL/PostgreSQL)
- **Tailwind CSS** - Styling
- **Alpine.js** - Client-side interactivity
- **Vite** - Build tool

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- SQLite (or MySQL/PostgreSQL)

### Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/WEshalom/whosbday-claude.git
   cd whosbday-claude
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies:**
   ```bash
   npm install
   ```

4. **Create environment file:**
   ```bash
   cp .env.example .env
   ```

5. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

6. **Create database:**
   ```bash
   touch database/database.sqlite
   ```

   Or configure MySQL/PostgreSQL in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=whosbday
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Run migrations:**
   ```bash
   php artisan migrate
   ```

8. **Build assets:**
   ```bash
   npm run build
   ```

9. **Create admin user:**
   ```bash
   php artisan make:filament-user
   ```
   Follow the prompts to create your admin account.

10. **Start the development server:**
    ```bash
    php artisan serve
    ```

11. **Access the application:**
    - Open your browser to `http://localhost:8000`
    - You'll be redirected to `/admin`
    - Login with the credentials you created

## Usage

### Adding a Person

1. Navigate to **People** in the sidebar
2. Click **New Person**
3. Fill in:
   - First Name
   - Last Name
   - Birth Date
   - Notes (optional)
4. Click **Create**

### Adding Notification Contacts

1. Open a person's detail page
2. Scroll to **Notification Contacts** section
3. Click **New Notification Contact**
4. Fill in:
   - Contact Name (optional)
   - Phone Number (for SMS)
   - Email Address (for email)
   - Method (SMS, Email, or Both)
   - Enabled toggle
5. Click **Create**

You can add as many notification contacts as needed per person!

### Running Birthday Notifications

**Manually:**
```bash
php artisan birthdays:notify
```

This command:
- Checks for birthdays today
- Finds all enabled notification contacts for each birthday person
- Sends notifications (currently stubbed, logs only)
- Records all attempts in notification logs

**Automatically (Scheduled):**

The command is scheduled to run daily at 8:00 AM. Start the scheduler:

```bash
php artisan schedule:work
```

For production, add this to your cron:
```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Viewing Notification Logs

1. Navigate to **Notification Logs** in the sidebar
2. View all sent notifications with:
   - Birthday person name
   - Contact notified
   - Method used
   - Status (success/failed)
   - Timestamp
   - Error messages (if any)

## Database Schema

### `people` table
- id
- first_name
- last_name
- birth_date
- notes
- timestamps

### `notification_contacts` table
- id
- person_id (FK)
- contact_name
- phone
- email
- method (sms/email/both)
- enabled
- timestamps

### `notification_logs` table
- id
- person_id (FK)
- notification_contact_id (FK)
- sent_at
- status (success/failed)
- error_message
- timestamps

## Implementing Actual Notifications

Currently, notifications are **stubbed** (logged only). To implement real SMS/Email sending:

### For SMS (Twilio example):

1. Install Twilio SDK:
   ```bash
   composer require twilio/sdk
   ```

2. Add to `.env`:
   ```env
   TWILIO_SID=your_account_sid
   TWILIO_AUTH_TOKEN=your_auth_token
   TWILIO_PHONE_NUMBER=your_twilio_phone
   ```

3. Update `BirthdayNotificationCommand.php` sendNotification method:
   ```php
   case 'sms':
       $twilio = new \Twilio\Rest\Client(
           config('services.twilio.sid'),
           config('services.twilio.token')
       );
       $twilio->messages->create($contact->phone, [
           'from' => config('services.twilio.phone'),
           'body' => $message
       ]);
       break;
   ```

### For Email:

Use Laravel's built-in Mail system:

```php
case 'email':
    Mail::to($contact->email)->send(
        new BirthdayNotification($person, $message)
    );
    break;
```

## Development

### Run development server:
```bash
php artisan serve
npm run dev
```

### Run tests:
```bash
php artisan test
```

### Code formatting:
```bash
./vendor/bin/pint
```

## Production Deployment

1. Set environment to production in `.env`:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. Build assets for production:
   ```bash
   npm run build
   ```

3. Optimize Laravel:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. Set up cron for scheduler (see above)

5. Configure queue worker for better performance:
   ```bash
   php artisan queue:work --daemon
   ```

## Troubleshooting

**Can't login to Filament:**
- Make sure you created a user with `php artisan make:filament-user`
- Check that your database is properly configured

**Notifications not sending:**
- Run `php artisan birthdays:notify` manually to see output
- Check notification logs in the admin panel
- Verify birth dates are in the correct format

**Assets not loading:**
- Run `npm run build`
- Check that `public/build` directory exists

## License

MIT License

## Credits

Built with [Laravel](https://laravel.com) and [Filament](https://filamentphp.com)
