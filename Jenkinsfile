pipeline {
    agent any

    environment {
        IMAGE_NAME = "laravel_app"
        TAG = "${BUILD_ID}"
        CONTAINER_NAME = "laravel_app_${BUILD_ID}"
        PORT = "8081"
    }

    stages {
        stage('Build') {
            steps {
                script {
                    if (!fileExists('Dockerfile')) {
                        error 'Dockerfile no encontrado.'
                    }
                }

                echo 'Dockerfile encontrado.'
                sh 'docker build -t $IMAGE_NAME:$TAG .'
                echo "Imagen Docker construida correctamente: $IMAGE_NAME:$TAG"
            }
        }

        stage('Deploy') {
            steps {
                script {
                    // Eliminar contenedor que está usando el puerto 8081
                    def containerIds = sh(script: "docker ps -aq --filter publish=$PORT", returnStdout: true).trim()
                    if (containerIds) {
                        sh "docker rm -f ${containerIds}"
                    }

                    // Detener contenedores con misma imagen
                    def oldContainers = sh(script: "docker ps -q -f ancestor=$IMAGE_NAME", returnStdout: true).trim()
                    if (oldContainers) {
                        sh "docker stop ${oldContainers} && docker rm ${oldContainers}"
                    }

                    // Desplegar nuevo contenedor
                    sh "docker run -d -p $PORT:80 --name $CONTAINER_NAME $IMAGE_NAME:$TAG"

                    // Comandos post-deploy
                    sh "docker exec $CONTAINER_NAME sh -c 'test -f .env || cp .env.example .env'"
                    sh "docker exec $CONTAINER_NAME composer install --no-interaction --optimize-autoloader"
                    sh "docker exec $CONTAINER_NAME php artisan key:generate"
                    sh "docker exec $CONTAINER_NAME chmod -R 777 storage bootstrap/cache"

                    // Configuraciones .env básicas (sin DB ni sesiones)
                    sh "docker exec $CONTAINER_NAME sh -c \"sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env\""
                    sh "docker exec $CONTAINER_NAME sh -c \"sed -i 's/^DB_DATABASE=.*/DB_DATABASE=/' .env\""
                    sh "docker exec $CONTAINER_NAME sh -c \"sed -i 's/^DB_USERNAME=.*/DB_USERNAME=/' .env\""
                    sh "docker exec $CONTAINER_NAME sh -c \"sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=/' .env\""
                    sh "docker exec $CONTAINER_NAME sh -c \"sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=array/' .env\""

                    echo "Proyecto Laravel desplegado correctamente en http://localhost:$PORT"
                }
            }
        }
    }
}
