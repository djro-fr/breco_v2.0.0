/* groovylint-disable CompileStatic, GStringExpressionWithinString */
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
                    image 'oven/bun:1-alpine'
                    args '-u root'
                }
            }
            steps {
                echo 'Linting...'
                sh '''
                    cd frontend/breco
                    bun install --frozen-lockfile || bun install --frozen-lockfile
                    bun run lint
                '''
            }
        }
        // stage('SonarQube Analysis') {
        // }
        stage('Tests') {
            parallel {
                stage('Unit Tests') {
                    agent {
                        docker {
                            image 'djrofr/breco-vitest:latest'
                            args '-u root'
                            alwaysPull true
                        }
                    }
                    steps {
                        sh '''
                            cd frontend/breco
                            npm run test:unit
                        '''
                    }
                    post {
                        always {
                            junit 'frontend/breco/test-results/unit-results.xml'
                            stash name: 'unit-results', includes: 'frontend/breco/test-results/unit-results.xml'
                        }
                    }
                }
                stage('Integration Tests') {
                    agent {
                        docker {
                            image 'djrofr/breco-vitest:latest'
                            args '-u root'
                            alwaysPull true
                        }
                    }
                    steps {
                        sh '''
                            cd frontend/breco
                            npm run test:integration
                        '''
                    }
                    post {
                        always {
                            junit 'frontend/breco/test-results/integration-results.xml'
                            stash name: 'integration-results', includes: 'frontend/breco/test-results/integration-results.xml'
                        }
                    }
                }
                stage('UI Tests') {
                    agent {
                        docker {
                            image 'djrofr/breco-vitest:latest'
                            args '-u root'
                            alwaysPull true
                        }
                    }
                    steps {
                        sh '''
                            cd frontend/breco
                            npm run test:ui
                        '''
                    }
                    post {
                        always {
                            junit 'frontend/breco/test-results/ui-results.xml'
                            stash name: 'ui-results', includes: 'frontend/breco/test-results/ui-results.xml'
                        }
                    }
                }
                stage('E2E Tests') {
                    agent {
                        docker {
                            image 'djrofr/breco-e2e:latest'
                            args '-u root'
                            alwaysPull true
                        }
                    }
                    steps {
                        sh '''
                            cd frontend/breco
                            npm ci
                            npm run test:e2e
                        '''
                    }
                    post {
                        always {
                            junit 'frontend/breco/test-results/e2e-results.xml'
                            stash name: 'e2e-results', includes: 'frontend/breco/test-results/e2e-results.xml'
                        }
                    }
                }
                stage('PHP Unit Tests') {
                    agent {
                        docker {
                            image 'djrofr/breco-phptest:8.4'
                            args '-u root'
                            alwaysPull true
                        }
                    }
                    steps {
                        sh '''
                            cd backend/breco
                            curl -sS https://getcomposer.org/installer | php
                            php composer.phar install --no-interaction 
                            mkdir -p test-results
                            php composer.phar test
                    '''
                    }
                    post {
                        always {
                            junit 'backend/breco/test-results/phpunit-results.xml'
                            stash name: 'phpunit-results', includes: 'backend/breco/test-results/phpunit-results.xml'
                        }
                    }
                }
            }
        }
        stage('Copy Test Results to Frontend') {
            // http://37.59.101.232:3001/test-results/unit-results.xml
            // http://37.59.101.232:3001/test-results/integration-results.xml
            // http://37.59.101.232:3001/test-results/ui-results.xml
            // http://37.59.101.232:3001/test-results/e2e-results.xml
            // http://37.59.101.232:3001/test-results/phpunit-results.xml
            steps {
                unstash 'unit-results'
                unstash 'integration-results'
                unstash 'ui-results'
                unstash 'e2e-results'
                unstash 'phpunit-results'
                sh '''
                    echo "Copy test results to dist/..."
                    mkdir -p frontend/breco/test-results
                    cp backend/breco/test-results/phpunit-results.xml \
                    frontend/breco/test-results/
                    echo '✅ Copy succeed'
                    echo "Verification:"
                    ls -la frontend/breco/test-results/
                '''
            }
        }
        stage('Build') {
            steps {
                echo 'Docker images build...'
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
        // stage('Swagger Bake: API Documentation') {
        // }
        stage('Deploy') {
            steps {
                withCredentials([usernamePassword(credentialsId: 'docker_credentials', usernameVariable: 'DOCKER_USERNAME', passwordVariable: 'DOCKER_PASSWORD')]) {
                    sh '''
                        # Login to Docker Hub
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

                echo 'Re-deploy on VPS...'
                sshagent(credentials: ['vps-ssh']) {
                    sh '''
                        ssh -o StrictHostKeyChecking=no ubuntu@37.59.101.232 "cd ~/breco_v2_0_0 && \
                        docker-compose -f docker-compose.yml stop frontend backend nginx mysql && \
                        docker-compose -f docker-compose.yml rm -f frontend backend nginx mysql && \
                        docker-compose -f docker-compose.yml pull frontend backend nginx mysql && \
                        docker-compose -f docker-compose.yml up -d frontend backend nginx mysql"
                    '''                    
                }
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
        stage('Verify') {
            steps {
                echo 'Deployment verification...'
                sh '''
                    sleep 10
                    curl -f http://37.59.101.232:8081/api/health || exit 1
                    echo "✅ Health check OK !"
                '''
            }
        }
        // stage('Security:Owasp ZAP Scan') {
        // }

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

        //             # Launch test
        //             ./apache-jmeter-5.6.3/bin/jmeter.sh -n -t ${WORKSPACE}/jmeter/test.jmx -l ${WORKSPACE}/jmeter/results.jtl -j ${WORKSPACE}/jmeter/jmeter.log

    //             # Generates the report
    //             ./apache-jmeter-5.6.3/bin/jmeter.sh -g ${WORKSPACE}/jmeter/results.jtl -o ${WORKSPACE}/jmeter/report
    //         '''
    //     }
    // }
    }
    post {
        failure {
            echo '❌ Pipeline failed!'
        }
        success {
            echo '✅ Pipeline succeeded!'
        }
    }
}
