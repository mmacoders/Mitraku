# SI-Huyula (Elektronik Perjanjian Kerja Sama)

A Laravel + Vue.js application for managing partnership agreements (PKS) with role-based access control for Mitra and Admin users.

## Features

- **User Authentication**: Registration and login for Mitra users, Admin users created via seeder
- **Role-based Dashboard**: Separate dashboards for Mitra and Admin users
- **PKS Management**: 
  - Mitra can submit new PKS requests with documents
  - Admin can review, approve, reject, or request revisions
  - Status tracking (Proses, Revisi, Ditolak, Disetujui)
  - Timeline/history of status changes
- **Document Management**: Upload and storage of PKS documents
- **Meeting Management**: Schedule meetings related to PKS submissions
- **Notification System**: Real-time notifications for all workflow events
- **Digital Signatures**: Capture and store digital signatures for approved documents
- **Document Tracking**: Comprehensive timeline visualization of document history
- **Responsive UI**: Mobile-first design with TailwindCSS
- **Dark Mode**: Toggle between light and dark themes

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue.js 3 + Vite + TailwindCSS
- **Database**: MySQL
- **Authentication**: Laravel Breeze

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd mitraku
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Create a MySQL database named `mitraku`

5. Configure your `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=e_pks
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. Generate application key:
   ```bash
   php artisan key:generate
   ```

7. Run database migrations:
   ```bash
   php artisan migrate
   ```

8. Seed the database (creates admin user and sample data):
   ```bash
   php artisan db:seed
   ```

9. Build frontend assets:
   ```bash
   npm run build
   ```

10. Start the development server:
    ```bash
    php artisan serve
    ```

11. In another terminal, start the Vite development server:
    ```bash
    npm run dev
    ```

## Default Users

- **Admin**: 
  - Email: admin@epks.local
  - Password: password

- **Mitra**: 
  - Email: test@example.com
  - Password: password

## Development

To run tests:
```bash
php artisan test
```

To run PHP CS Fixer:
```bash
./vendor/bin/pint
```

## Recent Enhancements

### PKS Workflow Implementation
- Enhanced user registration with role selection (Mitra/Admin)
- Comprehensive notification system for all workflow events
- Digital signature capture and storage capability
- Document timeline visualization for complete history tracking
- Meeting scheduling integrated with PKS submissions

### Dashboard Alignment
- Admin dashboard redesigned to match Mitra dashboard structure
- Added welcome banner with consistent messaging
- Implemented KPI cards for key metrics
- Added chart section with donut and line charts
- Included filter section with search, status, and date filtering
- Recent activities section for quick overview

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).