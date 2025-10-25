pipeline {
    agent any

    environment {
        DOCKER_COMPOSE_FILE = 'docker-compose.yml'
        VPS_IP = '37.59.101.232'
    }

    stages {
        stage('Checkout') {
            steps {
                echo "Clonage du repo..."
                checkout scm
            }
        }

        stage('Build') {
            steps {
                echo "Build des images Docker..."
                sh 'docker-compose build'
            }
        }

        stage('Deploy') {
            steps {
                echo "Redéploiement..."
                sh 'docker-compose down'
                sh 'docker-compose up -d'
            }
        }

        stage('Verify') {
            steps {
                echo "Vérification du déploiement..."
                sh 'sleep 10'
                sh 'curl -f http://37.59.101.232:8081/auth/test || exit 1'
                echo "✅ Déploiement réussi !"
            }
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
