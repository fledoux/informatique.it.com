# 🚀 Production Deployment Guide - SAML SSO Configuration

## Option 1: Environment Variable-based Deployment (Recommended)

### Prerequisites
- SSH access to production server
- Git remote configured
- `.env` certificates already base64-encoded (✅ Done)

### Quick Deploy

```bash
# Make script executable (one time only)
chmod +x deploy.sh

# Deploy to production
./deploy.sh production user@prod-server.com
```

**The script automatically:**
1. ✅ Loads SAML certificates from local `storage/app/sso/`
2. ✅ Converts them to base64
3. ✅ Pushes code to remote
4. ✅ Installs dependencies
5. ✅ Sets `SAML_*_CERT` env vars in production `.env`
6. ✅ Clears cache and runs migrations

---

## Option 1 (Manual - if script doesn't work)

### Step 1: Local - Generate base64 certificates
```bash
SAML_IDP_CERT=$(base64 -i storage/app/sso/idp.cert | tr -d '\n')
SAML_SP_CERT=$(base64 -i storage/app/sso/sp.crt | tr -d '\n')
SAML_SP_KEY=$(base64 -i storage/app/sso/sp.key | tr -d '\n')

# Display for copy-paste
echo "SAML_IDP_CERT=$SAML_IDP_CERT"
echo "SAML_SP_CERT=$SAML_SP_CERT"
echo "SAML_SP_KEY=$SAML_SP_KEY"
```

### Step 2: Server - Add to `.env.production`
```bash
ssh user@prod-server

# Edit your .env file
nano /var/www/informatique-it.com/.env

# Add these lines (paste from Step 1):
SAML_IDP_CERT=LS0tLS1CRUdJTi... (paste full base64)
SAML_SP_CERT=LS0tLS1CRUdJTi... (paste full base64)
SAML_SP_KEY=LS0tLS1CRUdJTi... (paste full base64)

# Save and exit
```

### Step 3: Server - Verify & clear cache
```bash
# Secure .env permissions
chmod 600 /var/www/informatique-it.com/.env

# Clear cache
cd /var/www/informatique-it.com
php artisan config:clear
php artisan cache:clear

# Test SAML config loads correctly
php artisan tinker
> config('saml2_settings')['idpNames']
```

---

## Security Checklist ✅

- [ ] SAML certificates are NOT in git (`.gitignore` covers `/storage/app/sso/`)
- [ ] Certificates loaded from `.env` variables (not file system)
- [ ] `.env` file permissions set to 600 (`-rw-------`)
- [ ] `config/saml2/synology_idp_settings.php` uses `env()` fallback for certs
- [ ] `SAML_STRICT=false` in development, verify for production
- [ ] Log SAML authentication attempts: `tail -50 storage/logs/laravel.log | grep Saml2`

---

## Troubleshooting

### Certificate not found error
```
Error: idp_cert_or_fingerprint_not_found_and_required
```

**Fix:**
1. Verify `.env` has `SAML_IDP_CERT` set:
   ```bash
   grep SAML_IDP_CERT /var/www/informatique-it.com/.env
   ```

2. If empty, add certificates from Step 1-2 above

3. Clear cache:
   ```bash
   cd /var/www/informatique-it.com && php artisan config:clear
   ```

### SSO login failing
1. Check logs: `tail -50 storage/logs/laravel.log`
2. Verify SP Entity ID matches Synology AppSSO config
3. Verify ACS URL (Assertion Consumer Service) is correct:
   - Should be: `https://yourdomain.com/saml2/synology/acs`

### Certificate validation fails
```
wantAssertionsSigned => true requires certificate signature
```

**If using self-signed certs:**
```php
// In config/saml2/synology_idp_settings.php:
'security' => [
    'wantAssertionsSigned' => false,  // For development
    // or
    'wantAssertionsSigned' => true,   // For production with valid certs
]
```

---

## Rollback

```bash
# Revert last deployment
cd /var/www/informatique-it.com
git revert HEAD
php artisan migrate:rollback
```

---

## Environment Variables Reference

Add these to your production `.env`:

```bash
# App Configuration
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# SAML Configuration (auto-loaded from env vars)
SAML_SP_ENTITY_ID=https://yourdomain.com
SAML_SP_ACS_URL=https://yourdomain.com/saml2/synology/acs
SAML_SP_SLS_URL=https://yourdomain.com/saml2/synology/sls

SAML_IDP_ENTITY_ID=https://ldap.yellowcactus.com/webman/sso/SSOOauth.cgi
SAML_IDP_SSO_URL=https://ldap.yellowcactus.com/webman/sso/SSOOauth.cgi
SAML_IDP_SLS_URL=https://ldap.yellowcactus.com/webman/sso/SSOOauth.cgi

# SAML Certificates (base64-encoded)
SAML_IDP_CERT=LS0tLS1CRUdJTi... (from Step 1)
SAML_SP_CERT=LS0tLS1CRUdJTi... (from Step 1)
SAML_SP_KEY=LS0tLS1CRUdJTi... (from Step 1)

# SAML Security
SAML_STRICT=true
SAML_DEBUG=false
```

---

## Testing SSO in Production

1. Go to login page: `https://yourdomain.com/login`
2. Click "Sign in with Synology SSO"
3. Redirect to Synology IdP
4. Authenticate with LDAP credentials
5. Redirect back to dashboard
6. Check user was created with correct role

**If not working:**
```bash
# SSH to server
ssh user@prod-server
cd /var/www/informatique-it.com

# Watch logs in real-time
php artisan pail --filter=Saml2

# Or check log file
tail -f storage/logs/laravel.log
```

