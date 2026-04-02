# Monitoring

Breco uses a Prometheus + Grafana stack for real-time metrics collection and visualization,
integrated into the Docker Compose setup.

## Stack

| Component | Image | Role |
| --- | --- | --- |
| Prometheus | `prom/prometheus:latest` | Metrics collection (scrape every 15s, 15-day retention) |
| Grafana | `grafana/grafana:latest` | Visualization and dashboards |
| nginx-exporter | `nginx/nginx-prometheus-exporter:latest` | Exposes Nginx metrics to Prometheus |

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

**Breco - Overview** — available in the `Breco` folder in Grafana.

Auto-provisioned from `monitoring/grafana/provisioning/dashboards/breco-overview.json` on container startup.

| Panel | Metric | Type |
| --- | --- |
| NGINX HTTP Requests | `nginx_http_requests_total` | Time series |
| NGINX Active connections | `nginx_connections_active` | Stat |
| NGINX Connections waiting | `nginx_connections_waiting` | Stat |
| NGINX Accepted connections | `nginx_connections_accepted` | Stat |

## Configuration

### Prometheus

Configuration file: `monitoring/prometheus.yml`

Scrape targets:

- `prometheus:9090` — Prometheus itself
- `nginx-exporter:9113` — Nginx metrics

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
            └── breco-overview.json # Breco Overview dashboard
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

## Volumes

| Volume | Content |
| --- | --- |
| `prometheus_data` | Time series data (15-day retention) |
| `grafana_data` | Grafana configuration and user data |

---

## Last Update

**Date**: April 2, 2026
