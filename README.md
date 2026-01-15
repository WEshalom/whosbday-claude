# Who's Birthday 🎂

A beautiful and intuitive birthday tracking web application built with Laravel and Filament. Never forget an important birthday again!

## Features

- **Contact Management**: Store and organize your contacts with detailed information
- **Birthday Tracking**: Keep track of birthdays with automatic age calculation
- **Smart Notifications**: Get email reminders before upcoming birthdays
- **Calendar View**: Visualize all birthdays in an interactive calendar
- **Gift Ideas**: Store gift ideas for each contact
- **User Authentication**: Secure multi-user support with personal birthday lists
- **Beautiful Dashboard**: See upcoming birthdays at a glance with statistics
- **Filament Admin Panel**: Modern, responsive admin interface

## Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Admin Panel**: Filament 3.2
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Build Tool**: Vite
- **Database**: MySQL/PostgreSQL
- **Calendar**: FullCalendar.js

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL or PostgreSQL database

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd whosbday-claude
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   ```

   Edit `.env` and configure your database:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=whosbday
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Create storage symlink**
   ```bash
   php artisan storage:link
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   # Or for development with hot reload:
   npm run dev
   ```

9. **Start the application**
   ```bash
   php artisan serve
   ```

10. **Access the application**
    - Open your browser and navigate to: `http://localhost:8000`
    - You'll be redirected to the admin panel at: `http://localhost:8000/admin`
    - Register a new account to get started

## Usage

### Managing Contacts

1. Navigate to **Contacts** in the admin panel
2. Click **New Contact** to add a contact
3. Fill in their information including:
   - Name (first name, last name, nickname)
   - Contact details (email, phone)
   - Relationship type
   - Birthday information
   - Gift ideas
4. Save to add the contact

### Birthday Notifications

Birthday notifications are sent automatically via email. To enable:

1. Configure your mail settings in `.env`:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=your_smtp_host
   MAIL_PORT=587
   MAIL_USERNAME=your_email
   MAIL_PASSWORD=your_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@yourapp.com"
   ```

2. Set up the scheduler (add to crontab):
   ```bash
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

3. Notifications will be sent daily at 8:00 AM for upcoming birthdays

### Manual Notification Testing

To test the notification system manually:
```bash
php artisan birthdays:notify
```

## Database Schema

### Contacts Table
- Personal information (name, email, phone)
- Relationship type
- Notes and avatar
- Soft deletes support

### Birthdays Table
- Date and year (year optional)
- Notification preferences
- Gift ideas
- Linked to contacts

### Users Table
- Standard Laravel authentication
- Multi-user support

## Development

### Running in Development Mode

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite dev server
npm run dev
```

### Queue Workers

For better performance in production, run queue workers:
```bash
php artisan queue:work
```

### Running Tests

```bash
php artisan test
```

## Project Structure

```
app/
├── Console/Commands/       # Artisan commands (birthday notifications)
├── Filament/
│   ├── Pages/             # Custom Filament pages
│   ├── Resources/         # CRUD resources for contacts
│   └── Widgets/           # Dashboard widgets
├── Models/                # Eloquent models
├── Notifications/         # Notification classes
└── Providers/             # Service providers

database/
├── migrations/            # Database migrations
├── factories/             # Model factories
└── seeders/              # Database seeders

resources/
├── css/                  # Stylesheets
├── js/                   # JavaScript files
└── views/                # Blade templates
```

## Configuration

### Birthday Notification Settings

Adjust the default notification timing in `.env`:
```
BIRTHDAY_NOTIFICATION_DAYS_BEFORE=7
```

### Customizing Colors

Edit the color scheme in `tailwind.config.js` and `app/Providers/Filament/AdminPanelProvider.php`

## Troubleshooting

### Migrations fail
- Ensure database credentials are correct in `.env`
- Check that the database exists
- Run `php artisan config:clear` and try again

### Assets not loading
- Run `npm run build`
- Clear browser cache
- Check file permissions on `public` directory

### Notifications not sending
- Verify mail configuration in `.env`
- Check queue is running: `php artisan queue:work`
- Test with: `php artisan birthdays:notify`

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the MIT license.

## Support

For issues and questions, please open an issue on GitHub.

---

Made with ❤️ using Laravel and Filament
