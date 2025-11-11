# BRECO

Local frontend docker image re-build à la racine de l'app :
'''
docker build --build-arg BUILD_NUMBER=1 -t local/breco-frontend:latest -f frontend/breco/Dockerfile-frontend frontend/breco
docker-compose down
docker-compose up -d
'''
