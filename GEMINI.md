# Tuki - Project Intelligence

Tuki is a modern Event Management and Ticketing SaaS platform built with Laravel 12. It features a multi-tenant-like structure for Admins, Organizers, and Customers, with a focus on a high-end "Modern SaaS" frontend UI and Spanish localization.

## 🚀 Project Overview

- **Core Stack:** Laravel 12.x, PHP 8.2+, MySQL.
- **Frontend:** Blade Templates, Bootstrap, jQuery, Laravel Mix.
- **Styling:** Moving towards a "Modern SaaS" aesthetic (inspired by Stripe, Linear, Vercel) using Inter font and custom CSS (`admin-skin.css`, `app.css`).
- **Key Domains:**
  - **Events:** Booking, ticketing, variations, QR codes.
  - **Shop:** Product management, orders, cart.
  - **Payments:** Multi-gateway integration (MercadoPago, Stripe, PayPal, Razorpay, etc.).
  - **Auth:** Custom auth for Customers and Organizers + Socialite (Google/Facebook).

## 🛠 Building and Running

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL

### Development Commands
- **Install Dependencies:** `composer install` & `npm install`
- **Asset Compilation:**
  - Dev: `npm run dev`
  - Watch: `npm run watch`
  - Production: `npm run prod`
- **Database:** `php artisan migrate`
- **Running Server:** `php artisan serve` or use Docker (`docker-compose up -d`)
- **Testing:** `php artisan test` or `./vendor/bin/phpunit`

## 📏 Development Conventions & Strict Rules

This project follows a **Precision Execution** model to optimize token usage and maintain system integrity.

### General Standards
- **Localization:** ALL frontend text visible to customers MUST be in Spanish (`es`).
- **UI/UX:** Adhere to the "Modern SaaS" redesign guidelines (Minimalist, Inter font, specific palette: Orange `#F97316` + Dark Gray `#1e2532`).
- **Architecture:** Keep logic in Controllers or dedicated Helpers/Services. Avoid bloated Models.
- **Large Files:** `app/Http/Helpers/Helper.php` is extremely large. **Always search for specific functions before reading it.**

### Agent Execution Rules (Mandatory)
- **Token Optimization:** Solve tasks with maximum accuracy and minimum token usage.
- **Scoped Exploration:** Work ONLY on explicitly mentioned files. Never explore the full repo unless instructed.
- **Search First:** Use `rg` or `grep` to find context before reading files.
- **Reading Limits:** Max 300 lines per read. Never read full large files.
- **Minimal Changes:** Apply the smallest possible fix. Do not reformat unrelated code or add unnecessary abstractions.
- **No Chitchat:** Keep responses technical and concise. Max 3 lines of explanation if requested.

### File Structure Reference
- **Frontend Views:** `resources/views/frontend/`
- **Organizer Views:** `resources/views/organizer/`
- **Admin Views:** `resources/views/backend/`
- **Controllers:** `app/Http/Controllers/` (Subdivided into `FrontEnd`, `BackEnd`, etc.)
- **Routes:** `routes/web.php`, `routes/admin.php`, `routes/api.php`, `routes/organizer.php`.

## 📍 Current Focus
- Completing the "Modern SaaS" frontend redesign.
- Full Spanish translation of the customer-facing interface.
- Social Login parity for Organizers.

---
*Refer to `PENDIENTES.md` for the latest task list and `CLAUDE.md` for detailed agent execution instructions.*