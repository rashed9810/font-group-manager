# Deployment Guide for Font Group System

This guide will help you deploy the Font Group System to a production environment.

## Prerequisites

- Web server (Apache, Nginx, etc.)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer (optional, for future dependencies)

## Deployment Steps

### 1. Server Setup

1. Set up a web server with PHP and MySQL support
2. Create a MySQL database for the application
3. Configure the web server to serve the application

### 2. Application Deployment

1. Clone the repository or upload the files to your server:
   ```
   git clone https://github.com/yourusername/font-group-system.git
   ```

2. Copy `config/config.sample.php` to `config/config.php` and update the values:
   ```
   cp config/config.sample.php config/config.php
   ```

3. Edit `config/config.php` with your database credentials and other settings

4. Create the database tables:
   ```
   mysql -u username -p your_database_name < db/schema.sql
   ```

5. Set proper permissions for the uploads directory:
   ```
   chmod 755 uploads/fonts
   ```

6. Run the installation script to verify everything is set up correctly:
   ```
   php install.php
   ```

### 3. Security Considerations

1. Ensure the `uploads/fonts` directory is properly secured but writable by the web server
2. Set up HTTPS for secure connections
3. Implement proper user authentication if needed
4. Consider adding rate limiting for the upload functionality

### 4. Performance Optimization

1. Enable PHP OPcache for better performance
2. Configure browser caching for static assets
3. Consider using a CDN for serving font files in high-traffic scenarios

## Troubleshooting

### Common Issues

1. **Upload issues**: Check file permissions on the `uploads/fonts` directory
2. **Database connection errors**: Verify database credentials in `config/config.php`
3. **Font preview not working**: Ensure the browser supports the FontFace API

### Getting Help

If you encounter any issues, please:

1. Check the browser console for JavaScript errors
2. Review the PHP error logs
3. Open an issue on the GitHub repository with detailed information about the problem
