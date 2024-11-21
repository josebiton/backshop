pipeline {
    agent any

    stages {
        stage('Preparación') {
            steps {
                echo 'Clonando el repositorio desde GitHub...'
                git branch: 'main', url: 'https://github.com/josebiton/backshop.git'
                echo 'Repositorio clonado con éxito.'
            }
        }

        stage('Verificar versión de PHP') {
            steps {
                echo 'Verificando la versión de PHP...'
                sh 'php --version'
            }
        }

        stage('Pruebas Unitarias con PHP') {
            steps {
                echo 'Ejecutando pruebas unitarias con PHPUnit...'
                sh '''
                    chmod +x ./vendor/bin/phpunit
                    ./vendor/bin/phpunit
                '''
            }
        }

        stage('Compilación de Docker') {
            steps {
                echo 'Construyendo imagen Docker...'
                sh 'docker build -t backshop .'
            }
        }

        stage('Implementación con Docker') {
            steps {
                echo 'Desplegando contenedores con Docker Compose...'
                sh 'docker compose up -d'
            }
        }
    }
}
