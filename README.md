
# azerothcore-passreset

## Password Reset for AzerothCore

This script provides a password reset feature for AzerothCore-based WoW WotLK servers. It generates a time-sensitive (15-minute) reset token, saves it to the database, and sends a password reset link to the player's email. The system works even if multiple accounts are registered with the same email but different usernames.

---

## Requirements:
- **AzerothCore** (WoW WotLK Server)
- **PHP 7.x or higher**
- **MySQL/MariaDB** for the `acore_auth` database
- Access to modify your server's database and configuration files

---

## Steps to Install:

### 1. Import the SQL File:
- Find the SQL file in the `sql` folder of this package.
- Import the file into your `acore_auth` database.
  
  **Important:** Ensure that the SQL file is imported into the correct database (`acore_auth`). This will set up the necessary tables and fields for the password reset feature.

You can import the SQL file using a MySQL client like phpMyAdmin or via the command line:

```bash
mysql -u username -p acore_auth < path_to_sql_file.sql
```

Replace:
- `username` with your MySQL username
- `path_to_sql_file.sql` with the actual path to the SQL file.

---

### 2. Configure the `config.php` File:
- Go to the `conf/config.php` file.
- Modify the configuration settings:

```php
<?php
$host = ''; // Database host (e.g., localhost)
$charDbName = 'acore_characters'; // Characters database name
$authDbName = 'acore_auth'; // Authentication database name
$worldDbName = 'acore_world'; // World database name
$user = ''; // Database username
$pass = ''; // Database password
```

Make sure to replace the placeholders with your actual database connection details.

---

### 3. Update Your AzerothCore Server:
Ensure that your server is using the updated database schema, especially if you're enabling multiple accounts per email. Your server should already be capable of handling multiple usernames linked to the same email. If not, you may need to make adjustments in the core code.

---

### 4. Test the Password Reset System:
After completing the setup, test the system to ensure it works as expected:
- Generate a reset token and check if the email is received with the reset link.
- Ensure that the reset link expires after 15 minutes as intended.

---

## Troubleshooting:

- **Email Not Sent:** Ensure that your server has a working mail setup (e.g., SMTP settings).
- **Database Issues:** If the SQL import fails, ensure that you have the correct permissions for your database user.
- **Token Expiry Issues:** If the reset link doesn't expire correctly, review the token expiry time logic in the code.

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
