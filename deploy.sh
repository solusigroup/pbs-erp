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
#   Rollback            : bash deploy.sh rollback
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
DB_PASS=""

APP_URL="https://${DOMAIN}"
# ────────────────────────────────────────────────────────────────────

# Warna output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'
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

print_ok()    { echo -e "  ${GREEN}✓${NC} $1"; }
print_warn()  { echo -e "  ${YELLOW}⚠${NC} $1"; }
print_error() { echo -e "  ${RED}✗${NC} $1"; }
print_info()  { echo -e "  ${CYAN}ℹ${NC} $1"; }

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

    # ── Step 1: Clone ke folder subdomain yang sudah ada ──
    print_step "1" "Clone repository ke folder subdomain"

    if [ -d "${APP_DIR}/.git" ]; then
        print_warn "Folder sudah berisi git repo"
        read -p "  Reset dan clone ulang? (y/n): " confirm
        if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
            print_info "Dibatalkan. Gunakan 'bash deploy.sh update' untuk update."
            exit 0
        fi
    fi

    # Folder subdomain sudah dibuat oleh cPanel, gunakan git init approach
    cd "${APP_DIR}"

    # Bersihkan isi folder (kecuali .well-known untuk SSL)
    find . -mindepth 1 -maxdepth 1 ! -name '.well-known' -exec rm -rf {} + 2>/dev/null || true
    print_ok "Folder subdomain dibersihkan"

    # Init git dan pull dari remote
    git init
    git remote add origin "${REPO_URL}" 2>/dev/null || git remote set-url origin "${REPO_URL}"
    git fetch origin "${BRANCH}"
    git checkout -f "${BRANCH}"
    git branch --set-upstream-to="origin/${BRANCH}" "${BRANCH}" 2>/dev/null || true
    print_ok "Repository berhasil di-clone ke ${APP_DIR}"

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
    if [ -L "public/storage" ]; then
        print_ok "Symlink sudah ada"
    else
        php artisan storage:link
        print_ok "public/storage → storage/app/public"
    fi

    # ── Step 6: Database ──
    print_step "6" "Setup database"

    # Test koneksi database
    DB_HOST_USED="127.0.0.1"
    if mysql -u "$DB_USER" -p"$DB_PASS" -e "USE ${DB_NAME};" 2>/dev/null; then
        print_ok "Koneksi database berhasil (127.0.0.1)"
    elif mysql -u "$DB_USER" -p"$DB_PASS" -h localhost -e "USE ${DB_NAME};" 2>/dev/null; then
        DB_HOST_USED="localhost"
        sed -i 's/DB_HOST=127.0.0.1/DB_HOST=localhost/' .env
        print_ok "Koneksi database berhasil (localhost)"
    else
        print_error "Tidak bisa konek ke database!"
        print_info "Pastikan database '${DB_NAME}' dan user '${DB_USER}' sudah dibuat di cPanel"
        print_info "Lanjutkan setup manual setelah fix database"
        print_info "  1. Edit .env → sesuaikan DB_PASSWORD"
        print_info "  2. bash deploy.sh cache"
        exit 1
    fi

    # Cek SQL dump
    SQL_DUMP=""
    if [ -f "${HOME_DIR}/pbs_erp_production.sql" ]; then
        SQL_DUMP="${HOME_DIR}/pbs_erp_production.sql"
    elif [ -f "${APP_DIR}/pbs_erp_production.sql" ]; then
        SQL_DUMP="${APP_DIR}/pbs_erp_production.sql"
    fi

    if [ -n "$SQL_DUMP" ]; then
        print_info "Ditemukan SQL dump: ${SQL_DUMP}"
        read -p "  Import SQL dump? (y/n): " import_confirm
        if [ "$import_confirm" = "y" ] || [ "$import_confirm" = "Y" ]; then
            mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST_USED" "$DB_NAME" < "$SQL_DUMP"
            print_ok "SQL dump berhasil diimport"
        else
            print_info "Menjalankan migration..."
            php artisan migrate --force
            php artisan db:seed --force
            print_ok "Migration + Seeder berhasil"
        fi
    else
        print_warn "SQL dump tidak ditemukan"
        print_info "Upload pbs_erp_production.sql ke ${HOME_DIR}/ lalu import via:"
        print_info "  mysql -u ${DB_USER} -p ${DB_NAME} < ${HOME_DIR}/pbs_erp_production.sql"
        print_info "Atau jalankan migration untuk fresh database..."
        read -p "  Jalankan migration sekarang? (y/n): " mig_confirm
        if [ "$mig_confirm" = "y" ] || [ "$mig_confirm" = "Y" ]; then
            php artisan migrate --force
            php artisan db:seed --force
            print_ok "Migration + Seeder berhasil"
        fi
    fi

    # ── Step 7: Cache Optimization ──
    print_step "7" "Optimasi cache production"
    do_cache_refresh

    # ── Step 8: Cleanup file helper ──
    print_step "8" "Cleanup file deployment helper"
    rm -f public/optimize.php
    rm -f public/storage_link.php
    print_ok "File helper dihapus (tidak diperlukan karena ada Terminal)"

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
    echo -e "  ${YELLOW}Pastikan SSL sudah aktif di cPanel → Let's Encrypt / AutoSSL${NC}"
    echo ""
}

