# Dulify - Digital Solutions Platform

A comprehensive digital platform empowering small schools and local businesses across India with affordable, user-friendly web solutions. Dulify provides customized digital tools designed specifically for institutions with limited technical expertise and resources.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.0+-38B2AC?style=flat&logo=tailwind-css&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 🌟 Features

### For Schools

- **Attendance Management**: Automated attendance tracking with SMS alerts to parents
- **Homework Submission**: Digital homework submission with plagiarism detection
- **Exam Results**: Automatic report card generation
- **Parent Communication**: Direct messaging system for teachers and parents
- **Mobile-First**: Works perfectly on any smartphone—no computer lab required
- **Bilingual Support**: Full Hindi/English interface

### For Local Businesses

- **Inventory Management**: Simple stock tracking with low-stock alerts
- **Customer Loyalty**: Digital punch card and rewards programs
- **WhatsApp Integration**: Automated order notifications
- **Sales Reports**: Daily profit/loss calculations and analytics
- **Customer Database**: Organize and track customer information
- **Mobile POS**: Accept orders directly from smartphones

### Platform Features

- **User Authentication**: Secure registration, login, and password management with email verification
- **User Dashboard**: Personalized dashboard showing purchased services and quick access
- **Service Marketplace**: Browse and purchase digital services
- **Customer Support System**: Built-in ticketing system with message threading
- **Admin Panel**: Complete administrative control over users, services, orders, and support
- **Payment Integration**: Ready for payment gateway integration (Razorpay/PayPal)
- **Email Notifications**: Automated emails using PHPMailer
- **Responsive Design**: Beautiful UI that works on all devices
- **Video Demos**: Integrated Vimeo player for service demonstrations
- **Testimonials**: Customer success stories and ratings
- **Legal Pages**: Privacy policy, terms of service, and cookie policy

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** >= 7.4
- **MySQL** >= 5.7 or MariaDB >= 10.2
- **Composer** (PHP dependency manager)
- **Apache/Nginx** web server
- **PHP Extensions**:
  - PDO
  - PDO_MySQL
  - mbstring
  - openssl
  - curl
  - gd or imagick (for image processing)

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/improperboy/Dulify.git
cd Dulify
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Database Connection

Create your database configuration file:

```bash
cp includes/config.php.example includes/config.php
```

Edit `includes/config.php` with your database credentials:

```php
<?php
// For local development
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'dulify_db');

// Start connection
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
```

### 4. Set Up Database

Create the database and import the schema:

```bash
mysql -u your_username -p -e "CREATE DATABASE dulify_db;"
mysql -u your_username -p dulify_db < database.sql
```

### 5. Configure Email (Optional)

If you want email functionality, create `includes/email_config.php`:

```php
<?php
// Email configuration for PHPMailer
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your_email@gmail.com');
define('SMTP_PASSWORD', 'your_app_password');
define('SMTP_FROM_EMAIL', 'noreply@dulify.com');
define('SMTP_FROM_NAME', 'Dulify');
?>
```

**Note**: For Gmail, you need to:
1. Enable 2-factor authentication
2. Generate an app-specific password
3. Use the app password in the configuration

### 6. Create Admin Account

Run the admin creation script (one-time setup):

```bash
php create_admin.php
```

Or access it via browser: `http://localhost/Dulify/create_admin.php`

After creating the admin account, **delete or secure** the following setup files:
- `create_admin.php`
- `check_admin.php`
- `create_contact_table.php`
- `create_orders_table_now.php`

### 7. Set Permissions

```bash
chmod 755 -R ./
chmod 777 img/uploads/
chmod 777 img/profiles/
chmod 644 includes/config.php
chmod 644 includes/email_config.php
```

### 8. Access the Application

Open your browser and navigate to:

```
http://localhost/Dulify
```

## 📱 Usage

### User Roles

The system supports three user roles:

1. **User**: Regular customers who purchase and access services
   - Dashboard: `/dashboard.php`
   - Browse services, make purchases, access support

2. **Admin**: Full system access with management capabilities
   - Dashboard: `/admin/index.php`
   - Manage users, services, orders, support tickets, and testimonials

### Default Access

- **Admin Panel**: Navigate to `/admin` after admin account creation
- **User Registration**: `/php/register.php`
- **User Login**: `/php/login.php`

### Key Pages

- **Home**: `index.php` - Main landing page with service offerings
- **Services**: `services.php` - Browse available digital solutions
- **About**: `about.php` - Learn about Dulify's mission
- **Contact**: `contact.php` - Get in touch with the team
- **Dashboard**: `dashboard.php` - User's personalized control panel
- **Support**: `support_details.php` - View and manage support tickets

## 🔧 Configuration

### Email Setup (Gmail)

1. Enable 2-factor authentication on your Gmail account
2. Go to Account Settings > Security > App Passwords
3. Generate a new app password for "Mail"
4. Use that password in `includes/email_config.php`

### Video Integration (Vimeo)

The platform uses Vimeo for video demonstrations. Update the video ID in `index.php`:

```html
<iframe src="https://player.vimeo.com/video/YOUR_VIDEO_ID"></iframe>
```

### Payment Gateway (Future Enhancement)

The platform is ready for payment gateway integration. You can integrate:
- Razorpay
- PayPal
- Stripe

## 📂 Project Structure

