# BRECO

## Local server, hot reload (port 5173)

Terminal 1, Backend docker (sans frontend) :
'''
docker-compose up -d backend mysql nginx
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
