# PHP/MySQL account system

This project is ready for PHP + MySQL/MariaDB hosting such as InfinityFree.

## Deployment

1. In the InfinityFree control panel, create a MySQL database.
2. Open phpMyAdmin for that database and import `database.sql`.
3. Edit `config.php` with the exact database hostname, database name, username, and password shown in InfinityFree's **MySQL Databases** panel. The hostname is commonly not `localhost`.
4. Upload all project files and folders to the site's `htdocs` directory using the File Manager or FTP.
5. Open `https://your-domain/` (the included `index.html` redirects to `login.php`) and register the first account.

Do not upload the former `.html` pages: this version uses the `.php` pages. Accounts are stored in MySQL and passwords are stored only as password hashes.

## Important

- Keep `config.php` private; do not post its completed database credentials online or commit it to a public repository.
- Enable HTTPS for the deployed domain. PHP marks session cookies as secure when the site is served over HTTPS.
