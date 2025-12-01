# Breco v2.0.0

Application de covoiturage construite avec Vue.js et CakePHP.

## 📚 Documentation

- [Architecture](docs/ARCHITECTURE.md) - Architecture DDD du projet

## 🏗️ Stack technique

- **Frontend** : Vue.js 3, TypeScript, Tailwind CSS
- **Backend** : CakePHP, MySQL
- **DevOps** : Docker, Jenkins

## Local server, hot reload (port 5173)

Terminal 1, Backend docker (sans frontend) :
'''
docker-compose up -d backend mysql nginx
ou
docker-compose -f docker-compose.linux.yml up --build -d mysql backend nginx
'''

Terminal 2, Frontend dev :
'''
cd frontend/breco
bun run dev
'''

## Local frontend docker image re-build à la racine de l'app

'''
docker build --build-arg BUILD_NUMBER=1 -t local/breco-frontend:latest -f frontend/breco/Dockerfile-frontend frontend/breco
docker-compose down
docker-compose up -d
'''

'''test@test.com/password123'''

## Compose docker containers locally

Sur Linux :
'''docker-compose -f docker-compose.linux.yml up -d'''

Sur Windows :
'''docker-compose up -d'''