# ─── FUNGSI: UPDATE ─────────────────────────────────────────────────
do_update() {
    print_header
    echo -e "${BOLD}Mode: UPDATE dari GitHub${NC}"

    if [ ! -d "${APP_DIR}/.git" ]; then
        print_error "Folder ${APP_DIR} bukan git repository!"
        print_info "Jalankan 'bash deploy.sh install' untuk fresh install."
        exit 1
    fi

    cd "$APP_DIR"

    # ── Step 1: Maintenance Mode ──
    print_step "1" "Aktifkan maintenance mode"
    php artisan down --retry=30
    print_ok "Maintenance mode ON"

    # ── Step 2: Pull dari GitHub ──
    print_step "2" "Pull update dari GitHub"

    # Simpan .env agar tidak tertimpa
    cp .env .env.backup 2>/dev/null || true

    git fetch origin "$BRANCH"
    git reset --hard "origin/${BRANCH}"

    # Restore .env
    cp .env.backup .env 2>/dev/null || true

    LATEST_COMMIT=$(git log --oneline -1)
    print_ok "Code di-update: ${LATEST_COMMIT}"

    # ── Step 3: Permissions ──
    print_step "3" "Fix permissions"
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    print_ok "Permissions OK"

    # ── Step 4: Migration ──
    print_step "4" "Jalankan migration (jika ada)"
    php artisan migrate --force 2>&1 && print_ok "Migration selesai" || print_warn "Tidak ada migration baru"

    # ── Step 5: Cache ──
    print_step "5" "Refresh cache"
    do_cache_refresh

    # ── Step 6: Live ──
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

    php artisan config:clear  2>/dev/null || true
    php artisan route:clear   2>/dev/null || true
    php artisan view:clear    2>/dev/null || true
    php artisan cache:clear   2>/dev/null || true
    print_ok "Cache lama dibersihkan"

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
    print_ok "Selesai!"
    echo ""
}

