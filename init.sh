#!/bin/bash

# ==========================================
# Axiom POS Framework - Init Script
# ==========================================
# Script ini akan menjalankan proses boilerplating
# untuk mengubah repository ini menjadi fresh install.

echo "🚀 Memulai Setup Axiom POS Starter Kit..."

# 1. Install Dependencies
echo "📦 Menginstall dependency (Tunggu sebentar)..."
composer install --no-interaction
npm install

# 2. Setup Environment
if [ ! -f .env ]; then
    echo "📄 Menyalin .env.example menjadi .env..."
    cp .env.example .env
fi

echo "🔑 Generate APP_KEY..."
php artisan key:generate

# 3. Database Prompting
read -p "❓ Masukkan nama database yang ingin digunakan (kosongkan jika pakai axiom_pos): " db_name
db_name=${db_name:-axiom_pos}

read -p "❓ Masukkan database username (kosongkan jika 'root'): " db_user
db_user=${db_user:-root}

read -sp "❓ Masukkan database password (kosongkan jika tidak ada): " db_pass
echo "" # newline

# Apply ke env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$db_pass/" .env

echo "🗄️ Menyiapkan Database dan melakukan Seeding..."
php artisan migrate:fresh --seed

# 4. Cleansing git history (Optional for client projects)
read -p "❓ Apakah Anda ingin mereset history Git (menghapus .git dan membuat repositori baru)? [y/N]: " reset_git
if [[ "$reset_git" =~ ^[Yy]$ ]]; then
    echo "🗑️ Menghapus folder .git lama..."
    rm -rf .git
    echo "✨ Inisialisasi Git baru..."
    git init
    git add .
    git commit -m "Initial commit from AxiomPOS Boilerplate"
fi

echo "✅ Selesai! Axiom POS berhasil disiapkan."
echo "👉 Jalankan 'php artisan serve' dan 'npm run dev' di terminal yang berbeda."
