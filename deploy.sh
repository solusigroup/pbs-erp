#!/bin/bash
# ══════════════════════════════════════════════════════════════════════
# PBS-ERP Production Deployment Script
# PT Pinastika Bhakti Semesta
# ══════════════════════════════════════════════════════════════════════
# Usage:
#   Pertama kali deploy : bash deploy.sh install
#   Update dari GitHub  : bash deploy.sh update
#   Reset cache saja    : bash deploy.sh cache
#   Cek status          : bash deploy.sh status
# ══════════════════════════════════════════════════════════════════════

set -e

# ─── KONFIGURASI ────────────────────────────────────────────────────
CPANEL_USER="simpleak"
HOME_DIR="/home/${CPANEL_USER}"
DOMAIN="pbs.simpleakunting.biz.id"
APP_DIR="${HOME_DIR}/${DOMAIN}"
REPO_URL="https://github.com/solusigroup/pbs-erp.git"
BRANCH="main"

DB_NAME="${CPANEL_USER}_pbserp"
DB_USER="${CPANEL_USER}_pbserp"
# Password diisi saat menjalankan script
DB_PASS=""

APP_URL="https://${DOMAIN}"
# ────────────────────────────────────────────────────────────────────

# Warna output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color
BOLD='\033[1m'

print_header() {
    echo ""
    echo -e "${CYAN}══════════════════════════════════════════════════════════${NC}"
    echo -e "${BOLD}  🏭 PBS-ERP Deployment Tool${NC}"
    echo -e "${CYAN}  PT Pinastika Bhakti Semesta${NC}"
    echo -e "${CYAN}══════════════════════════════════════════════════════════${NC}"
    echo ""
}

print_step() {
    echo -e "\n${GREEN}▶ LANGKAH $1: $2${NC}"
    echo -e "${GREEN}──────────────────────────────────────────${NC}"
}

print_ok() {
    echo -e "  ${GREEN}✓${NC} $1"
}

print_warn() {
    echo -e "  ${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "  ${RED}✗${NC} $1"
}

print_info() {
    echo -e "  ${CYAN}ℹ${NC} $1"
}

# ─── FUNGSI: FRESH INSTALL ─────────────────────────────────────────
do_install() {
    print_header
    echo -e "${BOLD}Mode: FRESH INSTALL${NC}"
    echo -e "Target: ${APP_DIR}"
    echo ""

    # Tanya password database
    read -sp "Masukkan PASSWORD database (${DB_USER}): " DB_PASS
    echo ""
    if [ -z "$DB_PASS" ]; then
        print_error "Password database tidak boleh kosong!"
        exit 1
    fi

    # ── Step 1: Clone Repository ──
    print_step "1" "Clone repository dari GitHub"

    if [ -d "$APP_DIR" ]; then
        print_warn "Folder ${APP_DIR} sudah ada"
        read -p "  Hapus dan clone ulang? (y/n): " confirm
        if [ "$confirm" = "y" ] || [ "$confirm" = "Y" ]; then
            rm -rf "$APP_DIR"
            print_ok "Folder lama dihapus"
        else
            print_error "Dibatalkan. Gunakan 'bash deploy.sh update' untuk update."
            exit 1
        fi
    fi

    cd "$HOME_DIR"
    git clone "$REPO_URL" "$DOMAIN"
    print_ok "Repository berhasil di-clone"

    cd "$APP_DIR"

    # ── Step 2: Setup .env ──
    print_step "2" "Setup file .env"

    cat > .env << ENVEOF
APP_NAME="PBS-ERP"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=${APP_URL}
ASSET_URL=${APP_URL}

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASS}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
CACHE_STORE=file

MAIL_MAILER=smtp
MAIL_HOST=mail.simpleakunting.biz.id
MAIL_PORT=465
MAIL_USERNAME=admin@simpleakunting.biz.id
MAIL_PASSWORD=GANTI_PASSWORD_EMAIL
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="pt.pinastikabhaktisemesta@gmail.com"
MAIL_FROM_NAME="PT Pinastika Bhakti Semesta"

