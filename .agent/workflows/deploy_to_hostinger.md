---
description: Deploy/Update the application on Hostinger via SSH
---

This workflow guides you through updating your live application on Hostinger using SSH.

> [!NOTE]
> Ensure you have SSH access enabled in your Hostinger hPanel and your public SSH key is added to your GitHub repository (if private).

1. **Connect to your server via SSH**
   ```bash
   ssh -P <port> <username>@<ip_address>
   # Example: ssh -P 65002 u123456789@123.456.78.90
   ```

2. **Navigate to your application directory**
   ```bash
   # Adjust path as necessary, commonly:
   cd domains/<your-domain>/public_html
   ```

3. **Enable Maintenance Mode (Optional but recommended)**
   ```bash
   php artisan down
   ```

4. **Pull the latest changes from GitHub**
   ```bash
   git pull origin main
   ```

5. **Install/Update PHP Dependencies**
   ```bash
   # We removed a package, so this is important!
   composer install --optimize-autoloader --no-dev
   ```

6. **Run Database Migrations**
   ```bash
   # Creating the database columns change
   php artisan migrate --force
   ```

7. **Build Frontend Assets**
   ```bash
   # If Node.js is available on your plan
   npm install
   npm run build
   ```

8. **Clear and Cache Configuration**
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

9. **Restart Queue Workers (if applicable)**
   ```bash
   php artisan queue:restart
   ```

10. **Disable Maintenance Mode**
    ```bash
    php artisan up
    ```

> [!TIP]
> If you cannot run `npm run build` on Hostinger, you will need to build locally, remove `/public/build` from your `.gitignore`, and commit the built assets to the repository.
