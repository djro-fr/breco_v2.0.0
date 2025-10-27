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
                    mkdir -p frontend/breco/test-results
                '''
                checkout scm
            }
        }
        stage('Lint') {
            agent {
                docker {
                    image 'node:25-alpine3.21'
                    args '-u root'
                }
            }
            steps {
                echo "Linting..."
                sh '''                    
                    cd frontend/breco
                    npm ci
                    npm run lint
                '''
            }
        }
        stage('Tests') {
            parallel {
                stage('Unit Tests') {
                    agent {
                        docker {
                            image 'node:25-alpine3.21'
                            args '-u root -v npm-cache:/root/.npm -v ${WORKSPACE}/frontend/breco/test-results:/app/test-results'
                        }
                    }
                    steps {
                        sh '''
                            cd /app
                            npm ci
                            npm run test:unit
                        '''
                    }
                    post {
                        always {
                            junit 'frontend/breco/test-results/unit-results.xml'
                        }
                    }
                }

                stage('Integration Tests') {
                    agent {
                        docker {
                            image 'node:25-alpine3.21'
                            args '-u root -v npm-cache:/root/.npm -v ${WORKSPACE}/frontend/breco/test-results:/app/test-results'
                        }
                    }
                    steps {
                        sh '''
                            cd /app
                            npm ci
                            npm run test:integration
                        '''
                    }
                    post {
                        always {
                            junit 'frontend/breco/test-results/integration-results.xml'
                        }
                    }
                }

                stage('UI Tests') {
                    agent {
                        docker {
                            image 'node:25-alpine3.21'
                            args '-u root -v npm-cache:/root/.npm -v ${WORKSPACE}/frontend/breco/test-results:/app/test-results'
                        }
                    }
                    steps {
                        sh '''
                            cd /app
                            npm ci
                            npm run test:ui
                        '''
                    }
                    post {
                        always {
                            junit 'frontend/breco/test-results/ui-results.xml'
                        }
                    }
                }
            }
        
            // stage('Test: E2E Tests') {
            //     agent {
            //         docker {
            //             image 'node:25-alpine3.21'
            //             args '-u root -v npm-cache:/root/.npm'
            //         }
            //     }
            //     steps {
            //         echo "Tests E2E (Selenium)..."
            //         sh '''
            //             export DEBIAN_FRONTEND=noninteractive
            //             apt-get update -qq && apt-get install -y firefox-esr wget netcat-openbsd
                        
            //             # Geckodriver
            //             wget -q https://github.com/mozilla/geckodriver/releases/download/v0.34.0/geckodriver-v0.34.0-linux64.tar.gz
            //             tar -xzf geckodriver-v0.34.0-linux64.tar.gz
            //             mv geckodriver /usr/bin/
            //             chmod +x /usr/bin/geckodriver
                        
            //             npm install -g bun
            //             cd frontend/breco
            //             bun install --frozen-lockfile
            //             VITEST=true bun run test:e2e
            //         '''
            //     }
            // }
        }
        stage('Copy Test Results to Frontend') {
            // http://37.59.101.232:3001/test-results/unit-results.xml
            // http://37.59.101.232:3001/test-results/integration-results.xml
            // http://37.59.101.232:3001/test-results/ui-results.xml
            steps {
                sh '''
                    echo "Copie des résultats de tests dans dist/..."
                    mkdir -p frontend/breco/dist/test-results
                    
                    # Trouve et copie tous les fichiers XML des tests
                    find /var/jenkins_home/workspace -name "*-results.xml" -exec cp {} frontend/breco/dist/test-results/ \; 2>/dev/null || true
                    
                    echo "Vérification :"
                    ls -la frontend/breco/dist/test-results/
                '''
            }
        }       

        stage('Build') {
            steps {
                echo "Build des images Docker..."
                sh 'docker-compose build --build-arg BUILD_NUMBER=${BUILD_NUMBER}'
            }
        }
        stage('Deploy') {
            steps {
                echo "Redéploiement..."
                sh '''
                    cat > .env << EOF
                    VITE_API_URL=http://37.59.101.232:8081/api
                    CORS_ORIGIN=http://37.59.101.232:3001
                    MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
                    MYSQL_DB=${MYSQL_DB}
                    MYSQL_USER=${MYSQL_USER}
                    MYSQL_PASSWORD=${MYSQL_PASSWORD}
                    JWT_SECRET=${JWT_SECRET}
                    EOF
                    docker-compose down
                    docker-compose up -d
                '''
            }
        }
        stage('Verify Deployment Version') {
            steps {
                sh '''
                    echo "Vérification de la version déployée..."                   
                    
                    sleep 10
                    # Récupère le numéro de build et la date depuis le serveur
                    
                    BUILD_NUM=$(curl -s http://37.59.101.232:3001/BUILD_NUMBER.txt 2>/dev/null | tr -d '\n')
                    BUILD_DATE=$(curl -s http://37.59.101.232:3001/BUILD_DATE.txt 2>/dev/null | tr -d '\n')
                    
                    echo "Build Number: $BUILD_NUM"
                    echo "Build Date: $BUILD_DATE"
                    echo "Jenkins Build: ${BUILD_NUMBER}"
                    
                    if [ "$BUILD_NUM" = "${BUILD_NUMBER}" ]; then
                        echo "✅ Correct version deployed!"
                    else
                        echo "❌ WRONG version deployed! Expected ${BUILD_NUMBER}, got $BUILD_NUM"
                        exit 1
                    fi
                '''
            }
        }
        stage('Verify') {
            steps {
                echo "Vérification du déploiement..."
                sh '''
                    sleep 10
                    curl -f http://37.59.101.232:8081/health || exit 1
                    echo "✅ Health check OK !"
                '''                
            }
        }
        
        // stage('Performance: JMeter Tests') {
        //     agent {
        //         docker {
        //             image 'openjdk:11-jre-slim'
        //             args '-u root'
        //         }
        //     }
        //     steps {
        //         echo "Tests de charge JMeter..."
        //         sh '''
        //             apt-get update -qq
        //             apt-get install -y wget unzip
                    
        //             # Télécharge JMeter
        //             wget -q https://archive.apache.org/dist/jmeter/binaries/apache-jmeter-5.6.3.zip
        //             unzip -o -q apache-jmeter-5.6.3.zip
                    
        //             # Lance le test
        //             ./apache-jmeter-5.6.3/bin/jmeter.sh -n -t ${WORKSPACE}/jmeter/test.jmx -l ${WORKSPACE}/jmeter/results.jtl -j ${WORKSPACE}/jmeter/jmeter.log
                    
        //             # Génère le rapport
        //             ./apache-jmeter-5.6.3/bin/jmeter.sh -g ${WORKSPACE}/jmeter/results.jtl -o ${WORKSPACE}/jmeter/report
        //         '''
        //     }
        // }

    }
    post {
        failure {
            echo "❌ Pipeline échoué !"
        }
        success {
            echo "✅ Pipeline réussi !"
        }
    }
}