VITE_APP_NAME="\${APP_NAME}"
ENVEOF

    print_ok "File .env berhasil dibuat"

    # ── Step 3: Generate App Key ──
    print_step "3" "Generate application key"
    php artisan key:generate --force
    print_ok "APP_KEY berhasil di-generate"

    # ── Step 4: Folder Permissions ──
    print_step "4" "Set folder permissions"
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    print_ok "storage/ → 775"
    print_ok "bootstrap/cache/ → 775"

    # ── Step 5: Storage Symlink ──
    print_step "5" "Buat storage symlink"
    php artisan storage:link
    print_ok "public/storage → storage/app/public"

    # ── Step 6: Database ──
    print_step "6" "Setup database"

    # Test koneksi database
    if mysql -u "$DB_USER" -p"$DB_PASS" -e "USE ${DB_NAME};" 2>/dev/null; then
        print_ok "Koneksi database berhasil"
    else
        # Coba dengan localhost
        if mysql -u "$DB_USER" -p"$DB_PASS" -h localhost -e "USE ${DB_NAME};" 2>/dev/null; then
            print_warn "Menggunakan DB_HOST=localhost (bukan 127.0.0.1)"
            sed -i 's/DB_HOST=127.0.0.1/DB_HOST=localhost/' .env
            print_ok "DB_HOST diubah ke localhost di .env"
        else
            print_error "Tidak bisa konek ke database!"
            print_info "Pastikan database '${DB_NAME}' dan user '${DB_USER}' sudah dibuat di cPanel MySQL"
            print_info "Cek juga password database sudah benar"
            exit 1
        fi
    fi

    # Cek apakah ada SQL dump untuk diimport
    SQL_DUMP="${HOME_DIR}/pbs_erp_production.sql"
    if [ -f "$SQL_DUMP" ]; then
        print_info "Ditemukan SQL dump: ${SQL_DUMP}"
        read -p "  Import SQL dump? (y/n): " import_confirm
        if [ "$import_confirm" = "y" ] || [ "$import_confirm" = "Y" ]; then
            mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$SQL_DUMP"
            print_ok "SQL dump berhasil diimport"
        else
            print_info "Menjalankan migration sebagai gantinya..."
            php artisan migrate --force
            print_ok "Migration berhasil"
        fi
    else
        print_warn "SQL dump tidak ditemukan di ${SQL_DUMP}"
        print_info "Menjalankan migration..."
        php artisan migrate --force
        print_ok "Migration berhasil"

        print_info "Menjalankan seeder..."
        php artisan db:seed --force
        print_ok "Seeder berhasil"
    fi

    # ── Step 7: Cache Optimization ──
    print_step "7" "Optimasi cache production"
    do_cache_refresh

    # ── Selesai ──
    echo ""
    echo -e "${GREEN}══════════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}  🎉 DEPLOYMENT BERHASIL!${NC}"
    echo -e "${GREEN}══════════════════════════════════════════════════════════${NC}"
    echo ""
    echo -e "  🌐 URL:      ${BOLD}${APP_URL}${NC}"
    echo -e "  🔑 Login:    ${BOLD}${APP_URL}/login${NC}"
    echo -e "  📂 Path:     ${APP_DIR}"
    echo -e "  🗄️  Database: ${DB_NAME}"
    echo ""
    echo -e "  ${YELLOW}⚠ Jangan lupa:${NC}"
    echo -e "    1. Aktifkan SSL di cPanel → Let's Encrypt / AutoSSL"
    echo -e "    2. Upload pbs_erp_production.sql jika belum import data"
    echo -e "    3. Hapus public/storage_link.php dan public/optimize.php"
    echo ""
}

# ─── FUNGSI: UPDATE ─────────────────────────────────────────────────
do_update() {
    print_header
    echo -e "${BOLD}Mode: UPDATE dari GitHub${NC}"

    if [ ! -d "$APP_DIR" ]; then
        print_error "Folder ${APP_DIR} tidak ditemukan!"
        print_info "Jalankan 'bash deploy.sh install' untuk fresh install."
        exit 1
    fi

    cd "$APP_DIR"

    # ── Step 1: Maintenance Mode ──
    print_step "1" "Aktifkan maintenance mode"
    php artisan down --retry=30
    print_ok "Maintenance mode ON (visitors melihat halaman maintenance)"

    # ── Step 2: Pull dari GitHub ──
    print_step "2" "Pull update dari GitHub"
    git fetch origin "$BRANCH"
    git reset --hard "origin/${BRANCH}"
    print_ok "Code berhasil di-update ke commit terbaru"

    LATEST_COMMIT=$(git log --oneline -1)
    print_info "Commit: ${LATEST_COMMIT}"

    # ── Step 3: Permissions ──
    print_step "3" "Fix permissions"
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    print_ok "Permissions diperbaiki"

    # ── Step 4: Database Migration ──
    print_step "4" "Jalankan migration (jika ada)"
    php artisan migrate --force 2>&1 || {
        print_warn "Migration gagal atau tidak ada yang baru"
    }
    print_ok "Migration selesai"

    # ── Step 5: Cache ──
    print_step "5" "Refresh cache"
    do_cache_refresh

    # ── Step 6: Matikan Maintenance Mode ──
    print_step "6" "Matikan maintenance mode"
    php artisan up
    print_ok "Aplikasi kembali LIVE!"

    echo ""
    echo -e "${GREEN}══════════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}  ✅ UPDATE BERHASIL!${NC}"
    echo -e "${GREEN}══════════════════════════════════════════════════════════${NC}"
    echo -e "  Commit: ${LATEST_COMMIT}"
    echo -e "  URL:    ${APP_URL}"
    echo ""
}

