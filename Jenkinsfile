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
        DOCKER_CREDENTIALS = credentials('docker_credentials')
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
                    # Install Bun first
                    curl -fsSL https://bun.sh/install | bash
                    export PATH="/root/.bun/bin:$PATH"
                    bun install --frozen-lockfile
                    bun run lint
                '''
            }
        }
        stage('Tests') {
            parallel {
                stage('Unit Tests') {
                    agent {
                        docker {
                            image 'node:25-alpine3.21'
                            args '-u root -v bun-cache:/root/.bun -v /var/jenkins_home/workspace/breco@2/frontend/breco:/app'
                        }
                    }
                    steps {
                        sh '''
                            cd /app
                            # Install Bun first
                            curl -fsSL https://bun.sh/install | bash
                            export PATH="/root/.bun/bin:$PATH"
                            bun install --frozen-lockfile
                            bun run test:unit
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
                            args '-u root -v bun-cache:/root/.bun -v /var/jenkins_home/workspace/breco@2/frontend/breco:/app'
                        }
                    }
                    steps {
                        sh '''
                            cd /app
                            # Install Bun first
                            curl -fsSL https://bun.sh/install | bash
                            export PATH="/root/.bun/bin:$PATH"
                            bun install --frozen-lockfile
                            bun run test:integration
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
                            args '-u root -v bun-cache:/root/.bun -v /var/jenkins_home/workspace/breco@2/frontend/breco:/app'
                        }
                    }
                    steps {
                        sh '''
                            cd /app
                            # Install Bun first
                            curl -fsSL https://bun.sh/install | bash
                            export PATH="/root/.bun/bin:$PATH"
                            bun install --frozen-lockfile
                            bun run test:ui
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
            //             args '-u root'
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
                        
            //             curl -fsSL https://bun.sh/install | bash
            //             export PATH="/root/.bun/bin:$PATH"
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
                    echo "Copy test results to dist/..."
                    mkdir -p frontend/breco/dist/test-results
                    
                    cp frontend/breco/test-results/*.xml frontend/breco/dist/test-results/ 2>/dev/null  && echo '✅ Copy succeed' || true
                    
                    echo "Verification:"
                    ls -la frontend/breco/dist/test-results/
                '''
            }
        } 
        stage('Build') {
            steps {
                echo "Docker images build..."
                sh '''
                    echo "BUILD_NUMBER is: ${BUILD_NUMBER}"

                    # Frontend Build
                    docker build \
                      --no-cache \
                      --build-arg BUILD_NUMBER=${BUILD_NUMBER} \
                      -t breco_v2_0_0_frontend:${BUILD_NUMBER} \
                      -t breco_v2_0_0_frontend:latest \
                      -f frontend/breco/Dockerfile-frontend \
                      frontend/breco
                    
                    # Backend Build 
                    docker build \
                      --no-cache \
                      -t breco_v2_0_0_backend:${BUILD_NUMBER} \
                      -t breco_v2_0_0_backend:latest \
                      -f backend/breco/Dockerfile-backend \
                      backend/breco                           
                '''
            }
        }
        stage('Deploy') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'docker_credentials', usernameVariable: 'DOCKER_USERNAME', passwordVariable: 'DOCKER_PASSWORD')]) {
                    sh '''
                        # Login to Docker Hub (automaticly mask password in logs)
                        echo "${DOCKER_PASSWORD}" | docker login -u "${DOCKER_USERNAME}" --password-stdin
                        
                        # Push Frontend
                        docker tag breco_v2_0_0_frontend:latest ${DOCKER_USERNAME}/breco-frontend:${BUILD_NUMBER}
                        docker tag breco_v2_0_0_frontend:latest ${DOCKER_USERNAME}/breco-frontend:latest
                        docker push ${DOCKER_USERNAME}/breco-frontend:${BUILD_NUMBER}
                        docker push ${DOCKER_USERNAME}/breco-frontend:latest
                        
                        # Push Backend
                        docker tag breco_v2_0_0_backend:latest ${DOCKER_USERNAME}/breco-backend:${BUILD_NUMBER}
                        docker tag breco_v2_0_0_backend:latest ${DOCKER_USERNAME}/breco-backend:latest
                        docker push ${DOCKER_USERNAME}/breco-backend:${BUILD_NUMBER}
                        docker push ${DOCKER_USERNAME}/breco-backend:latest
                    '''
                }
                
                echo "Re-deploy on VPS..."
                sh '''
                    ssh -o StrictHostKeyChecking=no ubuntu@37.59.101.232 "cd ~/breco_v2_0_0 && \
                    docker-compose down && docker-compose pull && docker-compose up -d"
                '''

                echo "Test results copy on VPS..."
                sh '''
                    ssh -o StrictHostKeyChecking=no ubuntu@37.59.101.232 "mkdir -p ~/breco_v2_0_0/frontend/breco/dist/test-results"
                    scp -o StrictHostKeyChecking=no -r frontend/breco/dist/test-results/* ubuntu@37.59.101.232:~/breco_v2_0_0/frontend/breco/dist/test-results/
                '''
            }
        }
        stage('Verify Deployment Version') {
            steps {
                sh '''
                    sleep 30
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
        
        // stage('Performance: JMeter Tests') {
        //     agent {
        //         docker {
        //             image 'openjdk:11-jre-slim'
        //             args '-u root'
        //         }
        //     }
        //     steps {        
        //         sh '''
        //             apt-get update -qq
        //             apt-get install -y wget unzip
                    
        //             # Download JMeter
        //             wget -q https://archive.apache.org/dist/jmeter/binaries/apache-jmeter-5.6.3.zip
        //             unzip -o -q apache-jmeter-5.6.3.zip
                    
        //             # Lanuch test
        //             ./apache-jmeter-5.6.3/bin/jmeter.sh -n -t ${WORKSPACE}/jmeter/test.jmx -l ${WORKSPACE}/jmeter/results.jtl -j ${WORKSPACE}/jmeter/jmeter.log
                    
        //             # Generates the report
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