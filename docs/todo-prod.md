# TODO - Production Deployment Checklist

> **Current status**: Development (not production-ready)  
> **Last updated**: March 25, 2026

---

## Security (CRITICAL)

### Credentials

- [ ] Change MySQL root password
- [ ] Change MySQL user password
- [ ] Generate new JWT secret
- [ ] Use environment variables (`.env`)
- [ ] NEVER commit secrets to Git

### HTTPS & SSL

- [ ] Buy a domain name
- [ ] Install Let's Encrypt (certbot)
- [ ] Force HTTPS (HTTP → HTTPS redirect)
- [ ] Verify SSL (A+ on SSL Labs)

### Authentication

- [ ] Implement JWT refresh tokens
- [ ] Add rate limiting (login, register)
- [ ] Enable fail2ban (SSH + nginx)

### OWASP ZAP Security Audit

- [x] OWASP ZAP baseline scan integrated in Jenkins pipeline
- [ ] Fix WARN-NEW: Server leaks version information (`Server` header in Nginx)
- [ ] Fix WARN-NEW: Content Security Policy (CSP) header not set
- [ ] Fix WARN-NEW: Permissions Policy header not set
- [ ] Fix WARN-NEW: Cross-Domain Misconfiguration (CORS too permissive)
- [ ] Fix WARN-NEW: Storable and Cacheable Content (Cache-Control headers)
- [ ] Fix WARN-NEW: Cross-Origin-Embedder-Policy (COEP) header missing
- [ ] Add `robots.txt` and `sitemap.xml` (resolves In Page Banner Information Leak)
- [ ] Run ZAP full scan (active scan) before production launch

### CORS

- [ ] Restrict CORS (replace `*` with domain)

  ```nginx
  Access-Control-Allow-Origin: https://breco.fr
  ```

---

## Email

### SMTP Service

- [ ] **Disable Mailhog on VPS**
- [ ] Choose a service:
  - SendGrid (free 100/day)
  - AWS SES (pay-as-you-go)
  - Mailgun (free 5000/month)
  - Brevo (free 300/day)
- [ ] Configure credentials in `.env`
- [ ] Test email sending
- [ ] Configure professional templates

### Email DNS

- [ ] Configure SPF record
- [ ] Configure DKIM
- [ ] Configure DMARC
- [ ] Verify deliverability (mail-tester.com)

---

## Domain & DNS

- [ ] Buy domain name (e.g., `breco.fr`)
- [ ] Configure DNS:

  ```text
  A     @    → 37.59.101.232
  A     www  → 37.59.101.232
  ```

- [ ] Wait for propagation (24-48h)
- [ ] Update nginx with domain
- [ ] Update CORS with domain

---

## Database

### Backups

- [ ] Configure daily automatic backups
- [ ] Test backup restoration
- [ ] Store backups off-VPS (S3, Cloud)
- [ ] Retention: 7 days + 4 weeks + 12 months

### Optimization

- [ ] Create necessary indexes
- [ ] Analyze slow queries
- [ ] Configure MySQL log rotation

---

## Monitoring

### Alerts

- [ ] Configure monitoring (Prometheus/Grafana)
- [ ] Alerts: CPU > 80%, RAM > 90%, Disk > 85%
- [ ] Uptime monitoring (UptimeRobot, Pingdom)
- [ ] Notifications (Email, Slack)

### Logs

- [ ] Nginx log rotation
- [ ] Application log rotation
- [ ] Centralized logs (optional)
- [ ] NEVER log passwords/tokens

---

## Performance

### Cache

- [ ] Implement Redis/Memcached
- [ ] Cache frequent database queries
- [ ] HTTP cache nginx
- [ ] CDN for static assets

### Frontend

- [ ] Minify JS/CSS
- [ ] Compress images (WebP)
- [ ] Lazy loading
- [ ] Code splitting

### Backend

- [ ] Enable PHP OPcache
- [ ] Optimize N+1 queries
- [ ] API pagination
- [ ] Nginx gzip compression

---

## Tests

- [ ] Complete E2E tests (Cypress/Playwright)
- [ ] Load tests (goal: 1000 simultaneous users)
- [x] OWASP ZAP baseline scan — integrated in Jenkins pipeline (60 PASS, 7 WARN, 0 FAIL)
- [ ] Backend coverage > 80%

---

## Legal (GDPR)

- [ ] Privacy policy
- [ ] Terms of service (ToS)
- [ ] Legal notice
- [ ] Cookie consent banner
- [ ] Right to be forgotten (account deletion)
- [ ] User data export

---

## Documentation

- [ ] User documentation
- [ ] Deployment guide
- [ ] Rollback procedures
- [ ] Incident runbook
- [ ] Architecture diagram

---

## Deployment

- [ ] Zero-downtime deployment
- [ ] Blue/Green deployment (optional)
- [ ] Automatic rollback on error
- [ ] Health checks before routing traffic

---

## Final Checklist Before Launch

- [ ] All points above validated
- [ ] Load tests successful
- [ ] Security audit validated (ZAP 0 FAIL)
- [ ] Backup tested and functional
- [ ] Monitoring operational
- [ ] Complete documentation
- [ ] Team trained on procedures
- [ ] Communication plan launched

**NEVER launch in production until all CRITICAL points are validated!**

**Last updated**: March 25, 2026
