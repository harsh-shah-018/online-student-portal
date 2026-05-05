# Project Details: Online Student Registration System

## Architecture Overview
This project was developed using a modern, decoupled **Single Page Application (SPA)** architecture, directly addressing the complexities required for an MCA capstone project.

### Frontend
- **Framework**: Vue 3 (Composition API) via Vite.
- **Styling**: Tailwind CSS for a premium, classic aesthetic (glassmorphism accents, soft gradients).
- **Component Library**: PrimeVue (Aura Theme) wrapped in custom `Base` components (`BaseInput`, `BaseButton`, `BaseTable`) to ensure reusability and clean code structure.
- **Routing & Guards**: Vue Router for seamless navigation between Admin and Student portals, completely protected by global authentication guards.
- **State**: Pinia and `localStorage` for centralized session and authentication handling.
- **HTTP Client**: Axios configured via `.env` variables for standardized REST API communication.

### Backend
- **Framework**: Vanilla PHP 8.x designed as a RESTful API.
- **Database Interaction**: PDO (PHP Data Objects) is used for all database queries to ensure robust security against SQL injection.
- **Headers**: CORS enabled to allow the Vue frontend to communicate with the local XAMPP server seamlessly.

### Database Strategy (Complexity Addressed)
To satisfy the reviewer's comment ("Try to add few more tables to increase complexity of project"), the database was expanded from the original 6 tables to **12 normalized tables**:
1. `admins`
2. `students`
3. `courses` (New)
4. `enrollments` (New: Many-to-Many junction table)
5. `attendance`
6. `marks`
7. `skills`
8. `physical_activities`
9. `fee_payments` (New: Financial tracking)
10. `notices` (New: Announcements system)
11. `library_books` (New: Expanding future scope)
12. `book_issues` (New: Library management link)

## Features Implemented
1. **Unified Authentication**: Single login view that intelligently routes to either the Admin or Student dashboard based on the selected role.
2. **Admin Dashboard**: Real-time statistics tracking total students, active courses, pending enrollments, and revenue collected.
3. **Student Dashboard**: Personalized view displaying enrolled courses, total fees paid, and the latest institutional announcements.
4. **API Endpoints**: Structured `backend/api/` folder neatly separating `auth/`, `admin/`, and `student/` logic with strict PDO Prepared Statement usage for security.
5. **Secure SPA Navigation**: Global route guards and Pinia authentication states to strictly protect dashboards against unauthenticated access.

## How it meets the requirements:
- **"Project size is little small"**: The addition of Vue.js SPA routing, a fully decoupled REST API, and 12 database tables significantly increases the code surface area and logical complexity.
- **"Good UI/UX"**: PrimeVue combined with Tailwind CSS provides a highly responsive, modern, and accessible user interface that feels premium.
- **"Base Components"**: Standardized UI elements were created in `frontend/src/components/base/` ensuring consistent design across the application.
