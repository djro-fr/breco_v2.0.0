# TODO - Production Deployment Checklist

> **Current status**: Development (not production-ready)  

---

## Security (CRITICAL)

### Credentials

- [ ] Change MySQL root password
- [ ] Change MySQL user password
- [ ] Generate new JWT secret
- [ ] Use environment variables (`.env`)
- [ ] NEVER commit secrets to Git

### HTTPS & SSL

- [ ] Buy a domain name on OVH (e.g., `breco.fr`)
- [ ] Install Let's Encrypt (certbot)
- [ ] Force HTTPS (HTTP → HTTPS redirect)
- [ ] Verify SSL (A+ on SSL Labs)

### Authentication

- [x] Enable Fail2ban (SSH brute force protection)
- [x] SSH key-only authentication (password auth disabled)
- [x] Custom SSH port (default port 22 disabled)
- [ ] Implement JWT refresh tokens
- [ ] Add rate limiting (login, register)
- [ ] Enable Fail2ban for nginx

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

- [ ] **Disable Mailhog on VPS** ⚠️ Required before public launch
- [ ] Configure OVH SMTP (mail.ovh.net)
- [ ] Configure credentials in `.env`
- [ ] Test email sending

### Email DNS

- [ ] Configure SPF record (Sender Policy Framework)
- [ ] Configure DKIM (DomainKeys Identified Mail)
- [ ] Configure DMARC (Domain-based Message Authentication)

---

## Domain & DNS

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
- [ ] Store backups off-VPS (OVH Object Storage)
- [ ] Retention: 7 days + 4 weeks + 12 months

### Optimization

- [ ] Create necessary indexes
- [ ] Analyze slow queries
- [ ] Configure MySQL log rotation
- [ ] Performance tuning (MySQL, Nginx, Docker), run pipeline to detect regressions

---

## Monitoring

- [x] Configure monitoring (Prometheus/Grafana)
- [x] Alerts: CPU > 80%, RAM > 90%, Disk > 85%
- [x] Notifications (OVH SMTP)
- [ ] Uptime monitoring (UptimeRobot) ⚠️ Required before public launch
- [ ] Nginx log rotation
- [ ] Application log rotation
- [ ] NEVER log passwords/tokens

---

## Performance

- [ ] Enable PHP OPcache
- [ ] Optimize N+1 queries
- [ ] Nginx gzip compression
- [ ] Implement Redis/Memcached
- [ ] Cache frequent database queries

---

## Tests

- [x] OWASP ZAP baseline scan - integrated in Jenkins pipeline (60 PASS, 7 WARN, 0 FAIL)
- [ ] Complete E2E tests (Selenium)
- [ ] Extend test plan to Stories 3–10 (controllers, repositories, exceptions)
- [ ] UAT sessions with varied user profiles
- [ ] Load tests (goal: 1000 simultaneous users)
- [ ] Backend coverage > 80%

---

## Migration

- [ ] Migrate to Bun (blocked: Zod v3 incompatibility)

---

## Legal (GDPR)

- [ ] Privacy policy
- [ ] Legal notice
- [ ] Cookie consent banner
- [ ] Explicit consent at registration
- [ ] Right to be forgotten (account deletion)
- [ ] Right of access and rectification
- [ ] User data export
- [ ] CNIL compliance

---

## Documentation

- [x] Monitoring documentation (monitoring.md)
- [x] Architecture diagram
- [x] API documentation
- [x] Endpoints documentation
- [x] Getting started guide
- [ ] Deployment guide
- [ ] Rollback procedures

---

## Deployment

- [ ] Zero-downtime deployment
- [ ] Automatic rollback on error
- [ ] Health checks before routing traffic

---

## Final Checklist Before Launch

- [ ] All points above validated
- [ ] Load tests successful
- [ ] Security audit validated (ZAP 0 FAIL)
- [ ] Backup tested and functional
- [ ] Monitoring operational
- [ ] Uptime monitoring active (UptimeRobot)
- [ ] MailHog replaced by OVH SMTP
- [ ] HTTPS active
- [ ] GDPR/CNIL compliance validated
- [ ] Complete documentation

**NEVER launch in production until all CRITICAL points are validated!**

---

**Last updated**: April 24, 2026
