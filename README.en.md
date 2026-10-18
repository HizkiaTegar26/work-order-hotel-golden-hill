[ Read in English 🇬🇧 ](README.en.md) | [ Bahasa Indonesia 🇮🇩 ](README.md)
<hr>

# WORK ORDER INFORMATION SYSTEM, ASSET MANAGEMENT, AND ANALYTIC PERFORMANCE V2.0
### Hotel Golden Hill by Golden Tulip Batu
**Based on Final Project:** Hizkia Tegar Kurniawan (Student ID / NIM: 223140707111136, Universitas Brawijaya)  
**Development Tier:** Enterprise Industrial Grade (V2.0)

---

## 📸 Visual Showcase

<div align="center">

### Executive KPI & Performance Analytics Dashboard
![Executive Dashboard](docs/screenshots/01-dashboard.png)

<br>

### Work Order Monitoring & SLA Countdown Timer
![Monitoring & SLA](docs/screenshots/02-sla-timer.png)

<br>

</div>

| 🔍 Asset QR Code Scanner | 🌐 Multi-Language (ID / EN) |
| :---: | :---: |
| ![QR Scanner](docs/screenshots/03-qr-scanner.png) | ![Language Switch](docs/screenshots/04-language-switch.png) |

---

## 1. System Architecture & Tech Stack
- **Backend Framework:** PHP 8.2+ with CodeIgniter 4 (MVC Architecture)
- **Database:** MySQL / MariaDB (Database Name: `ci4_workorder_v2`)
- **Frontend UI/UX:** Bootstrap 5, HTML5, Custom CSS3, Vanilla JS, DataTables.net, Chart.js 4.4
- **Enterprise Libraries:**
  - **Excel Engine:** `phpoffice/phpspreadsheet`
  - **PDF Generator:** `dompdf/dompdf`
  - **QR Code Engine:** `endroid/qr-code` (Backend PNG Generator) & `html5-qrcode` (Live Camera Scanner)
  - **WhatsApp API Gateway:** Fonnte / Wablas integration module with local simulation sandbox fallback
- **Server Environment:** XAMPP / Apache Local Architecture (`http://localhost/work-order/`)

---

## 2. Relational Database Structure (13 Tables)
The `ci4_workorder_v2` database is designed with InnoDB engine, Primary Keys, Foreign Key constraints, and indexes:
1. `roles`: User role master (`Admin`, `Kepala Departemen` / Dept Head, `Staf` / Field Staff).
2. `departemen`: Operational unit master (`Engineering & Maintenance`, `IT & Systems`, `Housekeeping`, `Front Office`, `Food & Beverage`).
3. `users`: User entities with BCRYPT password hashing, WhatsApp numbers, department relations, roles, and preferred language.
4. `kategori_wo`: Work order classifications (`Maintenance`, `IT Support`, `Housekeeping Service`, `General Repair`).
5. `status_wo`: Ticket lifecycle stages (`New`, `Accepted`, `Rejected`, `Assigned`, `In Progress`, `Completed`, `Awaiting Confirmation`, `Escalated`).
6. `prioritas_sla`: Target resolution SLAs (`Low` 24 Hours, `Medium` 12 Hours, `High` 4 Hours, `Emergency` 1 Hour).
7. `aset_hotel`: Hotel facility registry, unique asset codes, departments, physical locations, QR code image paths, and physical condition (`Good`, `Light Damage`, `Heavy Damage`).
8. `inventaris_barang`: Spare parts inventory, units of measurement, and unit material costs.
9. `work_order`: Core ticket transactions, reporting user relation, responsible department, assigned technician, approval verification, actual work duration, and cost calculation.
10. `wo_spareparts`: Many-to-many relation for spare parts utilized per work order along with cost subtotals.
11. `log_audit`: Comprehensive chronological audit trail of all CRUD actions, authentication, automatic escalation events, and client IP addresses.
12. `reset_password_token`: Cryptographically secure self-service password recovery tokens with 1-hour expiration.
13. `notification_queue`: Asynchronous WhatsApp & Email notification queue supporting retry limits and automatic SMTP failover.

---

## 3. Default Multi-Role User Accounts
| No | Role | Username | Password | Full Name | Department |
|:---|:---|:---|:---|:---|:---|
| 1 | **Admin** | `admin` | `admin123` | Hotel Administrator | IT & Systems |
| 2 | **Admin** | `hizkia` | `hizkia123` | Hizkia Tegar Kurniawan | IT & Systems |
| 3 | **Dept Head** | `kadep_eng` | `kadep123` | Ir. Budi Santoso | Engineering & Maintenance |
| 4 | **Staff** | `staf_eng` | `staf123` | Joko Prasetyo | Engineering & Maintenance |
| 5 | **Dept Head** | `kadep_it` | `kadep123` | Rian Pratama, S.Kom | IT & Systems |
| 6 | **Staff** | `staf_it` | `staf123` | Fajar Ramadhan | IT & Systems |
| 7 | **Dept Head** | `kadep_hk` | `kadep123` | Siti Aminah | Housekeeping |
| 8 | **Staff** | `staf_hk` | `staf123` | Dewi Lestari | Housekeeping |
| 9 | **Staff** | `staf_fo` | `staf123` | Reza Firmansyah | Front Office |

