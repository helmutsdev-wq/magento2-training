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

Hooks into any public method of any class without modifying the original.
Three types: `before`, `after`, and `around`.

```
app/code/Training/Hello/
  Plugin/ProductSaveLog.php
  etc/di.xml  (updated)
```

**Key concept:** The plugin method name follows the convention `before{MethodName}`,
`after{MethodName}`, or `around{MethodName}`. The first parameter is always
`$subject` (the intercepted object). Return all original arguments as an array
in `before` plugins.

**Test:** Save a product via admin, then check `var/log/training_product_saves.log`

### 7 — Custom DB Table + Model + Repository

Creates a custom flat table via declarative schema (`db_schema.xml`), a model
extending `AbstractModel`, a resource model, a collection, and a repository
implementing the service contract pattern.

```
app/code/Training/Hello/
  etc/db_schema.xml
  Model/Quote.php
  Model/ResourceModel/Quote.php
  Model/ResourceModel/Quote/Collection.php
  Api/Data/QuoteInterface.php
  Api/QuoteRepositoryInterface.php
  Model/QuoteRepository.php
  etc/di.xml  (updated)
```

**Key concept:** In Magento 2.3+, database tables are declared via `db_schema.xml`
(no more InstallSchema scripts). The repository pattern separates data access
from business logic via interfaces (service contracts).

**Test:** `docker exec -it compose-phpfpm-1 bin/magento setup:db:status`

### 8 — Events & Observers

Dispatches a custom event and observes a core event. Demonstrates the
`events.xml` configuration and observer classes.

```
app/code/Training/Hello/
  Observer/LogPageView.php
  Observer/ProductSaved.php
  etc/events.xml
  etc/di.xml  (updated)
```

**Key concept:** Events are Magento's pub/sub system. Dispatch in your code with
`$eventManager->dispatch('event_name', ['data' => $data])`. Observe by mapping
the event name to an observer class in `events.xml`.

**Test:** `docker exec -it compose-phpfpm-1 tail -f var/log/system.log`

### 9 — Admin Grid Page

Creates an admin page with a UI component listing grid. Covers admin routes,
menu registration, UI component XML, and a data provider.

```
app/code/Training/Hello/
  etc/adminhtml/routes.xml
  etc/adminhtml/menu.xml
  Controller/Adminhtml/Quotes/Index.php
  view/adminhtml/layout/training_hello_quotes_index.xml
  view/adminhtml/ui_component/training_hello_quotes_listing.xml
  Model/ResourceModel/Quote/Grid/Collection.php
```

**Key concept:** Admin grids use Magento's UI Components framework — declarative
XML that defines columns, filters, sorting, and data sources. No manual HTML
table rendering.

**Test:** Navigate to Admin → Training → Quotes

### 10 — GraphQL Query + Resolver

Exposes a custom GraphQL query to fetch data from the module. Covers
`schema.graphqls`, resolver classes, and the GraphQL module wiring.

```
app/code/Training/Hello/
  etc/graphql/
    di.xml
  Model/Resolver/Quotes.php
  etc/schema.graphqls
  etc/module.xml  (updated)
```

**Key concept:** Magento 2.3+ uses declarative GraphQL. Define the schema in
`.graphqls` files and implement resolvers via DI. The resolver receives `$context`
and `$value` per GraphQL conventions.

**Test:** `curl -sk https://magento.test/graphql -H "Content-Type: application/json" -d '{"query":"{ trainingQuotes { items { quote } } }"}'`

### 11 — Debugging Practice

Practical debugging session covering:
- Reading `var/log/system.log` and `var/log/exception.log`
- Following stack traces to find root causes
- Using the Xdebug container (`compose-phpfpm-xdebug-1`) with breakpoints
- `bin/magento dev:di:info` to inspect DI configuration
- `n98-magerun2 dev:console` for interactive PHP

**Test:** Trigger a deliberate error in the module, then trace and fix it.

### 12 — Architecture Walk-Through

No-code interview prep. Questions answered with Magento architecture patterns:
- "How would you customize the checkout?"
- "How do you add a new step to order placement?"
- "How would you integrate a third-party payment gateway?"
- "Explain the request flow from URL to rendered page."
- "How do you handle a module upgrade with database changes?"

**Key concept:** Magento interviews focus on knowing WHERE to make changes
(plugins, observers, preferences, layouts) and WHY — not memorizing exact APIs.

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
