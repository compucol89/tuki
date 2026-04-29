SHELL := /bin/bash

.PHONY: help up down restart ps logs build clean

help:
	@echo "Tuki (Docker Compose)"
	@echo ""
	@echo "make up       - Levanta todo (app+db+adminer)"
	@echo "make down     - Baja todo"
	@echo "make restart  - Reinicia todo"
	@echo "make ps       - Estado de servicios"
	@echo "make logs     - Logs (app)"
	@echo "make build    - Build de imagen app"
	@echo "make clean    - Baja y borra orphans (seguro)"

up:
	docker compose up -d --build --remove-orphans

down:
	docker compose down

restart: down up

ps:
	docker compose ps

logs:
	docker compose logs -f --tail=200 app

build:
	docker compose build app

clean:
	docker compose down --remove-orphans
