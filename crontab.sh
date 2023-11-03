#!/bin/bash
cd /home/deployer/ORCHESTRATOR/
docker-compose exec -T app bash -c "php artisan schedule:run"
