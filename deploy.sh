#!/bin/bash

# Configuration
FTP_USER="test-absesnsi"
FTP_HOST="ftp.pandanteknik.com"
URL="ftp://${FTP_HOST}"

echo "============================================"
echo "   Absensi GPS Barcode - FTP Deployment"
echo "============================================"
echo "Target: $URL"
echo "User:   $FTP_USER"
echo ""

# Check for git-ftp
if ! command -v git-ftp &> /dev/null; then
    echo "Error: git-ftp is not installed."
    echo "Please install it first: brew install git-ftp"
    exit 1
fi

# Securely read password
echo -n "Enter FTP Password: "
read -s FTP_PASSWORD
echo ""
echo ""

echo "Select deployment mode:"
echo "1) Update (git ftp push) - Fast upload of changed files only"
echo "2) First Time (git ftp init) - Uploads EVERYTHING (Use only for fresh server)"
echo "3) Catchup (git ftp catchup) - Server has files, just mark as done (No upload)"
read -p "Choose [1]: " MODE
MODE=${MODE:-1}

echo "--------------------------------------------"

if [ "$MODE" == "2" ]; then
    echo "Starting INITIAL upload... this may take a while."
    git ftp init --user "$FTP_USER" --passwd "$FTP_PASSWORD" "$URL"
elif [ "$MODE" == "3" ]; then
    echo "Marking server as up-to-date (Catchup)..."
    git ftp catchup --user "$FTP_USER" --passwd "$FTP_PASSWORD" "$URL"
else
    echo "Starting UPDATE upload..."
    git ftp push --user "$FTP_USER" --passwd "$FTP_PASSWORD" "$URL"
fi

echo "--------------------------------------------"
echo "Done!"
echo "IMPORTANT: If you see "ReflectionException" or "Class not exist" errors:"
echo "Please DELETE all files inside bootstrap/cache/ on your server manually via File Manager."
