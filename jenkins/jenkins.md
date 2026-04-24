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
└── Dockerfile-jenkins      ← custom Jenkins image (Docker CLI, plugins, JAVA_OPTS)
```

Jenkins is managed as a **Docker Compose service** in `docker-compose.yml`.

The custom image `breco-jenkins:2.541.3` is built from `jenkins/Dockerfile-jenkins` and adds:

- the Docker CLI and Docker Compose (absent from the base image)
- the docker group (GID 988) for the user jenkins
- the pre-installed Jenkins plugins
- the custom JAVA_OPTS

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

Installs **Docker Compose** for `docker compose` commands in the pipeline.

```dockerfile
RUN groupadd -g 988 docker && usermod -aG docker jenkins
```

Adds the `jenkins` user to the `docker` group (GID 988 on this VPS) so it can use the Docker socket without being root.

> **Note:** If you get `permission denied` on `/var/run/docker.sock` after a VPS reinstall,
> check the GID with `stat -c '%g' /var/run/docker.sock` and update the Dockerfile accordingly.

```dockerfile
USER jenkins
RUN jenkins-plugin-cli --plugins ...
```

Installs **Jenkins plugins** at image build time rather than at first startup, guarantees a reproducible image.

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
# jenkins/Dockerfile-jenkins
FROM jenkins/jenkins:X.X.X-lts-jdk21   ← set the new version
```

### 2. Rebuild and redeploy via Docker Compose

```bash
cd /home/ubuntu/breco_v2.0.0
docker compose build jenkins
docker compose up -d jenkins
```

---

## Fresh reinstallation procedure

Use this procedure after a VPS reinstall or if the Jenkins container needs to be fully recreated.

### 1. Check Docker socket GID

```bash
stat -c '%g' /var/run/docker.sock
```

Update `groupadd -g <GID>` in `jenkins/Dockerfile-jenkins` if it differs from the current value.

### 2. Add Jenkins SSH key to VPS authorized_keys

```bash
# Generate a new SSH key inside the container
docker exec -it breco-jenkins bash -c "ssh-keygen -t ed25519 -C 'breco-jenkins' -f ~/.ssh/id_ed25519 -N '' && cat ~/.ssh/id_ed25519.pub"

# Add the public key to the VPS
echo "PASTE_PUBLIC_KEY_HERE" >> ~/.ssh/authorized_keys

# Test the connection
docker exec -it breco-jenkins ssh -p NUMERO_DE_PORT -o StrictHostKeyChecking=no -i /var/jenkins_home/.ssh/id_ed25519 ubuntu@37.59.101.232 "echo ok"
```

### 3. Add Jenkins SSH key to GitHub

```bash
docker exec -it breco-jenkins cat /var/jenkins_home/.ssh/id_ed25519.pub
```

Add the public key in **GitHub → Settings → SSH and GPG keys → New SSH key (Authentication)**.

Test:

```bash
docker exec -it breco-jenkins ssh -T git@github.com
```

### 4. Add GitHub to known_hosts

```bash
docker exec -it breco-jenkins bash -c "mkdir -p ~/.ssh && ssh-keyscan github.com >> ~/.ssh/known_hosts"
```

### 5. Reconfigure Jenkins credentials

In **Credentials → Global → Add Credentials**, recreate the following:

| ID | Type | Description |
| --- | --- | --- |
| `github-ssh` | SSH Username with private key | Jenkins private key (`/var/jenkins_home/.ssh/id_ed25519`), username: `git` |
| `vps-ssh` | SSH Username with private key | Your PC private key (`~/.ssh/id_ed25519`), username: `ubuntu` |
| `docker_credentials` | Username with password | Docker Hub credentials |
| `sonarqube-token` | Secret text | SonarQube project token |
| `mysql_root_password` | Secret text | MySQL root password |
| `mysql_db` | Secret text | MySQL database name |
| `mysql_user` | Secret text | MySQL username |
| `mysql_password` | Secret text | MySQL password |
| `jwt_secret` | Secret text | JWT secret key |
| `vite_api_url` | Secret text | e.g. `http://37.59.101.232:8081/api` |
| `cors_origin` | Secret text | e.g. `http://37.59.101.232:3001` |
| `vps_ssh_port` | Secret text | VPS SSH port number |

### 6. Reconfigure SonarQube

In **Manage Jenkins → System → SonarQube servers**:

- Name: `SonarQube`
- URL: `http://sonarqube:9000` *(IP resolved dynamically at runtime via `docker inspect`)*
- Token: select `sonarqube-token`

In **Manage Jenkins → Tools → SonarQube Scanner**:

- Name: `SonarQube Scanner`
- Install automatically: checked

### 7. Reconfigure the pipeline job

- **New Item** → `breco` → **Pipeline**
- Pipeline definition: `Pipeline script from SCM`
- SCM: `Git`
- Repository URL: `git@github.com:djro-fr/breco_v2.0.0.git`
- Credentials: `github-ssh`
- Branch: `*/main`
- Build Triggers: check **GitHub hook trigger for GITScm polling**

### 8. Reconfigure the GitHub webhook

In **GitHub → repo → Settings → Webhooks → Add webhook**:

- Payload URL: `http://37.59.101.232:8080/github-webhook/`
- Content type: `application/json`
- Events: `Just the push event`
- Active: checked

---

## Useful commands

```bash
# Start / stop
docker compose start jenkins
docker compose stop jenkins

# Logs in case of problem
docker logs breco-jenkins --tail 50

# Check the active version
docker exec breco-jenkins jenkins --version

# Check Docker socket GID
stat -c '%g' /var/run/docker.sock

# Sync docker-compose.yml and monitoring/ to VPS (done automatically by pipeline)
scp -P NUMERO_DE_PORT docker-compose.yml ubuntu@37.59.101.232:~/breco_v2.0.0/
scp -P NUMERO_DE_PORT -r monitoring/ ubuntu@37.59.101.232:~/breco_v2.0.0/
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
| `sonar` | SonarQube Scanner integration |

---

**Last updated**: April 24, 2026
