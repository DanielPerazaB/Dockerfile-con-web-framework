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
                bat 'docker build -t %IMAGE_NAME%:%TAG% .'
                echo "Imagen Docker construida correctamente: ${env.IMAGE_NAME}:${env.TAG}"
            }
        }

        stage('Deploy') {
            steps {
                bat '''
                REM Detener y eliminar contenedor que use el puerto
                for /f "tokens=*" %%i in ('docker ps -aq --filter "publish=%PORT%"') do (
                    docker rm -f %%i
                )

                REM Detener contenedores anteriores con la misma imagen
                for /f "tokens=*" %%i in ('docker ps -q -f "ancestor=%IMAGE_NAME%"') do (
                    docker stop %%i
                    docker rm %%i
                )

                REM Ejecutar el nuevo contenedor
                docker run -d -p %PORT%:80 --name %CONTAINER_NAME% %IMAGE_NAME%:%TAG%

                REM Post deploy - Laravel setup
                docker exec %CONTAINER_NAME% sh -c "test -f .env || cp .env.example .env"
                docker exec %CONTAINER_NAME% composer install --no-interaction --optimize-autoloader
                docker exec %CONTAINER_NAME% php artisan key:generate
                docker exec %CONTAINER_NAME% chmod -R 777 storage bootstrap/cache

                docker exec %CONTAINER_NAME% sh -c "sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env"
                docker exec %CONTAINER_NAME% sh -c "sed -i 's/^DB_DATABASE=.*/DB_DATABASE=/' .env"
                docker exec %CONTAINER_NAME% sh -c "sed -i 's/^DB_USERNAME=.*/DB_USERNAME=/' .env"
                docker exec %CONTAINER_NAME% sh -c "sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=/' .env"
                docker exec %CONTAINER_NAME% sh -c "sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=array/' .env"
                '''
            }
        }
    }
}

