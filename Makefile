# Zycus Landing Page — developer task runner
#
# Usage: `make <target>` — or on Windows with make installed, same syntax.
# Quick Start: `make bootstrap && make dev`

.PHONY: help bootstrap dev lint test check docker clean

help:       ## Show this help
	@echo "Zycus Landing Page — available targets:"
	@echo ""
	@echo "  bootstrap    Install composer deps + copy .env.example -> .env"
	@echo "  dev          Start PHP built-in server at http://localhost:8000"
	@echo "  lint         php -l on every PHP file + node --check on every JS file"
	@echo "  test         Run the PHPUnit suite with --testdox output"
	@echo "  check        lint + test (equivalent to a CI pre-commit gate)"
	@echo "  docker       Build + run the Docker image (for Render / Fly / Koyeb)"
	@echo "  clean        Remove vendor/, composer cache, PHPUnit cache"
	@echo ""

bootstrap:  ## Install deps and seed .env
	composer install --prefer-dist --no-interaction --no-progress --optimize-autoloader
	@test -f .env || cp .env.example .env
	@echo ""
	@echo "Bootstrap complete. Edit .env then run: make dev"

dev:        ## Start the PHP built-in server
	@echo "Serving on http://localhost:8000 (Ctrl+C to stop)"
	php -S localhost:8000 -t public

lint:       ## Run PHP + JS syntax checks
	@echo "-> PHP lint"
	@find . -type f -name "*.php" -not -path "./vendor/*" -not -path "./storage/*" -exec php -l {} \;
	@echo ""
	@echo "-> JS syntax check"
	@for f in public/assets/js/*.js; do \
		echo "  node --check $$f"; \
		node --check "$$f" || exit 1; \
	done

test:       ## Run the PHPUnit suite
	vendor/bin/phpunit --testdox

check: lint test  ## Full gate: lint + test (run this before every push)
	@echo ""
	@echo "All checks passed."

docker:     ## Build + run the Docker image on :8080
	docker build -t zycus-landing:local .
	docker run --rm -p 8080:10000 --env-file .env zycus-landing:local

clean:      ## Wipe generated artefacts
	rm -rf vendor .phpunit.cache
	rm -f .phpunit.result.cache
	@echo "Cleaned."
