# Breco Endpoints

## Exposed Ports

### Local Development Environment

> Works on **Windows** (Docker Desktop + WSL2) or **Ubuntu** (native Docker)

| Service | Port | URL | Description |
| --- | --- | --- | --- |
| Frontend | 3001 | http://localhost:3001 | Vue.js application |
| Backend | 8765 | http://localhost:8765 | API PHP-FPM (direct) |
| Nginx | 8081 | http://localhost:8081 | Reverse proxy |
| MySQL | 3307 | localhost:3307 | Database |
| Mailhog UI | 8025 | http://localhost:8025 | Email web interface |
| Mailhog SMTP | 1025 | localhost:1025 | SMTP server |
| Swagger UI | 8081 | http://localhost:8081/swagger | API documentation |
| SonarQube | 9000 | http://localhost:9000 | Code quality analysis |
| Grafana | 3002 | http://localhost:3002 | Monitoring dashboards (SSH tunnel only) |

**Notes**:

- Swagger UI shares port 8081 with Nginx - it is not a separate service
- On **Windows**: Docker runs via WSL2, ports accessible on `localhost`
- On **Ubuntu**: Native Docker, ports accessible on `localhost` or `127.0.0.1`

### Production Environment (Ubuntu VPS)

| Service | Port | URL | Description |
| --- | --- | --- | --- |
| Frontend | 3001 | http://37.59.101.232:3001 | Vue.js application |
| Backend | 8765 | http://37.59.101.232:8765 | API PHP-FPM (direct) |
| Nginx | 8081 | http://37.59.101.232:8081 | Reverse proxy - ZAP scan target |
| MySQL | 3307 | 37.59.101.232:3307 | Database |
| Mailhog UI | 8025 | http://37.59.101.232:8025 | Email web interface |
| Mailhog SMTP | 1025 | 37.59.101.232:1025 | SMTP server |
| Jenkins | 8080 | http://37.59.101.232:8080 | CI/CD pipeline |
| Swagger UI | 8081 | http://37.59.101.232:8081/swagger | API documentation |
| SonarQube | 9000 | http://37.59.101.232:9000 | Code quality analysis |
| Grafana | 3002 | SSH tunnel only | Monitoring dashboards — see MONITORING.md |
| Prometheus | 9090 | Internal only | Metrics collection (not exposed publicly) |

**SSH Access**: `ssh ubuntu@37.59.101.232` (custom port: see private config)

---

## Important Notes

### Mailhog in Production

**WARNING**: Mailhog is currently active in production on the VPS.

**Mailhog is a development tool only** and should **NOT** be used in real production.

**TODO before production deployment**:

- [ ] Disable Mailhog on VPS
- [ ] Configure a real SMTP service (SendGrid, AWS SES, Mailgun, etc.)
- [ ] Update `backend/breco/config/app.php` with SMTP credentials
- [ ] Remove Mailhog from `docker-compose.yml`
- [ ] Configure environment variables for email in production

### Jenkins

Jenkins runs as a **Docker Compose service** on the VPS (defined in `docker-compose.yml`).
It uses a custom image defined in `jenkins/Dockerfile-jenkins`.
For update procedure, see [jenkins/JENKINS.md](../jenkins/JENKINS.md).

### SonarQube: Jenkins connectivity

Jenkins and SonarQube are both on the `breco_network` Docker network.
The Jenkins pipeline reaches SonarQube via its container IP: `http://172.18.0.5:9000`

> If SonarQube becomes unreachable from Jenkins, verify the current IP with:
> `docker inspect breco_sonarqube | grep IPAddress`

### OWASP ZAP

The Jenkins pipeline runs an automated OWASP ZAP baseline scan after each deployment, targeting Nginx on port 8081.  
Results are archived as an HTML report in Jenkins: **Build → OWASP ZAP Security Report**.

### Grafana / Monitoring

Grafana is not publicly exposed. Access via SSH tunnel only — see [MONITORING.md](../monitoring/MONITORING.md).
Prometheus scrapes metrics every 15s from nginx-exporter and cAdvisor.

---

**Last updated**: April 2, 2026
