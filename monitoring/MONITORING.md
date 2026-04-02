# Monitoring

Breco uses a Prometheus + Grafana stack for real-time metrics collection
and visualization, integrated into the Docker Compose setup.

## Stack

| Component | Image | Role |
| --- | --- | --- |
| Prometheus | `prom/prometheus:latest` | Metrics collection (scrape every 15s, 15-day retention) |
| Grafana | `grafana/grafana:latest` | Visualization and dashboards |
| nginx-exporter | `nginx/nginx-prometheus-exporter:latest` | Exposes Nginx metrics to Prometheus |
| cAdvisor | `gcr.io/cadvisor/cadvisor:latest` | Exposes CPU, RAM, Disk I/O metrics per container |

## Access

Grafana is not publicly exposed. Access is restricted to SSH tunnel only.

From your local machine:

```bash
ssh -L 3002:localhost:3002 -p NUMERO_DE_PORT ubuntu@VPS_IP -N
```

Leave the terminal open, then open `http://localhost:3002` in your browser.

| Field | Value |
| --- | --- |
| URL | `http://localhost:3002` |
| Login | `admin` |
| Password | `GRAFANA_PASSWORD` (see `.env`) |

## Dashboard

**Breco - Overview**: available in the `Breco` folder in Grafana.

Auto-provisioned from `monitoring/grafana/provisioning/dashboards/breco-overview.json` on container startup.

| Panel | Metric | Type |
| --- | --- | --- |
| NGINX HTTP Requests | `nginx_http_requests_total` | Time series |
| NGINX Active connections | `nginx_connections_active` | Stat |
| NGINX Connections waiting | `nginx_connections_waiting` | Stat |
| NGINX Accepted connections / sec | `rate(nginx_connections_accepted[1m])` | Time series |
| CPU Usage per Container (%) | `rate(container_cpu_usage_seconds_total{image!=""}[1m]) * 100` | Time series |
| Memory Usage per Container | `container_memory_usage_bytes{image!=""}` | Time series |
| Disk I/O per Container | `rate(container_fs_reads_bytes_total{image!=""}[1m])` + writes | Time series |

## Configuration

### Prometheus

Configuration file: `monitoring/prometheus.yml`

Scrape targets:

- `prometheus:9090`: Prometheus itself
- `nginx-exporter:9113`: Nginx metrics
- `cadvisor:8080`: Container metrics (CPU, RAM, Disk I/O)

### Grafana

Provisioning directory: `monitoring/grafana/provisioning/`

```text
monitoring/
├── prometheus.yml
└── grafana/
    └── provisioning/
        ├── datasources/
        │   └── datasource.yml      # Prometheus datasource (auto-configured)
        └── dashboards/
            ├── dashboards.yml      # Dashboard provider config
            └── breco-overview.json # Breco Overview dashboard (7 panels)
```

### Nginx stub_status

The `nginx-exporter` scrapes Nginx metrics via the `stub_status` module, restricted to the internal Docker network:

```nginx
location /stub_status {
    stub_status on;
    allow 172.0.0.0/8;
    deny all;
}
```

### cAdvisor

cAdvisor is mounted read-only on host system paths to collect container metrics:

```yaml
volumes:
  - /:/rootfs:ro
  - /var/run:/var/run:ro
  - /sys:/sys:ro
  - /var/lib/docker/:/var/lib/docker:ro
```

## Volumes

| Volume | Content |
| --- | --- |
| `prometheus_data` | Time series data (15-day retention) |
| `grafana_data` | Grafana configuration and user data |

## Disk Space Management

Docker images and build cache accumulate over time. The Jenkins pipeline runs automatic cleanup after each build:

```bash
docker image prune -a -f --filter until=24h
docker builder prune -f --filter until=24h
```

> **Note**: `docker volume prune` is intentionally excluded,
volumes contain persistent data (MySQL, Grafana, Prometheus)
and must never be pruned automatically.

## Relation to Course Objectives

This monitoring stack directly addresses the course requirements for real-time application surveillance:

- **Prometheus**: collects key metrics (response time, throughput, connection rates, CPU, RAM, Disk I/O)
- **Grafana**: visualizes metrics as dashboards and supports alerting
- **nginx-exporter**: provides HTTP-level observability without modifying application code
- **cAdvisor**: provides container-level resource monitoring
  (CPU > 80%, RAM > 90% thresholds configurable as alerts)

---

**Last updated**: April 2, 2026
