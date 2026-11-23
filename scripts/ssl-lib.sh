#!/bin/bash
# SSL library for sitematrix.
# Generates per-site TLS certificates and configures nginx to use them.
# Falls back from mkcert to openssl when mkcert is not installed.
# Design: additive (no existing certs removed), idempotent, --force for overwrite.

set -uo pipefail

# --- Configuration helpers ---
# get_site_port lives in scripts/helpers.sh (handles nginx_ + wp_ compose blocks
# and live-container fallback). ssl-lib previously redefined an nginx-only copy,
# which shadowed the general one and broke Apache-stack sites.

# Compute the HTTPS port paired with a given HTTP port.
# Rule: https_port = http_port + 1000
compute_https_port() {
    local http_port="${1:-0}"
    if [[ "$http_port" =~ ^[0-9]+$ ]] && [[ "$http_port" -ge 1 ]] 2>/dev/null; then
        echo $((http_port + 1000))
    else
        echo ""
    fi
}

# --- Certificate management ---

# generate_site_cert <site> [--force]
# Generates a TLS certificate for <site>.
# If mkcert is installed, uses mkcert to create a trusted certificate.
# Otherwise falls back to openssl self-signed certificate.
# Idempotent: overwrites existing cert only when --force is given.
generate_site_cert() {
    local site=""
    local force=false

    # Parse args: the site name may come before or after --force.
    while [[ $# -gt 0 ]]; do
        case "$1" in
            --force)
                force=true
                shift
                ;;
            *)
                if [[ -z "$site" ]]; then
                    site="$1"
                fi
                shift
                ;;
        esac
    done

    # If cert already exists and not --force, exit 0 (idempotent)
    if [[ "$force" != "true" && -f "$PROJECT_ROOT/ssl-certs/${site}/server.crt" ]]; then
        log_info "Certificate already exists for '$site'. Use --force to regenerate."
        return 0
    fi

    local cert_dir="$PROJECT_ROOT/ssl-certs/${site}"
    local server_cert="$cert_dir/server.crt"
    local server_key="$cert_dir/server.key"

    # Create cert directory if it doesn't exist
    mkdir -p "$cert_dir"

    if command -v mkcert >/dev/null 2>&1; then
        # Include the LAN IP: sites are browsed as https://<lan-ip>:<https_port>.
        local lan_ip=""
        lan_ip=$(get_lan_ip 2>/dev/null || true)
        local names=("localhost" "127.0.0.1" "${site}.test" "*.${site}.test")
        if [[ -n "$lan_ip" && "$lan_ip" != "localhost" ]]; then
            names+=("$lan_ip")
        fi

        # Use mkcert to generate a trusted certificate
        mkcert -cert-file "$server_cert" -key-file "$server_key" "${names[@]}" 2>/dev/null || \
            # Fallback if mkcert fails for any reason
            generate_openssl_cert "${site}"
    else
        # Use openssl fallback
        generate_openssl_cert "${site}"
    fi

    # Ensure restrictive permissions on the key file (convention T-05-02)
    chmod 600 "$server_key" 2>/dev/null || true

    log_info "Generated TLS certificate for '$site' at '$server_cert'"
    return 0
}

# generate_openssl_cert <site>
# Generates a self-signed certificate using openssl.
generate_openssl_cert() {
    local site="${1:-}"
    local cert_dir="$PROJECT_ROOT/ssl-certs/${site}"
    local server_cert="$cert_dir/server.crt"
    local server_key="$cert_dir/server.key"

    # SAN must cover the LAN IP: sites are browsed as https://<lan-ip>:<https_port>
    # (mkcert is optional; the openssl fallback is the common path).
    local lan_ip=""
    lan_ip=$(get_lan_ip 2>/dev/null || true)
    local san="DNS:localhost,DNS:${site}.test,IP:127.0.0.1,IP:::1"
    if [[ -n "$lan_ip" && "$lan_ip" != "localhost" ]]; then
        san="${san},IP:${lan_ip}"
    fi

    # Generate RSA 2048 key and self-signed cert with SAN
    openssl req -x509 \
        -newkey rsa:2048 \
        -nodes \
        -keyout "$server_key" \
        -out "$server_cert" \
        -subj "/C=US/ST=Development/L=Local/O=WordPress Dev/CN=${site}" \
        -addext "subjectAltName=${san}" 2>/dev/null || \
        return 1

    log_info "Generated openssl self-signed certificate for '$site'"
}

