pipeline {
    agent any

    environment {
        DOCKER_COMPOSE_FILE = 'docker-compose.yml'
        VPS_IP = '37.59.101.232'
    }

    stages {
        stage('Checkout') {
            steps {
                sh '''
                    mkdir -p ~/.ssh
                    ssh-keyscan -t rsa,ed25519 github.com >> ~/.ssh/known_hosts 2>/dev/null || true
                '''
                checkout scm
            }
        }

        stage('Build') {
            steps {
                echo "Build des images Docker..."
                sh '''
                    set -a
                    . .env
                    set +a
                    docker-compose build
                '''
                    }
        }

        stage('Deploy') {
            steps {
                echo "Redéploiement..."
                sh '''
                    set -a
                    . .env
                    set +a
                    docker-compose down
                    docker-compose up -d
                '''
            }
        }

        stage('Verify') {
            steps {
                echo "Vérification du déploiement..."
                sh '''
                    set -a
                    . .env
                    set +a
                    sleep 10
                    curl -f http://37.59.101.232:8081/auth/test || exit 1
                    echo "✅ OK !"
                '''
                }
    }

    post {
        always {
            echo "Pipeline terminé"
        }
        failure {
            echo "❌ Pipeline échoué !"
        }
    }
}
