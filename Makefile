#=============================================================================#
#                           JWT CONFIGURATION
#=============================================================================#
ifndef EMAIL
EMAIL=admin@example.com
endif
ifndef PASSWORD
PASSWORD=123456
endif

ifndef JWT_DIR
JWT_DIR=api/config/jwt
endif
JWT_PRIVATE_KEY=${JWT_DIR}/private.pem
JWT_PUBLIC_KEY=${JWT_DIR}/public.pem

ifndef JWT_PASSPHRASE
JWT_PASSPHRASE=!ChangeMe!
endif

${JWT_DIR}:
	mkdir -p ${JWT_DIR}

${JWT_PRIVATE_KEY}: ${JWT_DIR}
	openssl genrsa -passout pass:${JWT_PASSPHRASE} -out $@ -aes256 4096

${JWT_PUBLIC_KEY}: ${JWT_PRIVATE_KEY}
	openssl rsa -passin pass:${JWT_PASSPHRASE} -pubout -in $< -out $@

api/.env.local: ${JWT_PRIVATE_KEY} ${JWT_PUBLIC_KEY}
	rm -f $@
	echo JWT_PASSPHRASE=$(JWT_PASSPHRASE) >> $@
	echo JWT_PRIVATE_KEY=`cat ${JWT_PRIVATE_KEY} | base64 | tr -d '\n'` >> $@
	echo JWT_PUBLIC_KEY=`cat ${JWT_PUBLIC_KEY} | base64 | tr -d '\n'` >> $@
	echo JWT_PRIVATE_KEY_FILE=%kernel.project_dir%/config/jwt/private.pem >> $@
	echo JWT_PUBLIC_KEY_FILE=%kernel.project_dir%/config/jwt/public.pem >> $@

clean/jwt:
	rm -rf $(JWT_DIR)


#=============================================================================#
#                           INIT DEPLOYMENT TARGETS
#=============================================================================#
ifndef DEPLOYMENT_ENV
DEPLOYMENT_ENV=staging
endif
ifndef HEROKU_TEAM
HEROKU_TEAM=YOUR_TEAM
endif
ifndef HEROKU_REGION
HEROKU_REGION=eu
endif
ifndef AWS_REGION
AWS_REGION=eu-west-3
endif

API_APP_NAME=$(HEROKU_TEAM)-api-$(DEPLOYMENT_ENV)
API_HOST=$(HEROKU_TEAM)-api-$(DEPLOYMENT_ENV).herokuapp.com
API_URL=https://$(API_HOST)

ADMIN_APP_NAME=$(HEROKU_TEAM)-admin-$(DEPLOYMENT_ENV)
ADMIN_BUCKET_URL=s3://$(ADMIN_APP_NAME)
ADMIN_URL=http://$(ADMIN_APP_NAME).s3-website.$(AWS_REGION).amazonaws.com

provision/api/app:
	heroku apps:create \
		--team $(HEROKU_TEAM) \
		--region $(HEROKU_REGION) \
		--remote $(DEPLOYMENT_ENV) \
		--buildpack https://github.com/negativetwelve/heroku-buildpack-subdir \
		--addons heroku-postgresql:hobby-dev \
		$(API_APP_NAME)
	mkdir -p $@

provision/api/environment: provision/api/app ${JWT_PUBLIC_KEY}
	heroku config:set -a $(API_APP_NAME) \
		APP_ENV=prod \
		APP_SECRET=$(shell openssl rand -base64 32) \
		CORS_ALLOW_ORIGIN=$(ADMIN_URL) \
		JWT_PASSPHRASE=$(JWT_PASSPHRASE) \
		JWT_PRIVATE_KEY=`cat ${JWT_PRIVATE_KEY} | base64 | tr -d '\n'` \
		JWT_PUBLIC_KEY=`cat ${JWT_PUBLIC_KEY} | base64 | tr -d '\n'` \
		TRUSTED_HOSTS=$(API_HOST)
	mkdir -p $@

provision/api: provision/api/environment

provision/admin:
	aws s3 mb --region $(AWS_REGION) s3://$(HEROKU_TEAM)-admin-staging
	aws s3 website $(ADMIN_BUCKET_URL) --index-document index.html --error-document index.html
	mkdir -p $@

provision: provision/api provision/admin

clean/provision:
	rm -rf provision

destroy/api:
	heroku apps:destroy -a $(API_APP_NAME)

destroy/admin:
	aws s3 rb $(ADMIN_BUCKET_URL) $(FORCE)

destroy: destroy/api destroy/admin


#=============================================================================#
#                           DEPLOYMENT TARGETS
#=============================================================================#
deploy/api:
	git push $(FORCE) $(DEPLOYMENT_ENV) HEAD:master

api/user:
	heroku run -r $(DEPLOYMENT_ENV) ./api/bin/console fos:user:create

api/token:
	curl -X POST \
		-H "Content-Type: application/json" $(API_URL)/login_check \
		-d '{"login":"$(EMAIL)","password":"$(PASSWORD)"}' 2>/dev/null

admin/build: install
	echo REACT_APP_API_ENTRYPOINT=$(API_URL) >> admin/.env.production.local
	docker-compose run admin /bin/sh -c 'yarn build'

deploy/admin: admin/build
	aws s3 sync $< $(ADMIN_BUCKET_URL) --acl public-read
	# aws cloudfront create-invalidation --distribution-id $(AWS_CLOUDFRONT_DISTRIBUTION_ID) --paths "/*"

deploy: deploy/api deploy/admin

clean/deploy:
	rm -f admin/.env.production.local
	rm -rf admin/build


#=============================================================================#
#                               DOCKER TARGETS
#=============================================================================#
clean/docker/volumes:
	docker system prune --volumes

clean/docker:
	docker system prune --all --force --volumes


#=============================================================================#
#                           GENERAL TARGETS
#=============================================================================#
update:
	# update-deps.sh

test:
	@echo "TODO test"

install: api/.env.local

start: test install
	docker-compose up -d

fixtures: start
	docker-compose exec php ./bin/console doctrine:fixtures:load

token:
	curl -X POST \
		-H "Content-Type: application/json" http://localhost:8080/login_check \
		-d '{"login":"$(EMAIL)","password":"$(PASSWORD)"}'

stop:
	docker-compose down

clean: stop clean/deploy clean/jwt clean/docker/volumes

clean/all: clean clean/provision clean/docker

all: start

.DEFAULT_GOAL := start
