# Font Group System

A web application for managing custom fonts and creating font groups with customizable size and price settings.

## Features

- Upload TTF fonts with drag and drop functionality
- View uploaded fonts with accurate font previews
- Create font groups with multiple fonts
- Customize font size and price for each font in a group
- Edit and delete font groups
- Single-page application without page reloads

## Technologies Used

- PHP (OOP approach with SOLID principles)
- JavaScript (Vanilla JS)
- Bootstrap CSS framework
- MySQL database
- FontFace API for font loading and preview

## Installation

1. Clone the repository:

   ```
   git clone https://github.com/rashed9810/font-group-manager.git
   ```

2. Create a MySQL database named `font_group_system`

3. Import the database schema:

   ```
   mysql -u username -p font_group_system < db/schema.sql
   ```

4. Update database configuration in `config/config.php` if needed

5. Make sure the `uploads/fonts` directory is writable:

   ```
   chmod 777 uploads/fonts
   ```

6. Set up a web server (Apache/Nginx) to serve the application

## Usage

### Uploading Fonts

1. Drag and drop TTF font files onto the upload area or click to select files
2. Uploaded fonts will appear in the font list with accurate previews

### Creating Font Groups

1. Enter a group title
2. Select at least 2 fonts from the dropdown menus
3. Adjust size and price values using the up/down buttons or by typing
4. Click "Create Font Group" to save

### Managing Font Groups

- View all font groups in the list
- Edit a font group by clicking the "Edit" button
- Delete a font group by clicking the "Delete" button

## Project Structure

- `api/` - API endpoints for AJAX requests
- `assets/` - CSS, JavaScript, and other assets
- `classes/` - PHP classes (models, repositories)
- `config/` - Configuration files
- `db/` - Database-related files
- `uploads/` - Directory for uploaded fonts
- `utils/` - Utility functions

## License

MIT
