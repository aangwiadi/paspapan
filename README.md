<div align="center">

![PasPapan Hero](./public/hero-banner.png)

# PasPapan - Enterprise Attendance System

**Advanced GPS Geofencing, Biometric Verification & Payroll Solution**

[![Laravel 11](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire 3](https://img.shields.io/badge/Livewire-3-4E56A6?style=flat&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.4-38B2AC?style=flat&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Capacitor](https://img.shields.io/badge/Capacitor-6.0-1199EE?style=flat&logo=capacitor&logoColor=white)](https://capacitorjs.com)

</div>

---

## Overview

**PasPapan** is a comprehensive Human Resource Information System (HRIS) designed for modern hybrid workforces. It bridges the gap between secure physical attendance and remote flexibility using advanced location validation, native mobile capabilities, and a robust web administration panel.

---

## 🔄 System Workflow

1.  **Check-In Request**: User initiates attendance via Mobile App / PWA.
2.  **Validation Layer**:
    *   **GPS**: Verifies user is within permitted office radius (Geofencing).
    *   **Anti-Fake GPS**: Analyzes signal accuracy and variance.
    *   **Biometrics**: Scans Face ID matching user profile.
3.  **Data Processing**: Server records timestamp, coordinates, and photo evidence.
4.  **Administrative Action**: Supervisors receive notifications; data flows into Payroll calculation automatically.

---

## Key Features

### Attendance & Validation
- **GPS Geofencing**: High-precision location validation ensuring employees check-in only within designated office radii.
- **Fake GPS Detection**: Intelligent algorithms to analyze GPS accuracy, variance, and consistency to flag suspicious location spoofing attempts.
- **Face ID Verification**: AI-powered facial recognition to verify identity during attendance (Anti-Buddy Punching).
- **QR Code Scanning**: Dynamic QR code support for secure on-site verification.
- **Biometric Evidence**: Required selfie photo attachment with every attendance record.
- **Secure Photo Access**: Privacy-first file storage ensuring attendance photos are accessible only to authorized personnel, compatible with secure tunnels.

### HR & Management Modules
- **Payroll System**: Automated salary calculation including basic pay, overtime, and deductions with professional PDF payslip generation.
- **Overtime Management**: Digital workflow for overtime requests and supervisor approvals.
- **Leave & Reimbursement**: Integrated portals for leave applications and expense claims.
- **Shift Management**: Flexible shift scheduling with auto-detection logic.
- **Team Approvals**: Hierarchical approval system for managers and supervisors.

### Platform Capabilities
- **Enterprise Dashboard**: Real-time analytics, employee monitoring, and extensive reporting options (pro-rated logic, export to Excel).
- **Mobile Super App**: Native Android experience via Capacitor, supporting offline mode and background location services.
- **PWA Support**: Fully installable Progressive Web App (v1.9.3) for Desktop, iOS, and Android with verified manifest and service worker caching.
- **Multi-Language**: Native support for English and Indonesian (Bahasa Indonesia).

---

## 📸 Application Previews

<details>
<summary><b>💻 Admin Dashboard (Web)</b></summary>
<br>

| Dashboard & Monitoring | Attendance Data |
| :---: | :---: |
| ![Dashboard](./screenshots/admin/01_Dashboard.png) | ![Attendance](./screenshots/admin/02_DataAbsensi.png) |

| Leave Approval | Overtime Management |
| :---: | :---: |
| ![Leave](./screenshots/admin/03_PersetujuanCuti.png) | ![Overtime](./screenshots/admin/04_ManagementLembur.png) |

| Shift Scheduling | Analytics Dashboard |
| :---: | :---: |
| ![Shift](./screenshots/admin/05_ManagemetShift.png) | ![Analytics](./screenshots/admin/06_DashboardAnalitik.png) |

| Calendar & Holidays | Announcements |
| :---: | :---: |
| ![Calendar](./screenshots/admin/07_LiburKalender.png) | ![Announcements](./screenshots/admin/08_Announcements.png) |

| Payroll Management | Reimbursements |
| :---: | :---: |
| ![Payroll](./screenshots/admin/09_Payroll.png) | ![Reimbursements](./screenshots/admin/10_Reimbursement.png) |

| Allowances & Deductions | Barcode Management |
| :---: | :---: |
| ![Allowances](./screenshots/admin/11_Allowances.png) | ![Barcode](./screenshots/admin/12_Barcode.png) |

| App Settings | Maintenance Mode |
| :---: | :---: |
| ![Settings](./screenshots/admin/13_AppSettings.png) | ![Maintenance](./screenshots/admin/14_Maintance.png) |

| User Import/Export | Attendance Export |
| :---: | :---: |
| ![Export Users](./screenshots/admin/15_ExportImportEmployee.png) | ![Export Attendance](./screenshots/admin/16_ExportImportAttendance.png) |

</details>

<details>
<summary><b>📱 Mobile App (Android/PWA)</b></summary>
<br>

| Login Screen | Home (Face Registered) | Home (New User) |
| :---: | :---: | :---: |
| <img src="./screenshots/users/01_Login.png" width="250"> | <img src="./screenshots/users/02_HomeFace.png" width="250"> | <img src="./screenshots/users/03_Home.png" width="250"> |

| Attendance History | Leave Request | Overtime Request |
| :---: | :---: | :---: |
| <img src="./screenshots/users/04_History.png" width="250"> | <img src="./screenshots/users/05_LeaveRequest.png" width="250"> | <img src="./screenshots/users/06_Overtime.png" width="250"> |

| Reimbursement | Payslip | Profile |
| :---: | :---: | :---: |
| <img src="./screenshots/users/07_Reimbursement.png" width="250"> | <img src="./screenshots/users/08_Payslip.png" width="250"> | <img src="./screenshots/users/09_Profile.png" width="250"> |

| Schedule | Face Registration | Scan QR |
| :---: | :---: | :---: |
| <img src="./screenshots/users/10_Schedule.png" width="250"> | <img src="./screenshots/users/11_FaceID.png" width="250"> | <img src="./screenshots/users/12_ScanQR.png" width="250"> |

| Scan Error | Selfie Evidence | Check-Out Success |
| :---: | :---: | :---: |
| <img src="./screenshots/users/13_ScanRQError.png" width="250"> | <img src="./screenshots/users/14_Selfi.png" width="250"> | <img src="./screenshots/users/15_CheckOut.png" width="250"> |

| After Check-Out | | |
| :---: | :---: | :---: |
| <img src="./screenshots/users/16_HomeAfterCheckOut.png" width="250"> | | |

</details>

---

## Technology Stack

### Backend
- **Framework**: Laravel 11.x (PHP 8.3)
- **Authentication**: Laravel Sanctum & Jetstream
- **Database**: MySQL / MariaDB

### Frontend
- **Interface**: Blade Templates with Tailwind CSS 3.4
- **Interactivity**: Livewire 3 (Full-stack reactivity) & Alpine.js
- **Components**: Tom Select (Searchable Dropdowns), Chart.js (Analytics)

### Mobile & PWA
- **Engine**: Capacitor 6 (Native Bridge)
- **PWA**: Custom Service Worker strategy (Network-First)
- **Features**: Native Camera & Geolocation Plugins

---

## Installation & Setup

### Prerequisites
- PHP 8.3 or higher
- Composer
- Node.js & NPM/Bun
- MySQL Server

### Development Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/RiprLutuk/PasPapan.git
   cd PasPapan
   ```

2. **Install Dependencies**
   ```bash
   # Backend
   composer install

   # Frontend
   bun install  # or npm install
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure your database credentials in the `.env` file.*

4. **Database Migration & Seeding**
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

5. **Run Application**
   ```bash
   # Terminal 1: Vite Dev Server
   bun run dev

   # Terminal 2: Laravel Server
   php artisan serve
   ```

### Mobile Build (Android)

```bash
bun run build
npx cap sync android
cd android
./gradlew assembleDebug
```
*Output APK located at: `android/app/build/outputs/apk/debug/app-debug.apk`*

---

## ❓ Troubleshooting

**Q: GPS not working / Camera blocked?**
> A: Ensure you are serving the app via **HTTPS** (e.g., using Cloudflare Tunnel, Ngrok, or Valet Secure). Browsers block sensitive permissions on HTTP (except localhost).

**Q: Maps not loading?**
> A: This app uses OpenStreetMap/Leaflet which is free. Ensure your device has internet access to load map tiles.

---

<div align="center">

### ☕ Traktir Developer Kopi

<img src="./screenshots/donation-qr.jpeg" width="180px" style="border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

</div>

---

<div align="center">
  <p>Developed by <a href="https://github.com/RiprLutuk"><b>RiprLutuk</b></a></p>
</div>