# ─── FUNGSI: CACHE REFRESH ──────────────────────────────────────────
do_cache_refresh() {
    cd "$APP_DIR"

    # Clear dulu
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
    print_ok "Cache lama dibersihkan"

    # Rebuild
    php artisan config:cache
    print_ok "Config cached"

    php artisan route:cache
    print_ok "Routes cached"

    php artisan view:cache
    print_ok "Views cached"
}

do_cache() {
    print_header
    echo -e "${BOLD}Mode: CACHE REFRESH${NC}"
    print_step "1" "Refresh semua cache"
    do_cache_refresh
    echo ""
    print_ok "Semua cache berhasil di-refresh!"
    echo ""
}

# ─── FUNGSI: STATUS CHECK ──────────────────────────────────────────
do_status() {
    print_header
    echo -e "${BOLD}Mode: STATUS CHECK${NC}"
    echo ""

    # App directory
    if [ -d "$APP_DIR" ]; then
        print_ok "App directory: ${APP_DIR}"
    else
        print_error "App directory TIDAK ADA: ${APP_DIR}"
        return
    fi

    cd "$APP_DIR"

    # .env
    if [ -f ".env" ]; then
        print_ok ".env file exists"
    else
        print_error ".env file TIDAK ADA"
    fi

    # Git info
    if [ -d ".git" ]; then
        CURRENT_BRANCH=$(git branch --show-current 2>/dev/null)
        LATEST_COMMIT=$(git log --oneline -1 2>/dev/null)
        print_ok "Git branch: ${CURRENT_BRANCH}"
        print_ok "Last commit: ${LATEST_COMMIT}"
    else
        print_warn "Bukan git repository"
    fi

    # PHP version
    PHP_VER=$(php -v 2>/dev/null | head -1)
    print_ok "PHP: ${PHP_VER}"

    # Laravel version
    LARAVEL_VER=$(php artisan --version 2>/dev/null)
    print_ok "${LARAVEL_VER}"

    # Storage symlink
    if [ -L "public/storage" ]; then
        print_ok "Storage symlink: OK"
    else
        print_error "Storage symlink: TIDAK ADA (jalankan: php artisan storage:link)"
    fi

    # Folder permissions
    if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
        print_ok "Folder permissions: OK"
    else
        print_warn "Folder permissions mungkin perlu diperbaiki"
    fi

    # Database connection
    DB_CHECK=$(php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch(\Exception \$e) { echo 'FAIL: '.\$e->getMessage(); }" 2>/dev/null)
    if echo "$DB_CHECK" | grep -q "OK"; then
        print_ok "Database connection: OK"
    else
        print_error "Database connection: GAGAL"
        print_info "$DB_CHECK"
    fi

    # Disk usage
    DISK_USAGE=$(du -sh "$APP_DIR" 2>/dev/null | cut -f1)
    print_info "Disk usage: ${DISK_USAGE}"

    # Log file size
    if [ -f "storage/logs/laravel.log" ]; then
        LOG_SIZE=$(du -sh "storage/logs/laravel.log" 2>/dev/null | cut -f1)
        print_info "Log file size: ${LOG_SIZE}"
    fi

    echo ""
}

# ─── FUNGSI: ROLLBACK ──────────────────────────────────────────────
do_rollback() {
    print_header
    echo -e "${BOLD}Mode: ROLLBACK${NC}"

    if [ ! -d "$APP_DIR" ]; then
        print_error "App directory tidak ditemukan"
        exit 1
    fi

    cd "$APP_DIR"

    echo "5 commit terakhir:"
    git log --oneline -5
    echo ""
    read -p "Masukkan commit hash untuk rollback: " COMMIT_HASH

    if [ -z "$COMMIT_HASH" ]; then
        print_error "Commit hash tidak boleh kosong"
        exit 1
    fi

    php artisan down --retry=30
    print_ok "Maintenance mode ON"

    git reset --hard "$COMMIT_HASH"
    print_ok "Rollback ke: $(git log --oneline -1)"

    chmod -R 775 storage bootstrap/cache
    do_cache_refresh

    php artisan up
    print_ok "Aplikasi kembali LIVE"

    echo ""
    echo -e "${GREEN}✅ Rollback berhasil!${NC}"
    echo ""
}

# ─── MAIN ───────────────────────────────────────────────────────────
case "${1}" in
    install)
        do_install
        ;;
    update)
        do_update
        ;;
    cache)
        do_cache
        ;;
    status)
        do_status
        ;;
    rollback)
        do_rollback
        ;;
    *)
        print_header
        echo -e "  ${BOLD}Usage:${NC}"
        echo ""
        echo -e "    ${CYAN}bash deploy.sh install${NC}   Fresh install (clone + setup)"
        echo -e "    ${CYAN}bash deploy.sh update${NC}    Pull update dari GitHub"
        echo -e "    ${CYAN}bash deploy.sh cache${NC}     Refresh semua cache"
        echo -e "    ${CYAN}bash deploy.sh status${NC}    Cek status aplikasi"
        echo -e "    ${CYAN}bash deploy.sh rollback${NC}  Rollback ke commit sebelumnya"
        echo ""
        ;;
esac
