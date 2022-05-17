start:
	@docker-compose up -d --remove-orphans
	@symfony proxy:start --quiet
	@symfony server:start -d --quiet
	@symfony run -d yarn encore dev-server
	@symfony run -d bin/console messenger:consume async

stop:
	@symfony server:stop --quiet
	@docker-compose stop 

restart: stop start
	
dump:
	@symfony server:stop --quiet
	@dep database:download preprod
	@symfony console doctrine:database:drop --force --quiet
	@symfony console doctrine:database:create --quiet
	@docker-compose exec database bash -c 'pg_restore -U $$POSTGRES_USER -d $$POSTGRES_DB --clean --if-exists --no-owner --no-acl /tmp/database/formation.dump'
	@symfony console doctrine:migration:migrate --no-interaction --quiet
	@symfony server:start -d --quiet

dump-local:
	docker-compose exec database bash -c 'pg_dump -Fc -U $$POSTGRES_USER $$POSTGRES_DB -f /tmp/database/formation.dump'


db:
	@symfony console doctrine:database:drop --force
	@symfony console doctrine:database:create
	@symfony console doctrine:schema:create
	@symfony console doctrine:fixture:load -n
	CONSOLE="symfony console" IMPORT_DIR="var/imports" bin/import.sh
	@echo '✓ Done'

docs:
	docker run --rm --name slate -v $(pwd)/public/docs:/srv/slate/build -v $(pwd)/docs:/srv/slate/source slatedocs/slate build

.PHONY: docs
