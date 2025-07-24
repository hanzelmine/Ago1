# ⛪ Church Information System - Ago1

A web-based Church Information System developed using PHP and MySQL, designed to manage and organize church data including families, members (jemaat), and church zones (rayon). This system provides a user-friendly interface for admins to input, update, and generate reports on church community data.

## 🛠️ Tech Stack

- **Backend:** PHP (vanilla)
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Server:** XAMPP (Apache + MySQL)
- **UI Framework:** AdminLTE3
- **PDF Generation:** mPDF
- **JS Plugins:** jQuery, SweetAlert2, DataTables

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

## 🚀 Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/hanzelmine/Ago1.git
   ```

2. **Set Up XAMPP**
   - Move the project folder to `htdocs/`
   - Start Apache and MySQL from XAMPP Control Panel

3. **Import Database**
   - Open `phpMyAdmin`
   - Create a database (e.g., `church_info`)
   - Import the SQL dump file (e.g., `database.sql`) if available

4. **Configure Connection**
   - Open `database.php`
   - Update DB credentials if needed:
     ```php
     $conn = mysqli_connect("localhost", "root", "", "church_info");
     ```

5. **Run the Project**
   - Open browser and go to:  
     [http://localhost/Ago1](http://localhost/Ago1)

## 🖼️ Screenshots

> *You can add screenshots here for dashboard, form modals, or PDF reports for better visualization.*

## 🧩 Directory Structure

\`\`\`
Ago1/
├── assets/             # CSS, JS, images
├── functions/          # PHP logic for each module
├── pages/              # Modular pages (jemaat, keluarga, rayon, etc.)
├── vendor/             # mPDF and dependencies
├── index.php           # Main dashboard
├── database.php        # DB connection
└── README.md
\`\`\`

## 📄 License

This project is open-source and licensed under the [MIT License](LICENSE).

---

## 👤 Author

- **Hanzel Mine**  
  [github.com/hanzelmine](https://github.com/hanzelmine)

---

> Feel free to fork and improve this system for your local church or organization!
