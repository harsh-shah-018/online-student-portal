# Online Student Registration System (EduPortal)

## Prerequisites

1. **XAMPP** (or equivalent WAMP/MAMP stack) to run Apache and MySQL.
2. **Node.js** (v18+) to run the Vue frontend.

## Step 1: Database Setup

1. Start **Apache** and **MySQL** from your XAMPP Control Panel.
2. Open phpMyAdmin (`http://localhost/phpmyadmin`).
3. Create a new database named `student_registration_system` (or just import the file, it handles creation).
4. Import the `database.sql` file provided in the root folder.
   - _This will create all 12 tables and populate them with dummy data for testing._

## Step 2: Backend Setup

1. Ensure the entire `StudentRegistrationSystem` folder is placed inside your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\MCA Project\StudentRegistrationSystem`).
2. The PHP API endpoints are automatically served. You can verify by visiting:
   `http://localhost/MCA Project/StudentRegistrationSystem/backend/api/admin/students.php` (it should return JSON data).
   _(Adjust the URL path depending on exactly where inside `htdocs` you placed the folder)._

## Step 3: Frontend Setup

1. Open a terminal (Command Prompt / PowerShell) and navigate to the `frontend` folder.
   ```bash
   cd frontend
   ```
2. Install the necessary dependencies (Vue, Tailwind CSS, PrimeVue).
   ```bash
   npm install
   ```
3. Start the development server.
   ```bash
   npm run dev
   ```
4. Open the displayed local URL (usually `http://localhost:5173/`) in your browser.

## Demo Credentials

- **Admin**: `admin@mca.edu` / `admin123`
- **Student**: `harsh@student.edu` / `student123`
