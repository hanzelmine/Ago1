# ⛪ Church Information System - Ago1

A web-based Church Information System developed using PHP and MySQL, designed to manage and organize church data including families, members (jemaat), and church zones (rayon). This system provides a user-friendly interface for admins to input, update, and generate reports on church community data.

---

## 🛠️ Tech Stack

- **Backend:** PHP (vanilla)
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Server:** XAMPP (Apache + MySQL)
- **UI Framework:** AdminLTE3
- **PDF Generation:** mPDF
- **JS Plugins:** jQuery, SweetAlert2, DataTables

---

## 📦 Features

- ✅ Family (Keluarga) Management
- ✅ Church Member (Jemaat) Management
- ✅ Zone/Region (Rayon) Categorization
- ✅ Dynamic Add/Edit/Detail modal forms
- ✅ Birthday Tracking
- ✅ Export to PDF and Excel
- ✅ Search and Filter with DataTables
- ✅ Secure Login System with Role-based Redirects
- ✅ Responsive AdminLTE Interface
- ✅ Modal-based Detail View & Print Functionality

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/hanzelmine/Ago1.git
```

### 2. Set Up with XAMPP

- Move the project folder into:
  ```
  C:\xampp\htdocs\gtm
  ```
- Start **Apache** and **MySQL** via XAMPP Control Panel

### 3. Import Database

- Open `phpMyAdmin`
- Create a new database, e.g. `gtm`
- Import the SQL file (if available), for example: `gtm.sql`

### 4. Configure Database Connection

- Edit the file `database.php`:

```php
$conn = mysqli_connect("localhost", "root", "", "gtm");
```

---

## ▶️ Running the Project

1. Open browser and navigate to:  
   [http://localhost/gtm](http://localhost/gtm)

2. Login using admin credentials (or register if registration is enabled)

3. Navigate through:

   - **Rayon**: Assign families to church zones
   - **Keluarga**: Manage family data
   - **Jemaat**: Manage church member details

4. Use **search & filter** for quick data access and click **Cetak** to generate PDF reports

5. Click **Logout** to securely end your session

---

---

## 🧠 Notes & Troubleshooting

- Alert and confirmation messages are powered by SweetAlert2
- DataTables may require internet or local JS files (check `assets/` or `plugins/`)
- If PDF reports do not display, verify `mPDF` is properly installed in `vendor/` folder
- In production, disable error display in `php.ini` for security

---

---

## 👤 Author

- **Hanzel Mine**  
  [github.com/hanzelmine](https://github.com/hanzelmine)

---

> Feel free to fork and adapt this system for your local church or community projects.
