run:
	docker exec -it laravel bash -c "\
		php artisan key:generate --force && \
		php artisan migrate --force && \
		php artisan db:seed --force \
	"
	@echo "Successfully!"
