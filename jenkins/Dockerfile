# jenkins/Dockerfile - for VPS
FROM jenkins/jenkins:2.541.3-lts-jdk21
USER root
# Install Docker CLI
RUN apt-get update && \
    apt-get install -y \
        apt-transport-https \
        ca-certificates \
        curl \
        gnupg \
        lsb-release && \
    curl -fsSL https://download.docker.com/linux/debian/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg && \
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/debian $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null && \
    apt-get update && \
    apt-get install -y docker-ce-cli && \
    rm -rf /var/lib/apt/lists/* && \
    # Install Docker Compose
    curl -L "https://github.com/docker/compose/releases/download/v2.24.5/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose && \
    chmod +x /usr/local/bin/docker-compose && \
    # Adds jenkins to docker group to allow running docker commands without sudo
    groupadd -g 999 docker || true && \
    usermod -aG docker jenkins
USER jenkins
# Install essential Jenkins plugins for pipeline and docker integration
RUN jenkins-plugin-cli --plugins \
    blueocean \
    docker-workflow \
    git \
    workflow-aggregator \
    credentials \
    github \
    pipeline-stage-view \
    timestamper \
    ws-cleanup \
    ssh-agent \
    htmlpublisher
# Disable Jenkins setup wizard and allow loading of resources in the directory browser    
ENV JAVA_OPTS="-Djenkins.install.runSetupWizard=false -Dhudson.model.DirectoryBrowserSupport.CSP="
# Expose Jenkins default HTTP and agent ports
EXPOSE 8080 50000