pipeline {
    agent any
    environment {
        DOCKER_COMPOSE_FILE = 'docker-compose.yml'
        VPS_IP = '37.59.101.232'
        MYSQL_ROOT_PASSWORD = credentials('mysql_root_password')
        MYSQL_DB = credentials('mysql_db')
        MYSQL_USER = credentials('mysql_user')
        MYSQL_PASSWORD = credentials('mysql_password')
        JWT_SECRET = credentials('jwt_secret')
        VITE_API_URL = credentials('vite_api_url')
        CORS_ORIGIN = credentials('cors_origin')
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
        stage('Test: Unit Tests') {
            agent {
                docker {
                    image 'node:25-alpine3.21'
                    args '-u root'
                }
            }
            steps {
                echo "Tests unitaires Vitest..."
                sh '''
                    npm install -g bun
                    cd frontend/breco
                    bun install --frozen-lockfile
                    VITEST=true bun run test:unit
                '''
            }
        }
        stage('Test: Integration Tests') {
            agent {
                docker {
                    image 'node:25-alpine3.21'
                    args '-u root'
                }
            }
            steps {
                echo "Tests intégration..."
                sh '''
                    npm install -g bun
                    cd frontend/breco
                    bun install --frozen-lockfile
                    VITEST=true bun run test:integration
                '''
            }
        }
        stage('Test: UI Tests') {
            agent {
                docker {
                    image 'node:25-alpine3.21'
                    args '-u root'
                }
            }
            steps {
                echo "Tests UI..."
                sh '''
                    npm install -g bun
                    cd frontend/breco
                    bun install --frozen-lockfile
                    VITEST=true bun run test:ui
                '''
            }
        }
        stage('Test: E2E Tests') {
            steps {
                echo "Tests E2E (Selenium)..."
                sh '''
                    npm install -g bun
                    cd frontend/breco
                    bun install --frozen-lockfile
                    VITEST=true bun run test:e2e
                '''
            }
        }
        // stage('Build') {
        //     steps {
        //         echo "Build des images Docker..."
        //         sh 'docker-compose build'
        //     }
        // }
        // stage('Deploy') {
        //     steps {
        //         echo "Redéploiement..."
        //         sh '''
        //             docker-compose down
        //             docker-compose up -d
        //         '''
        //     }
        // }
        // stage('Verify') {
        //     steps {
        //         echo "Vérification du déploiement..."
        //         sh '''
        //             sleep 10
        //             curl -f http://37.59.101.232:8081/health || exit 1
        //             echo "✅ OK !"
        //         '''                
        //     }
        // }
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