# HealthDesk Core — Enterprise Healthcare Information & Provider Portal (HIMS)

An enterprise-grade, HIPAA-aligned healthcare scheduling and clinical management platform built with **Modern Laravel 11, PHP 8.2, and MySQL 8 (3NF)**. Inspired by real-world clinical workflows from **OpenEMR (v6 & v7.0.3)** and the **XHalo360 Provider Portal**.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2.12-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

---

## 🌟 Key Architecture & Features

### 1. Enterprise Role-Based Access Control (RBAC)
Dedicated portals and operational dashboards across 5 distinct healthcare roles:
* 🛡️ **Administrator:** Global hospital census, provider provisioning, and HIPAA audit controls.
* 👨‍⚕️ **Doctor (Physician):** Patient charting, clinical SOAP notes, ICD-10 diagnosis tagging, and signed encounters.
* 👩‍⚕️ **Nurse (Triage):** Patient check-in queue, vital signs intake (BP, Pulse, SpO2, Temp, Weight), and allergy screening.
* 📋 **Receptionist (Front Desk):** Arrival lobby, unique MRN generation, insurance verification (Aetna, Humana, Medicare), and copay collection.
* 🧑‍💼 **Patient Portal:** Visit history, prescribed medications, and signed encounter summaries.

### 2. Clinical Safety & Allergy Alert Engine (PR #1930)
* Automatically detects critical drug allergies (e.g. Penicillin) and combines them with Primary Care Physician (PCP) alert banners whenever a patient chart is accessed.

### 3. High-Performance 3NF Relational Database
* Strict foreign key constraints and B-Tree composite indexing on `[doctor_id, appointment_time]` for zero-latency queries.
* Eager loading (`with('doctor')`) eliminates the $N+1$ query bottleneck across high-volume schedules.

---

## 🚀 Quick Setup & Installation

### 1. Clone the repository
```bash
git clone https://github.com/SairamPulipati/healthdesk-api.git
cd healthdesk-api
```

### 2. Configure Environment (`.env`)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=healthdesk_db
DB_USERNAME=root
DB_PASSWORD=

# 100% Free Local Mail Driver (Logs emails locally without third-party fees)
MAIL_MAILER=log
```

### 3. Run Migrations & Seeders (One Command Setup)
```bash
php artisan migrate:fresh --seed
```

### 4. Start the Application
```bash
php artisan serve
```
Open **`http://127.0.0.1:8000`** in your browser.

---

## 🔑 Pre-Configured Demo Credentials (Password: `password123`)

| Role | Email Login | Default Password | Primary Dashboard |
| :--- | :--- | :--- | :--- |
| **Doctor** | `doctor@healthdesk.com` | `password123` | `/doctor/dashboard` |
| **Nurse** | `nurse@healthdesk.com` | `password123` | `/nurse/dashboard` |
| **Receptionist** | `reception@healthdesk.com` | `password123` | `/reception/dashboard` |
| **Administrator** | `admin@healthdesk.com` | `password123` | `/admin/dashboard` |
| **Patient** | `patient@healthdesk.com` | `password123` | `/patient/dashboard` |

*(The login screen also features 1-click quick login chips for rapid demonstration).*

---

## 👨‍💻 Author & Lead Engineer
**Sairam Pulipati**  
*Senior Backend Software Engineer (PHP • Laravel • OpenEMR • MySQL • REST APIs)*  
GitHub: [@SairamPulipati](https://github.com/SairamPulipati) &bull; LinkedIn: [in/pulipati-sairam](https://linkedin.com/in/pulipati-sairam)
