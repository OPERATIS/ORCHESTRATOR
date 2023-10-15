#!/bin/bash
cd /home/deployer/ORCHESTRATOR/
docker-compose exec -T --user=www app bash -c "php artisan schedule:run"