# site_cert_exists <site>
# Returns 0 if a certificate exists for <site>, 1 otherwise.
site_cert_exists() {
    local site="${1:-}"
    [[ -f "$PROJECT_ROOT/ssl-certs/${site}/server.crt" ]]
}

# site_cert_fingerprint <site>
# Returns the SHA256 fingerprint of the certificate, or empty string.
site_cert_fingerprint() {
    local site="${1:-}"
    local cert="$PROJECT_ROOT/ssl-certs/${site}/server.crt"
    if [[ -f "$cert" ]]; then
        # LibreSSL prefixes "sha256 Fingerprint=", OpenSSL "SHA256 Fingerprint=".
        openssl x509 -in "$cert" -fingerprint -noout -sha256 2>/dev/null | sed 's/^[^=]*=//' || true
    fi
}

# --- Domain management ---

# domain <site> [hostname] [--yes]
# Manages /etc/hosts entries for pretty hostnames (<site>.test).
# Without --yes, prompts for confirmation in interactive mode and exits
# with an error if not running in a terminal.
# With --yes, skips the confirmation prompt.
domain() {
    local site="${1:-}"
    local hostname="${2:-${site}.test}"
    local force=false

    # Parse --yes flag
    while [[ $# -gt 0 ]]; do
        case "$1" in
            --yes)
                force=true
                shift
                ;;
            *)
                break
                ;;
        esac
    done

    # Validate site name
    if [[ -z "$site" ]]; then
        log_error "Site name is required."
        return 1
    fi

    # Check if already in /etc/hosts
    if grep -qF -- "${hostname}" /etc/hosts 2>/dev/null; then
        log_info "Hostname '${hostname}' is already in /etc/hosts."
        return 0
    fi

    # Ask for confirmation unless --yes is given
    if [[ "$force" != "true" && -t 0 ]]; then
        read -r -p "Add '${hostname}' to /etc/hosts pointing to your LAN IP? (y/N) " response
        response=$(echo "$response" | tr '[:upper:]' '[:lower:]')
        if [[ "$response" != "y" && "$response" != "yes" ]]; then
            log_info "Skipping /etc/hosts modification."
            return 0
        fi
    fi

    # Get LAN IP and add to /etc/hosts
    local lan_ip
    lan_ip=$(get_lan_ip)
    if [[ -n "$lan_ip" ]]; then
        # Append to /etc/hosts using sudo; graceful failure if sudo not available
        if sudo sh -c "echo '${lan_ip} ${hostname}' >> /etc/hosts" 2>/dev/null; then
            log_info "Added '${hostname}' (${lan_ip}) to /etc/hosts."
        else
            # Fallback: instruct user to add manually
            log_warning "Could not modify /etc/hosts via sudo."
            log_info "Please add manually: ${lan_ip} ${hostname}"
            # Still exit 0 so the workflow isn't broken
            return 0
        fi
    else
        log_warning "Could not determine LAN IP."
        log_info "Please add '${hostname}' to /etc/hosts manually."
    fi

    return 0
}

# Remove a hostname entry from /etc/hosts
remove_hostname_from_etc() {
    local hostname="${1:-}"
    if sudo sed -i "/${hostname}/d" /etc/hosts 2>/dev/null; then
        log_info "Removed '${hostname}' from /etc/hosts."
    else
        log_warning "Could not remove '${hostname}' from /etc/hosts via sudo."
        log_info "Please remove manually: sudo sed -i '/${hostname}/d' /etc/hosts"
    fi
}