*Quick Role Switcher Feature*: Evaluators can instantly switch between roles via the **Switch Role (Demo)** button on the bottom sidebar to test multi-role workflows without recurring manual logins.

---

## 4. Key Enterprise Modules & Capabilities
1. **Live SLA Countdown Timer & Auto-Escalation Engine:**
   - Computes dynamic remaining resolution time based on SLA priority targets.
   - Real-time visual status badges (Green: Healthy, Yellow: <30% remaining, Red/Blinking: Overdue).
   - Automated escalation engine transitions overdue tickets to `Escalated` status and triggers priority emergency notifications to management.
2. **Asynchronous WhatsApp Gateway & Email Failover:**
   - Non-blocking notification dispatch via `notification_queue` and `NotificationService`.
   - **Automatic Failover** to HTML Email (SMTP) whenever the WhatsApp gateway encounters timeouts or unreachable recipients.
3. **Asset QR Code Scanner & Camera Mitigations:**
   - Client-side HTML5 camera scanner operating directly from desktop and mobile browsers.
   - Equipped with **Flashlight / Torch Toggle**, camera switcher (front/rear), and a fallback flow for manual asset code input or searchable dropdown selection when camera access is restricted.
4. **Inventory Deduction & Maintenance Costing (Pessimistic Locking):**
   - Concurrency-safe atomic database transactions using row-level locking (`SELECT ... FOR UPDATE`) on `inventaris_barang`.
   - Automatic transaction rollback with user-friendly warnings if requested stock exceeds availability.
5. **Full Internationalization (i18n Dual-Language ID/EN):**
   - Dynamic navbar language switcher (`/lang/id` and `/lang/en`) with session, cookie, and user profile database persistence.
   - Seamless locale adaptation across all views, validation messages, DataTables tables, and generated PDF/Excel reports.
6. **Basement Blindspot Mitigation (PWA & Offline Draft Auto-Sync):**
   - Service Worker (`sw.js`) and Web App Manifest enable basic application shell access in low-signal utility or basement areas.
   - Form drafts are preserved in browser `localStorage` while offline and automatically uploaded via background sync once connectivity is restored.
7. **Executive KPI Analytics & Interactive Dashboards:**
   - High-impact Chart.js visualizations:
     - *Average Response Time* (Submission to Kadep Verification)
     - *Average Resolution Time* (Assignment to Completion)
     - *SLA Compliance Rate* (% Completed On-Time vs Overdue)
     - *Spare Part Expenses by Department*
     - *Top 5 Most Frequently Repaired Assets*
8. **Enterprise Security, Password Reset & Audit Trail:**
   - 32-byte cryptographically secure password reset tokens valid for 1 hour.
   - Complete audit trail logging (`log_audit`) capturing user IDs, roles, actions, timestamps, and client IP addresses.

---

## 5. Local Installation & Setup Guide

### System Prerequisites
- PHP 8.2 or newer (with extensions: `intl`, `mbstring`, `mysqli`, `gd`, `curl`)
- MySQL / MariaDB (XAMPP local server)
- Composer 2.x
- Apache Web Server (XAMPP directory: `C:\xampp\htdocs\work-order`)

### Installation Steps
1. **Clone the Repository:**
   ```bash
   git clone https://github.com/HizkiaTegar26/work-order-hotel-golden-hill.git
   cd work-order-hotel-golden-hill
   ```
2. **Install PHP Dependencies:**
   ```bash
   composer install
   ```
3. **Environment Configuration (`.env`):**
   Copy `env` to `.env` and configure your database parameters:
   ```ini
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost/work-order/'
   database.default.hostname = localhost
   database.default.database = ci4_workorder_v2
   database.default.username = root
   database.default.password = 
   database.default.DBDriver = MySQLi
   ```
4. **Execute Database Migrations & Seeders:**
   ```bash
   php spark migrate
   php spark db:seed DatabaseSeeder
   ```
5. **Launch the Application:**
   Open your browser and navigate to [http://localhost/work-order/](http://localhost/work-order/).

---

## 6. Automated Testing & Quality Assurance

### 6.1 Black Box End-to-End Validation Suite (25 Test Cases)
```bash
php spark test:blackbox
```
Validation Result: **25 VALID (PASS) / 0 FAIL (100% PASS RATE)**.

### 6.2 Robustness & Failover Suite (15 Test Cases)
```bash
php spark test:robustness
```
Validation Result: **15 VALID (PASS) / 0 FAIL (100% PASS RATE)**.

---

## 7. Supporting CLI Commands
- `php spark wo:escalate` : Triggers automated evaluation of overdue tickets for escalation.
- `php spark queue:work --once` : Processes queued WhatsApp notifications and executes email failover if needed.
- `php spark test:blackbox` : Executes the full 25-scenario multi-role Black Box test suite.
- `php spark test:robustness` : Runs the 15-scenario robustness, caching, and concurrency test suite.
- `php spark db:seed DatabaseSeeder` : Seeds realistic demonstration data for all operational modules.

---

## 8. Author & License
- **Author:** Hizkia Tegar Kurniawan (Student ID: 223140707111136)
- **Institution:** D4 Informatics Management Study Program, Vocational Faculty, Universitas Brawijaya
- **Industry Partner:** Hotel Golden Hill by Golden Tulip Batu
- **License:** MIT License - Free to use and adapt for academic and hospitality operational purposes.
