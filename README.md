# Magento 2 Training Project

Magento 2.4.9 hands-on PHP refresher — from module basics to plugins, DI, and
beyond. Built on [Mark Shust's Docker for Magento](https://github.com/markshust/docker-magento).

## Setup

### Prerequisites

- Docker with 6+ GB RAM allocated
- [Mark Shust's Docker for Magento](https://github.com/markshust/docker-magento) installed
- Magento 2.4.9 source already downloaded into `src/`

### Start the environment

```bash
cd compose
docker compose -f compose.yaml -f compose.healthcheck.yaml -f compose.dev.yaml -f compose.dev-linux.yaml up -d
```

The `compose.dev-linux.yaml` overlay mounts the full `./src` directory into the container at `/var/www/html`, giving you live code editing on the host.

Site: `https://magento.test/`  
Admin: `https://magento.test/admin/` (user: `john.smith`, password: `password123`)

### Stop the environment

```bash
cd compose && docker compose down
```

## Development

### Common commands

Run these from the `compose/` directory:

```bash
# Run a bin/magento command
docker exec -it compose-phpfpm-1 bin/magento cache:clean

# Open a bash shell inside the container
docker exec -it compose-phpfpm-1 bash

# Check module status
docker exec -it compose-phpfpm-1 bin/magento module:status Training_Hello

# Run Composer
docker exec -it compose-phpfpm-1 composer install

# Lint a PHP file
docker exec -it compose-phpfpm-1 php -l app/code/Training/Hello/ViewModel/Hello.php

# Format code
docker exec -it compose-phpfpm-1 php vendor/bin/php-cs-fixer fix app/code/Training/Hello/

# Tail logs
docker exec -it compose-phpfpm-1 tail -f var/log/system.log
docker exec -it compose-phpfpm-1 tail -f var/log/exception.log
```

Shortcut scripts also exist in `compose/bin/` but may require TTY alignment:

```bash
./bin/magento cache:clean     # same as docker exec above
./bin/composer install
./bin/cli ls
```

### VS Code setup

Open `compose/src/` as the workspace root so Intelephense resolves `vendor/`
and all Magento classes. Extensions:

- **PHP Intelephense** — code intelligence
- **XML by Red Hat** — XML validation
- **php cs fixer** — formatting (binary at `vendor/bin/php-cs-fixer`)

---

## Training Tasks

All custom code lives in `src/app/code/Training/Hello/`.

### 1 — Module Skeleton

Created `registration.php` and `etc/module.xml`. Bare-minimum module that Magento
recognizes.

```
app/code/Training/Hello/
  registration.php
  etc/module.xml
```

### 2 — CLI Command

`bin/magento training:hello` prints a greeting. Introduces Symfony Console
commands and `di.xml` CommandList wiring.

```
app/code/Training/Hello/
  Console/HelloWorld.php
  etc/di.xml
```

**Test:** `docker exec -it compose-phpfpm-1 bin/magento training:hello`

### 3 — Frontend Route + Controller

`https://magento.test/training/hello/index` renders the `hello.phtml` template.
Covers `routes.xml`, controllers extending `Action`, and layout XML.

```
app/code/Training/Hello/
  etc/frontend/routes.xml
  Controller/Hello/Index.php
  view/frontend/layout/training_hello_index.xml
  view/frontend/templates/hello.phtml
```

**Test:** `curl -sk https://magento.test/training/hello/index`

### 4 — ViewModel

Replaced the plain template block with a ViewModel that passes dynamic data
(greeting with current time, product count via `ProductRepositoryInterface`).

```
app/code/Training/Hello/
  ViewModel/Hello.php
  view/frontend/templates/hello.phtml  (updated)
  view/frontend/layout/training_hello_index.xml  (updated)
```

**Key concept:** ViewModels must implement `Magento\Framework\View\Element\Block\ArgumentInterface`
(marker interface — no methods, just a gate). They're attached via layout XML:

```xml
<block class="Magento\Framework\View\Element\Template" ...>
    <arguments>
        <argument name="view_model" xsi:type="object">
            Training\Hello\ViewModel\Hello
        </argument>
    </arguments>
</block>
```

### 5 — Dependency Injection + Service Contracts

`bin/magento training:products` prints a random motivational quote and lists the
first 5 products from the catalog. Demonstrates DI with three injected dependencies.

```
app/code/Training/Hello/
  Service/GreetingService.php
  Console/Products.php
  etc/di.xml  (updated)
```

**Key concept:** Concrete classes (like `GreetingService`) need NO `di.xml` entry
to be injected — Magento auto-instantiates them. `di.xml` is for wiring interfaces
to implementations, adding to lists (CommandList), and registering plugins.

**Test:** `docker exec -it compose-phpfpm-1 bin/magento training:products`

### 6 — Plugins (Interceptors)

*In progress...*

### 7 — Model, Repository, DB Table

*Coming soon...*

### 8 — Events & Observers

*Coming soon...*

### 9 — Admin Grid Page

*Coming soon...*

### 10 — GraphQL Query + Resolver

*Coming soon...*

---

## Repository

https://github.com/helmutsdev-wq/magento2-training

```bash
git push -u origin main
```

## Notes

- `.gitignore` excludes `vendor/`, `generated/`, `var/`, `pub/static/`, `pub/media/`, `dev/`, and sensitive files
- Environment credentials in `env/` are local dev defaults (not production secrets)
- Always start with `compose.dev-linux.yaml` to get the full mounted `src/` directory
- If the page renders empty after a restart, flush the full page cache:
  `docker exec -it compose-phpfpm-1 bin/magento cache:flush`