# --- nginx / compose wiring ------------------------------------------------
# matrix ssl <site> historically only minted a certificate: the nginx conf kept
# a single `listen 80` block and compose published no 443 port, so https://
# never answered. These helpers close that gap.

MATRIX_TLS_BEGIN='# BEGIN Matrix TLS (managed by ./matrix ssl — do not edit by hand)'
MATRIX_TLS_END='# END Matrix TLS'

# ssl_nginx_enable <site> — append a TLS server block to config/nginx/<site>.conf
# The block is derived from the existing HTTP block so root, fastcgi_pass and
# location rules stay identical; only the listener and cert directives differ.
ssl_nginx_enable() {
    local site="${1:-}"
    local conf="$PROJECT_ROOT/config/nginx/${site}.conf"

    if [[ ! -f "$conf" ]]; then
        log_error "No nginx config for '$site' at $conf (apache-stack sites have none)."
        return 1
    fi

    if grep -qF "$MATRIX_TLS_BEGIN" "$conf"; then
        log_info "TLS server block already present in $conf"
        return 0
    fi

    if ! grep -qE '^[[:space:]]*listen[[:space:]]+80;' "$conf"; then
        log_error "No 'listen 80' found in $conf — cannot derive a TLS block."
        return 1
    fi

    local cert="/etc/nginx/ssl/server.crt"
    local key="/etc/nginx/ssl/server.key"

    # Build the block in a temp file: reading $conf with awk while appending to
    # $conf makes awk consume its own output.
    local tmp
    tmp=$(mktemp)
    {
        printf '\n%s\n' "$MATRIX_TLS_BEGIN"
        awk -v cert="$cert" -v key="$key" '
            !done && /^[[:space:]]*listen[[:space:]]+80;/ {
                print "    listen 443 ssl;"
                print "    ssl_certificate " cert ";"
                print "    ssl_certificate_key " key ";"
                print "    ssl_protocols TLSv1.2 TLSv1.3;"
                done = 1
                next
            }
            { print }
        ' "$conf"
        printf '%s\n' "$MATRIX_TLS_END"
    } > "$tmp"

    cat "$tmp" >> "$conf"
    rm -f "$tmp"

    log_info "Added TLS server block to $conf"
    return 0
}

# ssl_nginx_disable <site> — drop the marker block from config/nginx/<site>.conf
ssl_nginx_disable() {
    local site="${1:-}"
    local conf="$PROJECT_ROOT/config/nginx/${site}.conf"

    [[ -f "$conf" ]] || return 0
    grep -qF "$MATRIX_TLS_BEGIN" "$conf" || return 0

    local tmp
    tmp=$(mktemp)
    awk -v begin="$MATRIX_TLS_BEGIN" -v end="$MATRIX_TLS_END" '
        $0 == begin { skip = 1; next }
        $0 == end   { skip = 0; next }
        !skip       { print }
    ' "$conf" > "$tmp"
    cat "$tmp" > "$conf"
    rm -f "$tmp"

    log_info "Removed TLS server block from $conf"
    return 0
}

