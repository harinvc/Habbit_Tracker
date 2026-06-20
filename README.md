# 📝 Habit & Task Tracker

A modern and responsive Habit & Task Tracker built using **HTML, CSS, JavaScript, PHP, and MySQL**. This application helps users manage their daily tasks and habits through a clean interface inspired by Notion.

## ✨ Features

### 🔐 User Authentication
- User Registration
- User Login
- Guest Login
- Secure password hashing using PHP
- Session-based authentication

### ✅ Task & Habit Management
- Add new tasks and habits
- Edit existing items
- Delete items
- Mark items as completed or pending
- Separate support for:
  - 🎯 To-Do Tasks
  - 🔄 Daily Habits

### 🔍 Filtering & Search
- Search tasks by title or description
- Filter by status:
  - All
  - Pending
  - Completed

### 👤 Guest Mode
- Use the application without creating an account.
- Data is stored locally using browser Local Storage.

### 🛡️ Admin Features
- View registered users
- View total task count
- Delete users
- Role-based access control

### 📱 Responsive Design
- Mobile-friendly interface
- Modern UI inspired by Notion
- Built using Tailwind CSS

---

## 🛠️ Technologies Used

### Frontend
- HTML5
- CSS3
- JavaScript (Vanilla JS)
- Tailwind CSS
- Font Awesome

### Backend
- PHP
- MySQL
- PDO (PHP Data Objects)

### Local Development Tools
- XAMPP / WAMP / Laragon
- phpMyAdmin

---

## 📂 Project Structure

```
Habit-Task-Tracker/
│
├── index.html      # Frontend application
├── api.php         # Backend API endpoints
├── README.md       # Project documentation
└── database.sql    # Database schema (optional)
```

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/habit-task-tracker.git
```

### 2. Move Project to Server Directory

For XAMPP:

```
xampp/htdocs/habit-task-tracker
```

### 3. Create Database

Open phpMyAdmin and create a database named:

```sql
habit_tracker
```

### 4. Create Tables

#### Users Table

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Tasks Table

```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('todo','habit') NOT NULL,
    status ENUM('pending','completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 5. Configure Database

Update the credentials inside `api.php`:

```php
$host = 'localhost';
$dbname = 'habit_tracker';
$user = 'root';
$pass = '';
```

### 6. Run the Application

Start Apache and MySQL from XAMPP and open:

```
http://localhost/habit-task-tracker/
```

---

## 💡 Demo Credentials

### Admin Account

Create an admin user manually in the database or modify an existing user:

```
Username: admin
Password: YourPassword
Role: admin
```

### Guest Access

Click **"Continue as Guest"** to use the application without registration.

---

## 🔮 Future Improvements

- Dark Mode
- Habit Streak Tracking
- Progress Charts and Analytics
- Email Notifications
- Password Reset
- Profile Pictures
- Drag-and-Drop Task Sorting
- PWA Support for Offline Usage

---

## 📸 Screenshots

Add screenshots of your application here.

Example:

```
screenshots/dashboard.png
screenshots/login.png
```

---

## 🤝 Contributing

Contributions are welcome.

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to your branch
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License.

---

## 👨‍💻 Author

**Harin**

Built as a personal project to improve productivity through habit tracking and task management.
