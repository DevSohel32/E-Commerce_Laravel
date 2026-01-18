# 🛍️ E-Commerce Platform

A modern, feature-rich e-commerce application built with Laravel 12, featuring a comprehensive admin panel, shopping cart management, and integrated payment processing.

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Database Models](#database-models)
- [API Routes](#api-routes)
- [Admin Panel](#admin-panel)
- [Payment Integration](#payment-integration)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### Customer Features
- 🏪 Browse products by categories and brands
- 🔍 Search and filter products
- 🛒 Shopping cart management (add, update, remove items)
- 💰 Discount coupon system
- 💳 Multiple payment options (PayPal, Google Pay, Stripe)
- 📦 Order management and tracking
- 👤 User accounts and profiles
- 📍 Multiple address management

### Admin Features
- 📊 Dashboard and analytics
- 🏷️ Brand management (CRUD operations)
- 📂 Category management (CRUD operations)
- 📦 Product management with image uploads
- 💲 Coupon code creation and management
- 📋 Order management and order details
- 👥 User management
- 🔐 Role-based access control

## 🛠️ Tech Stack

- **Backend**: Laravel 12.0
- **Frontend**: Laravel Blade, Tailwind CSS, Bootstrap 5
- **Build Tool**: Vite 7
- **Database**: MySQL/PostgreSQL (via Laravel migrations)
- **PHP Version**: ^8.2
- **Payment Gateway**: PayPal (srmklive/paypal ~3.0)
- **Shopping Cart**: Surfside Media Shopping Cart 2.0
- **Image Processing**: Intervention Image 3.11
- **Testing**: Pest 4.3
- **UI Framework**: Bootstrap 5.3.8, Tailwind CSS 4.0

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- PHP 8.2 or higher
- Composer
- Node.js (v16 or higher)
- npm or yarn
- MySQL or PostgreSQL
- Git

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/ecommerce-project.git
cd ecommerce-project
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Copy Environment File
```bash
cp .env.example .env
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Create Database
Create a new database in your MySQL/PostgreSQL server:
```sql
CREATE DATABASE ecommerce_db;
```

### 6. Update Environment Variables
Edit `.env` file with your database credentials:
```env
APP_NAME="E-Commerce Platform"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 7. Run Database Migrations
```bash
php artisan migrate --seed
```

### 8. Install Node Dependencies
```bash
npm install
```

### 9. Build Frontend Assets
```bash
npm run build
```

### 10. Generate Storage Link
```bash
php artisan storage:link
```

## ⚙️ Configuration

### PayPal Configuration
Update your PayPal credentials in `.env`:
```env
PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_CLIENT_SECRET=your_client_secret
```

### Mail Configuration
Configure your mail provider in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

### Image Upload Configuration
Product images are stored in the `storage/app/public` directory. Ensure the storage link is created:
```bash
php artisan storage:link
```

## 💻 Usage

### Start Development Server
```bash
php artisan serve
```

### Run Frontend Development Server (in another terminal)
```bash
npm run dev
```

Access the application at: `http://localhost:8000`

### Admin Panel Access
Navigate to: `http://localhost:8000/admin`
(Requires admin authentication)

### Create Admin User (Optional)
```bash
php artisan tinker
>>> \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'is_admin' => true]);
```

## 📁 Project Structure

```
project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── ShopController.php
│   │   │   ├── CartController.php
│   │   │   ├── PaypalController.php
│   │   │   ├── UserController.php
│   │   │   └── Admin/AdminController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Category.php
│   │   ├── Brand.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Coupon.php
│   │   ├── Address.php
│   │   └── Transaction.php
│   └── Providers/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── paypal.php
│   └── ...
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   ├── css/
│   ├── js/
│   └── sass/
├── routes/
│   └── web.php
├── public/
│   ├── index.php
│   ├── assets/
│   └── uploads/
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
├── tests/
└── vendor/
```

## 🗄️ Database Models

### User
- User authentication and profiles
- Role-based access (admin/customer)

### Product
- Product information (name, description, price)
- Image storage
- Brand and Category relationships
- Inventory management

### Category
- Product categorization
- Hierarchical structure support

### Brand
- Brand information and branding

### Order
- Order tracking and management
- Order status
- Customer information

### OrderItem
- Individual items within an order
- Quantity and pricing information

### Coupon
- Discount codes
- Expiry dates
- Discount percentages/amounts

### Address
- Customer delivery and billing addresses
- Multiple addresses per user

### Transaction
- Payment transaction records
- PayPal and other payment gateway integration

## 🛣️ API Routes

### Public Routes
- `GET /` - Home page
- `GET /shop` - Shop listing
- `GET /shop/product/details/{product_slug}` - Product details
- `GET /cart` - View cart
- `POST /cart/add` - Add to cart
- `POST /cart/coupon-apply` - Apply coupon
- `GET /checkout` - Checkout page
- `POST /place-an-order` - Place order

### User Routes (Protected)
- `GET /user` - User dashboard
- `GET /user/paypal/payment/{order_id}` - PayPal payment
- `GET /user/paypal/success/{order_id}` - PayPal success callback
- `GET /user/paypal/cancel` - PayPal cancel callback

### Admin Routes (Protected + Admin Middleware)
- `GET /admin` - Admin dashboard
- `GET /admin/brands` - List brands
- `POST /admin/brand/store` - Create brand
- `PUT /admin/brand/update` - Update brand
- `DELETE /admin/brand/delete/{id}` - Delete brand
- `GET /admin/categories` - List categories
- `POST /admin/category/store` - Create category
- `GET /admin/products` - List products
- `POST /admin/product/store` - Create product
- `GET /admin/coupons` - List coupons
- `GET /admin/orders` - List orders

## 🎛️ Admin Panel

The admin panel provides comprehensive management tools:

### Dashboard
- Overview of key metrics
- Recent orders and activities

### Brands Management
- Create, edit, and delete brands
- Brand listings

### Categories Management
- Category CRUD operations
- Organize product categories

### Products Management
- Add/edit products with images
- Manage product details, prices, inventory
- Associate products with brands and categories

### Coupons Management
- Create discount coupons
- Set expiry dates and discount amounts
- Track coupon usage

### Orders Management
- View all orders
- Order status tracking
- Order details and customer information

## 💳 Payment Integration

### PayPal
- Sandbox and production support
- Secure payment processing
- Success and cancel callbacks

### Google Pay / Stripe (Ready for implementation)
- Infrastructure in place for Stripe integration
- Google Pay support prepared

## 🧪 Testing

Run tests using Pest:

```bash
# Run all tests
php artisan pest

# Run specific test file
php artisan pest tests/Feature/CartTest.php

# Run with coverage
php artisan pest --coverage
```

## 📝 Environment Variables

Key environment variables to configure:

```env
APP_NAME=E-Commerce
APP_ENV=local|production
APP_DEBUG=true|false
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_db
DB_USERNAME=root
DB_PASSWORD=password

PAYPAL_MODE=sandbox|live
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_CLIENT_SECRET=your_secret

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Author

Created with ❤️ by the development team.

## 📧 Support

For support, email support@example.com or open an issue in the GitHub repository.

## 🔮 Future Enhancements

- [ ] Mobile app (React Native)
- [ ] Advanced analytics dashboard
- [ ] Inventory management system
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Product recommendations
- [ ] User reviews and ratings
- [ ] Wishlist feature
- [ ] Advanced filtering and search
- [ ] Multi-language support

---

**Last Updated**: January 2026

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