# ─── FUNGSI: STATUS CHECK ──────────────────────────────────────────
do_status() {
    print_header
    echo -e "${BOLD}Mode: STATUS CHECK${NC}"
    echo ""

    if [ ! -d "$APP_DIR" ]; then
        print_error "App directory TIDAK ADA: ${APP_DIR}"
        return
    fi

    cd "$APP_DIR"

    # .env
    [ -f ".env" ] && print_ok ".env exists" || print_error ".env TIDAK ADA"

    # Git
    if [ -d ".git" ]; then
        print_ok "Git branch: $(git branch --show-current 2>/dev/null)"
        print_ok "Last commit: $(git log --oneline -1 2>/dev/null)"

        # Cek apakah ada update di remote
        git fetch origin "$BRANCH" --quiet 2>/dev/null
        LOCAL=$(git rev-parse HEAD 2>/dev/null)
        REMOTE=$(git rev-parse "origin/${BRANCH}" 2>/dev/null)
        if [ "$LOCAL" = "$REMOTE" ]; then
            print_ok "Up to date dengan GitHub"
        else
            print_warn "Ada update baru di GitHub! Jalankan: bash deploy.sh update"
        fi
    else
        print_warn "Bukan git repository"
    fi

    # PHP & Laravel
    print_ok "PHP: $(php -v 2>/dev/null | head -1)"
    print_ok "$(php artisan --version 2>/dev/null)"

    # Storage symlink
    [ -L "public/storage" ] && print_ok "Storage symlink: OK" || print_error "Storage symlink: MISSING"

    # Permissions
    [ -w "storage" ] && [ -w "bootstrap/cache" ] && print_ok "Permissions: OK" || print_warn "Permissions: perlu diperbaiki"

    # Database
    DB_CHECK=$(php artisan tinker --execute="try{DB::connection()->getPdo();echo 'OK';}catch(\Exception \$e){echo 'FAIL';}" 2>/dev/null | tail -1)
    [ "$DB_CHECK" = "OK" ] && print_ok "Database: Connected" || print_error "Database: GAGAL"

    # Disk
    print_info "Disk usage: $(du -sh "$APP_DIR" 2>/dev/null | cut -f1)"
    [ -f "storage/logs/laravel.log" ] && print_info "Log size: $(du -sh storage/logs/laravel.log 2>/dev/null | cut -f1)"

    echo ""
}

# ─── FUNGSI: ROLLBACK ──────────────────────────────────────────────
do_rollback() {
    print_header
    echo -e "${BOLD}Mode: ROLLBACK${NC}"

    [ ! -d "${APP_DIR}/.git" ] && { print_error "Bukan git repo"; exit 1; }

    cd "$APP_DIR"

    echo ""
    echo "5 commit terakhir:"
    git log --oneline -5
    echo ""
    read -p "Masukkan commit hash untuk rollback: " COMMIT_HASH
    [ -z "$COMMIT_HASH" ] && { print_error "Commit hash kosong"; exit 1; }

    php artisan down --retry=30
    git reset --hard "$COMMIT_HASH"
    chmod -R 775 storage bootstrap/cache
    do_cache_refresh
    php artisan up

    echo ""
    echo -e "${GREEN}✅ Rollback ke: $(git log --oneline -1)${NC}"
    echo ""
}

# ─── FUNGSI: LOG ────────────────────────────────────────────────────
do_log() {
    print_header
    echo -e "${BOLD}Mode: LARAVEL LOG (last 50 lines)${NC}"
    echo ""

    if [ -f "${APP_DIR}/storage/logs/laravel.log" ]; then
        tail -50 "${APP_DIR}/storage/logs/laravel.log"
    else
        print_info "Log file belum ada"
    fi
}

# ─── MAIN ───────────────────────────────────────────────────────────
case "${1}" in
    install)  do_install  ;;
    update)   do_update   ;;
    cache)    do_cache    ;;
    status)   do_status   ;;
    rollback) do_rollback ;;
    log)      do_log      ;;
    *)
        print_header
        echo -e "  ${BOLD}Usage:${NC}"
        echo ""
        echo -e "    ${CYAN}bash deploy.sh install${NC}    Fresh install (clone + setup)"
        echo -e "    ${CYAN}bash deploy.sh update${NC}     Pull update dari GitHub"
        echo -e "    ${CYAN}bash deploy.sh cache${NC}      Refresh semua cache"
        echo -e "    ${CYAN}bash deploy.sh status${NC}     Cek status aplikasi"
        echo -e "    ${CYAN}bash deploy.sh rollback${NC}   Rollback ke commit sebelumnya"
        echo -e "    ${CYAN}bash deploy.sh log${NC}        Lihat 50 baris terakhir error log"
        echo ""
        ;;
esac