# ssl_compose_enable <site> <https_port> — publish 443 and mount the cert pair
# on the nginx_<site> service. Idempotent; validates through compose_atomic_write
# when available (standalone sourcing in tests does not define it).
ssl_compose_enable() {
    local site="${1:-}"
    local https_port="${2:-}"

    if [[ -z "$https_port" ]]; then
        log_error "ssl_compose_enable: missing https port"
        return 1
    fi

    if [[ ! -f "${COMPOSE_FILE:-}" ]]; then
        log_error "docker-compose.yml not found at ${COMPOSE_FILE:-<unset>}"
        return 1
    fi

    local port_line="      - \"${https_port}:443\""
    local volume_line="      - ./ssl-certs/${site}:/etc/nginx/ssl:ro"

    if ! grep -qE "^  nginx_${site}:[[:space:]]*$" "$COMPOSE_FILE"; then
        log_error "No nginx_${site} service in docker-compose.yml (apache-stack site?)."
        return 1
    fi

    if grep -qF "\"${https_port}:443\"" "$COMPOSE_FILE" && \
       grep -qF "./ssl-certs/${site}:/etc/nginx/ssl" "$COMPOSE_FILE"; then
        log_info "nginx_${site} already publishes ${https_port}:443"
        return 0
    fi

    local tmp
    tmp=$(mktemp)
    awk -v site="$site" -v port_line="$port_line" -v volume_line="$volume_line" '
        BEGIN { inblk = 0; sect = "" }

        $0 ~ ("^  nginx_" site ":[[:space:]]*$") { inblk = 1; sect = ""; print; next }

        inblk && /^  [^[:space:]]/ { inblk = 0; sect = "" }

        inblk && sect == "" && /^    ports:[[:space:]]*$/   { sect = "ports";   print; next }
        inblk && sect == "" && /^    volumes:[[:space:]]*$/ { sect = "volumes"; print; next }

        inblk && sect == "ports"   && /^      - / { print; next }
        inblk && sect == "volumes" && /^      - / { print; next }

        inblk && sect == "ports"   { print port_line;   sect = "" }
        inblk && sect == "volumes" { print volume_line; sect = "" }

        { print }
    ' "$COMPOSE_FILE" > "$tmp"

    if declare -F compose_atomic_write >/dev/null 2>&1; then
        compose_atomic_write "$tmp" "$COMPOSE_FILE"
        local rc=$?
        rm -f "$tmp"
        [[ $rc -eq 0 ]] || return $rc
    else
        cat "$tmp" > "$COMPOSE_FILE"
        rm -f "$tmp"
    fi

    log_info "nginx_${site} publishes ${https_port}:443 with certs from ./ssl-certs/${site}"
    return 0
}

# ssl_compose_disable <site> <https_port> — remove the port mapping + cert mount
ssl_compose_disable() {
    local site="${1:-}"
    local https_port="${2:-}"

    [[ -f "${COMPOSE_FILE:-}" ]] || return 0

    local tmp
    tmp=$(mktemp)
    awk -v https_port="$https_port" -v site="$site" '
        $0 ~ ("^      - \"" https_port ":443\"[[:space:]]*$") { next }
        index($0, "./ssl-certs/" site ":/etc/nginx/ssl") { next }
        { print }
    ' "$COMPOSE_FILE" > "$tmp"

    if declare -F compose_atomic_write >/dev/null 2>&1; then
        compose_atomic_write "$tmp" "$COMPOSE_FILE"
        local rc=$?
        rm -f "$tmp"
        [[ $rc -eq 0 ]] || return $rc
    else
        cat "$tmp" > "$COMPOSE_FILE"
        rm -f "$tmp"
    fi

    log_info "Removed ${https_port}:443 and cert mount from nginx_${site}"
    return 0
}

# ssl_enable <site> [--force] — cert + nginx block + compose wiring
ssl_enable() {
    local site="${1:-}"
    local force="${2:-false}"

    if [[ "$force" == "true" ]]; then
        generate_site_cert "$site" --force || return 1
    else
        generate_site_cert "$site" || return 1
    fi

    local http_port https_port
    http_port=$(get_site_port "$site")
    https_port=$(compute_https_port "$http_port")
    if [[ -z "$https_port" ]]; then
        log_error "Could not compute the HTTPS port for '$site'."
        return 1
    fi

    ssl_nginx_enable "$site" || return 1
    ssl_compose_enable "$site" "$https_port" || return 1

    log_info "TLS enabled for '$site': HTTP ${http_port}, HTTPS ${https_port}"
    return 0
}

# --- matrix command wrappers ---

