#!/bin/bash

# Initialize Git repository
git init

# Add all files
git add .

# Create initial commit
git commit -m "Initial commit: Font Group System"

# Instructions for GitHub upload
echo ""
echo "Repository initialized successfully!"
echo ""
echo "To upload to GitHub, follow these steps:"
echo ""
echo "1. Create a new repository on GitHub (without README, license, or .gitignore)"
echo "2. Run the following commands:"
echo "   git remote add origin https://github.com/yourusername/font-group-system.git"
echo "   git branch -M main"
echo "   git push -u origin main"
echo ""
echo "Replace 'yourusername' with your actual GitHub username and 'font-group-system' with your repository name."
echo ""
