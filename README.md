# Breco v2.0.0

Carpooling app, for the Brittany region in France, built with Vue.js and CakePHP.

## Documentation

- [Architecture](docs/architecture.md) - DDD architecture of the project
- [Error Handling](docs/error-handling.md) - Error flow

## Technical stack

- **Frontend** : Vue.js 3, TypeScript, Tailwind CSS
- **Backend** : CakePHP, MySQL
- **DevOps** : Docker, Jenkins

## Local server, hot reload (port 5173)

Terminal 1, Backend docker (sans frontend) :
'''
docker-compose up -d backend mysql nginx mailhog
(windows)
ou
docker-compose -f docker-compose.linux.yml up --build -d mysql backend nginx mailhog
(ubuntu)
'''

Terminal 2, Frontend dev :
'''
cd frontend/breco
bun run dev
'''

## Compose docker containers locally

Sur Linux :
'''docker-compose -f docker-compose.linux.yml up -d'''

Sur Windows :
'''docker-compose up -d'''

## Local frontend docker image re-build (at the root of the application directory)

'''
docker build --build-arg BUILD_NUMBER=1 -t local/breco-frontend:latest -f frontend/breco/Dockerfile-frontend frontend/breco
docker-compose down
docker-compose up -d
'''

## Local backend docker image re-build (at the root of the application directory)

docker build --build-arg BUILD_NUMBER=1 -t local/breco-backend:latest -f backend/breco/Dockerfile-backend backend/breco

## Test user (to remove in production)

'''test@test.com/Password123'''

## Empty user db

'''
docker exec -it breco_mysql mysql -u root -p breco_db

mysql> SELECT * FROM users;
mysql> TRUNCATE TABLE users;
'''

## Delete user nr. 4

'''
docker exec -it breco_mysql mysql -u root -p breco_db

mysql> SELECT * FROM users;
mysql> DELETE FROM users WHERE id='4';
'''

## Migration DB with Docker

docker-compose exec backend bin/cake migrations migrate

## Postman

'''
POST
http://localhost:8081/api/auth/register
Body:
{
  "email": "test@example.com",
  "password": "Test1234!",
  "password_confirmation": "Test1234!",
  "firstName": "Jean",
  "lastName": "Dupont",
  "phone":"0607080910"
}
'''

'''
POST
http://localhost:8081/api/auth/login
Body:
{
  "email": "test@example.com",
  "password": "Test1234!"
}
'''
