.PHONY: install clean help

# Default target
help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install dependencies
	composer install

clean: ## Clean generated files
	rm -rf build/ vendor/

build: ## Prepare for distribution
	composer install --no-dev --optimize-autoloader

dev-setup: ## Set up development environment
	composer install
	@echo "Development environment ready!"
