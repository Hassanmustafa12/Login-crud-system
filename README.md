# RecordHub — Role-Based Login + CRUD System

A beginner-friendly PHP + MySQL project with two roles:

- **Admin** — can Create, Read, Update, and Delete records, and can create new
  user accounts (admin or regular user).
- **User** — can only View and Add records. No edit/delete, and cannot create
  accounts for anyone.

Only an admin can create accounts — there is **no public "Sign Up" page** on
purpose, exactly as you described.

## 1. Requirements

- A local server with PHP + MySQL. The easiest way as a beginner is to install
  **XAMPP** (Windows/Mac/Linux) or **Laragon** (Windows).
- These come with PHP, MySQL, and phpMyAdmin already set up.

## 2. Folder setup

1. Install XAMPP and start **Apache** and **MySQL** from the XAMPP control panel.
2. Copy the whole `login-crud-system` folder into XAMPP's `htdocs` folder
   (on Windows usually `C:\xampp\htdocs\`).
3. Your project should now be reachable at:
   `http://localhost/login-crud-system/`

## 3. Create the database

1. Open `http://localhost/phpmyadmin` in your browser.
2. Click the **Import** tab.
3. Choose the file `database.sql` from this project and click **Go**.
   This creates the database `login_crud_system`, the `users` and `records`
   tables, and one default admin account.

   *(Alternative: run `mysql -u root -p < database.sql` from a terminal.)*

## 4. Update the database credentials (if needed)

Open `config.php` and check these lines match your MySQL setup
(XAMPP defaults are already filled in — username `root`, no password):

```php
$host   = "localhost";
$dbname = "login_crud_system";
$dbuser = "root";
$dbpass = "";
```

## 5. Log in

Go to `http://localhost/login-crud-system/login.php`

Default admin account:

- **Username:** `admin`
- **Password:** `admin123`

Log in, then go to **Manage Users** in the top navigation to create accounts
for other people — pick "Admin" or "User" for their role.

## 6. How the role system works

- `includes/auth.php` has two helper functions:
  - `requireLogin()` — blocks the page unless someone is logged in.
  - `requireAdmin()` — blocks the page unless the logged-in user's role is `admin`.
- Every page that needs protecting calls one of these at the very top,
  right after `config.php` is included.
- The navigation bar (`includes/header.php`) also hides the "Manage Users"
  link and the Edit/Delete buttons from regular users, so the UI matches
  what they're actually allowed to do.

## File map

```
login-crud-system/
├── database.sql          → run this once in phpMyAdmin
├── config.php             → database connection
├── index.php               → redirects to login or dashboard
├── login.php               → sign-in form
├── logout.php              → destroys the session
├── dashboard.php           → view all records (everyone)
├── add_record.php          → add a record (everyone)
├── edit_record.php         → edit a record (admin only)
├── delete_record.php       → delete a record (admin only)
├── manage_users.php        → create/delete accounts (admin only)
├── includes/
│   ├── auth.php             → login/role-check helper functions
│   ├── header.php           → navbar + page <head>
│   └── footer.php           → closing tags + script include
└── assets/
    ├── css/style.css        → all styling
    └── js/script.js         → small UX touches (auto-hide alerts)
```

## Security notes (already handled for you, but good to know)

- Passwords are stored using PHP's `password_hash()` (bcrypt) — never in plain text.
- All database queries use prepared statements (PDO with `?` placeholders) to
  prevent SQL injection.
- All output is passed through `htmlspecialchars()` to prevent XSS.
- Every protected page checks the session on the server side — hiding a
  button in the UI is not what actually blocks access; the `requireAdmin()`
  check in the PHP file is what really enforces it.

Enjoy, and feel free to extend it (e.g. add a "change my password" page,
or add search/filter to the records table) as your next practice step!