# matrix ssl <site> [--force] [--revoke] [--json]
# Generates/removes TLS certificate and configures nginx for the site.
# --force: regenerate existing certificate
# --revoke: remove certificate and nginx ssl configuration
# --json: emit JSON output
cmd_ssl() {
    local site="${1:-}"
    local force=false
    local revoke=false
    # main() strips --json before dispatch, so honour the global flag too.
    local json="${JSON_OUTPUT:-false}"

    # Parse flags
    while [[ $# -gt 0 ]]; do
        case "$1" in
            --force)
                force=true
                shift
                ;;
            --revoke)
                revoke=true
                shift
                ;;
            --json)
                json=true
                shift
                ;;
            *)
                site="${1:-}"
                shift
                ;;
        esac
    done

    # Validate site name
    if [[ -z "$site" ]]; then
        error_json "SSL_SITE_NOT_FOUND" "Site name is required."
        return 1
    fi

    if [[ ! -d "$PROJECT_ROOT/wp_${site}" ]]; then
        error_json "SSL_SITE_NOT_FOUND" "No site named '${site}'."
        return 1
    fi

    # Remove the cert + nginx ssl config when --revoke given.
    local cert_dir="$PROJECT_ROOT/ssl-certs/${site}"
    local server_cert="$cert_dir/server.crt"
    local server_key="$cert_dir/server.key"

    if [[ "$revoke" == "true" ]]; then
        local http_port https_port
        http_port=$(get_site_port "$site")
        https_port=$(compute_https_port "$http_port")
        ssl_nginx_disable "$site"
        [[ -n "$https_port" ]] && ssl_compose_disable "$site" "$https_port"
        rm -f "$server_cert" "$server_key"
        # Remove nginx ssl block from the per-site conf (repair will regenerate)
        log_info "Revoked TLS certificate for '$site'."
        if [[ "$json" == "true" ]]; then
            output_json "{\"success\":true,\"site\":\"${site}\",\"revoked\":true}"
        fi
        return 0
    fi

    # Generate the certificate and wire nginx + compose
    ssl_enable "$site" "$force" || return 1

    # Compute the HTTPS port
    local http_port
    http_port=$(get_site_port "$site")
    local https_port
    https_port=$(compute_https_port "$http_port")
    if [[ -z "$https_port" ]]; then
        error_json "SSL_PORT_COMPUTE_FAILED" "Could not compute HTTPS port for '$site'."
        return 1
    fi

    # Ensure nginx config has the ssl block — compose-lib repair regenerates it.
    log_info "TLS setup for '$site': HTTP port ${http_port}, HTTPS port ${https_port}."

    if [[ "$json" == "true" ]]; then
        local cert_source="openssl"
        command -v mkcert >/dev/null 2>&1 && cert_source="mkcert"
        local fingerprint lan_ip
        fingerprint=$(site_cert_fingerprint "${site}")
        lan_ip=$(get_lan_ip 2>/dev/null || echo "localhost")
        output_json "{\"success\":true,\"site\":\"${site}\",\"http_port\":${http_port},\"https_port\":${https_port},\"https_url\":\"https://${lan_ip}:${https_port}\",\"cert_source\":\"${cert_source}\",\"fingerprint\":\"${fingerprint}\"}"
    fi

    return 0
}

# matrix domain <site> [hostname] [--yes]
cmd_domain() {
    local site="${1:-}"
    local hostname="${2:-${site}.test}"
    local force=false
    local json="${JSON_OUTPUT:-false}"

    # Parse --yes flag
    while [[ $# -gt 0 ]]; do
        case "$1" in
            --yes)
                force=true
                shift
                ;;
            *)
                break
                ;;
        esac
    done

    # Validate site name
    if [[ -z "$site" ]]; then
        error_json "SSL_SITE_NOT_FOUND" "Site name is required."
        return 1
    fi

    # Run domain management (adds /etc/hosts entry)
    domain "$site" "$hostname" --yes

    if [[ "$json" == "true" ]]; then
        output_json "{\"success\":true,\"site\":\"${site}\",\"hostname\":\"${hostname}\"}"
    fi

    return 0
}