```
Dulify/
├── admin/                      # Admin panel
│   ├── index.php               # Admin dashboard
│   ├── users.php               # User management
│   ├── services.php            # Service management
│   ├── orders.php              # Order management
│   ├── purchases.php           # Purchase history
│   ├── support.php             # Support tickets
│   ├── messages.php            # Customer messages
│   ├── testimonials.php        # Testimonial management
│   ├── add_service.php         # Add new service
│   └── edit_service.php        # Edit service
├── includes/                   # Core configuration
│   ├── config.php              # Database configuration
│   ├── email_config.php        # Email SMTP settings
│   └── email_helper.php        # Email utility functions
├── php/                        # Backend scripts
│   ├── register.php            # User registration
│   ├── login.php               # User login
│   ├── logout.php              # Logout handler
│   ├── forgot_password.php     # Password recovery
│   ├── reset_password.php      # Password reset
│   ├── purchase.php            # Service purchase handler
│   ├── support.php             # Support ticket creation
│   ├── contact.php             # Contact form handler
│   └── verification_pending.php # Email verification pending
├── css/                        # Stylesheets
├── js/                         # JavaScript files
├── img/                        # Images and assets
│   ├── uploads/                # Uploaded files
│   └── profiles/               # User profile images
├── index.php                   # Homepage
├── dashboard.php               # User dashboard
├── services.php                # Services page
├── about.php                   # About page
├── contact.php                 # Contact page
├── edit_profile.php            # Profile editing
├── change_password.php         # Password change
├── service_access.php          # Purchased service access
├── support_details.php         # Support ticket details
├── verify_email.php            # Email verification handler
├── privacy.php                 # Privacy policy
├── terms.php                   # Terms of service
├── cookies.php                 # Cookie policy
├── composer.json               # PHP dependencies
└── README.md                   # Project documentation
```

## 🛡️ Security

- **Never commit sensitive files** to version control:
  - `includes/config.php`
  - `includes/email_config.php`
  - Setup scripts after initial deployment
- **Password hashing**: All passwords are hashed using `password_hash()` with bcrypt
- **Prepared statements**: SQL injection protection via PDO/MySQLi prepared statements
- **Input validation**: All user inputs are validated and sanitized
- **Session security**: Secure session management with proper timeout
- **Email verification**: Users must verify their email before accessing services
- **HTTPS**: Enable HTTPS in production for encrypted data transmission
- **Regular backups**: Implement automated database backups
- **File upload validation**: Restrict file types and sizes for uploads

## 🐞 Troubleshooting

### Common Issues

**Database Connection Error**
```
ERROR: Could not connect
```
- Verify database credentials in `includes/config.php`
- Ensure MySQL service is running
- Check database name exists
- Test connection with a database client

**Email Not Sending**
- Verify SMTP credentials in `includes/email_config.php`
- Check if port 587 is open and not blocked by firewall
- Ensure "Less secure app access" or App Password is configured for Gmail
- Check PHPMailer is installed: `composer require phpmailer/phpmailer`

**Composer Dependencies Not Found**
```
Fatal error: Class 'PHPMailer\PHPMailer\PHPMailer' not found
```
- Run `composer install` in the project root
- Ensure composer.json is present
- Verify internet connection for downloading packages

**Session Issues / Can't Login**
- Clear browser cookies and cache
- Check PHP session configuration in `php.ini`
- Verify session directory has proper write permissions
- Ensure `session_start()` is called in `config.php`

**Upload Directory Not Writable**
```
Warning: move_uploaded_file(): failed to open stream
```
- Set permissions: `chmod 777 img/uploads/`
- Check if directory exists
- Verify web server has write access

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License.

## 👨‍💻 Author

**improperboy**
- Email: divyanshuomar856@gmail.com
- GitHub: [@improperboy](https://github.com/improperboy)
- Website: [Dulify](https://dulify.com)

## 🙏 Acknowledgments

- [PHPMailer](https://github.com/PHPMailer/PHPMailer) - Email sending functionality
- [Tailwind CSS](https://tailwindcss.com/) - Responsive UI framework
- [Font Awesome](https://fontawesome.com/) - Icon library
- [AOS](https://michalsnik.github.io/aos/) - Scroll animations
- [Vimeo](https://vimeo.com/) - Video hosting and player

## 🔄 Roadmap & Future Enhancements

### Planned Features

- [ ] Payment gateway integration (Razorpay/PayPal)
- [ ] SMS notifications via Twilio/MSG91
- [ ] WhatsApp Business API integration
- [ ] Mobile application (Android/iOS)
- [ ] Advanced analytics dashboard
- [ ] Multi-language support (add regional languages)
- [ ] Subscription management
- [ ] Invoice generation (PDF)
- [ ] Service usage analytics
- [ ] API for third-party integrations
- [ ] Progressive Web App (PWA) capabilities
- [ ] Dark mode theme
- [ ] Social media integration
- [ ] Live chat support
- [ ] Bulk email campaigns

### In Progress

- Email notification system
- Enhanced admin reporting
- Service rating and reviews
- Referral program

## 💡 Use Cases

### For Schools
- Small private schools (K-12)
- Coaching centers
- Tutorial classes
- Preschools and daycare centers
- Skill development institutes

### For Businesses
- Kirana stores (grocery shops)
- Boutiques and clothing stores
- Small restaurants and cafes
- Medical shops/pharmacies
- Hardware stores
- Electronics shops
- Book stores
- Gift shops

## 📞 Support

For support and queries:
- Email: divyanshuomar856@gmail.com
- Create an issue in the repository
- Visit our support page: `support_details.php`

## 🌐 Live Demo

Visit the live platform: [Dulify Website](#)

---

**⭐ If you find this project helpful, please consider giving it a star!**

**Made with ❤️ for small schools and businesses across India**
