pipeline {
    agent any
     tools {
        nodejs "NodeJS" // Reemplaza "nodejs" con el nombre configurado en Jenkins
    }

    stages {
        stage('Preparacion') {
            steps {
                git branch: 'main', url: 'https://github.com/josebiton/backshop.git'
                echo 'Pulled from GitHub successfully'
            }
        }

        stage('Verifica version php') {
            steps {
                sh 'php --version'
            }
        }

        stage('Pruebas unitarias php') {
            steps {
                sh 'chmod +x ./vendor/bin/phpunit'
                sh './vendor/bin/phpunit'
            }
        }

        stage('Compilación de Docker') {
            steps {
                sh 'docker build -t backshop .'
            }
        }

        stage('Implementar php') {
            steps {
                sh 'docker compose up -d'
            }
        }

        stage('Ejecutar Postman Collection') {
            steps {
                script {
                    // Instalar Newman si no está instalado
                    sh 'npm install -g newman'

                    // Ejecutar la colección de Postman
                    sh "newman run 'Market.postman_collection.json' -e 'Market-Env.postman_environment.json'"
                }
            }
        }
    }
}
