# Jenkins - Update procedure

## Versions

| Component | Current version |
| --- | --- |
| Jenkins LTS | 2.541.3 |
| JDK | 21 |
| Base image | `jenkins/jenkins:2.541.3-lts-jdk21` |

---

## Files concerned

```text
jenkins/
└── Dockerfile      ← custom Jenkins image (Docker CLI, plugins, JAVA_OPTS)
```

The Dockerfile is stored on VPS: /home/ubuntu/breco_v2_0_0/jenkins/Dockerfile
The Dockerfile starts from the official image jenkins/jenkins:2.541.3-lts-jdk21 and adds to it:

- the Docker CLI and Docker Compose (absent from the base image)
- the docker group for the user jenkins
- the pre-installed Jenkins plugins
- the custom JAVA_OPTS

The result is the image breco-jenkins:2.541.3 - a Jenkins image tailored specifically for Breco.

---

## Dockerfile explanation

```dockerfile
FROM jenkins/jenkins:2.541.3-lts-jdk21
```

Official Jenkins LTS base image with JDK 21.

```dockerfile
USER root
RUN apt-get install ... docker-ce-cli
```

Installs the **Docker CLI** so Jenkins can run `docker` commands from pipeline stages (e.g. ZAP scan, image build).

```dockerfile
RUN curl ... docker-compose
```

Installs **Docker Compose** for `docker-compose` commands in the pipeline (stop/start services on the VPS).

```dockerfile
RUN groupadd -g 999 docker && usermod -aG docker jenkins
```

Adds the `jenkins` user to the `docker` group so it can use the Docker socket without being root.

```dockerfile
USER jenkins
RUN jenkins-plugin-cli --plugins ...
```

Installs **Jenkins plugins** at image build time rather than at first startup - guarantees a reproducible image.

```dockerfile
ENV JAVA_OPTS="-Djenkins.install.runSetupWizard=false \
               -Dhudson.model.DirectoryBrowserSupport.CSP="
```

- `runSetupWizard=false` → disables the setup wizard at first boot
- `CSP=` (empty) → allows HTML reports to be displayed in Jenkins (required for the OWASP ZAP report via HTML Publisher)

---

## Update procedure

### 1. Update the Dockerfile

```dockerfile
# jenkins/Dockerfile
FROM jenkins/jenkins:X.X.X-lts-jdk21   ← set the new version
```

### 2. Rebuild the image

```bash
cd /home/ubuntu/breco_v2_0_0/jenkins
docker build -t breco-jenkins:X.X.X .
```

### 3. Replace the container

```bash
docker stop breco-jenkins
docker rm breco-jenkins
docker run -d \
  --name breco-jenkins \
  --privileged \
  --restart unless-stopped \
  -p 8080:8080 \
  -p 50000:50000 \
  -v jenkins-data:/var/jenkins_home \
  -v /var/run/docker.sock:/var/run/docker.sock \
  breco-jenkins:X.X.X
```

> **Note:** The `jenkins-data` volume retains all Jenkins configuration
> (jobs, credentials, plugins, build history).

### 4. In case of permissions error at startup

If Jenkins fails to start with `Permission denied` on `/var/jenkins_home/war`:

```bash
docker stop breco-jenkins
docker rm breco-jenkins

# Fix permissions on the volume
docker run --rm \
  -v jenkins-data:/var/jenkins_home \
  -u root \
  jenkins/jenkins:X.X.X-lts-jdk21 \
  chown -R 1000:1000 /var/jenkins_home

# Restart
docker run -d \
  --name breco-jenkins \
  --privileged \
  --restart unless-stopped \
  -p 8080:8080 \
  -p 50000:50000 \
  -v jenkins-data:/var/jenkins_home \
  -v /var/run/docker.sock:/var/run/docker.sock \
  breco-jenkins:X.X.X




```

---

## Useful commands

```bash
# Start / stop
docker start breco-jenkins
docker stop breco-jenkins

# Logs in case of problem
docker logs breco-jenkins --tail 50

# Check the active version
docker exec breco-jenkins jenkins --version
```

---

## JAVA_OPTS configuration

Defined in the Dockerfile:

```dockerfile
ENV JAVA_OPTS="-Djenkins.install.runSetupWizard=false -Dhudson.model.DirectoryBrowserSupport.CSP="
```

| Option | Role |
| --- | --- |
| `runSetupWizard=false` | Disables the startup wizard |
| `DirectoryBrowserSupport.CSP=` | Allows viewing HTML reports (ZAP, etc.) |

---

## Installed plugins

Defined in the Dockerfile via `jenkins-plugin-cli`:

| Plugin | Role |
| --- | --- |
| `blueocean` | Visual pipeline interface |
| `docker-workflow` | Docker integration in pipelines |
| `git` | Git/GitHub integration |
| `workflow-aggregator` | Jenkinsfile pipeline support |
| `credentials` | Secret management |
| `github` | GitHub integration |
| `pipeline-stage-view` | Pipeline stage visualization |
| `timestamper` | Build log timestamps |
| `ws-cleanup` | Workspace cleanup |
| `ssh-agent` | SSH authentication |
| `htmlpublisher` | HTML report publishing (ZAP, etc.) |

---

## Last Update

**Date**: March 26, 2026
