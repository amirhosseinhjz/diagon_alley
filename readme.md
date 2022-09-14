initialize project step by step:

1. `$docker-compose up --build`

2. open a new terminal and run:
   `$docker-compose exec diagonalley bash`

3. `$composer install`

Swagger configuration:

1. Place all your routes under /api route.
2. Use `OpenApi\Attributes as OA` in your controllers.
3. Use it as attribute on your controllers.
4. Read https://symfony.com/bundles/NelmioApiDocBundle/current/index.html for more details.
5. for authentication ,click on Authorize button and paste a valid user bearer token.

Set Database environment:

1. To configure the database for testing run, `bin/console test:prepare-test-database`.
   It will remove and remake the whole test-database.
2. Extends created base class for testing,The base class is --> `BaseJsonApiTestCase`

JWT ssl keys:

1. Generate the SSL keys:
   `php bin/console lexik:jwt:generate-keypair --overwrite`
   use --overwrite option to overwrite keys because they already exist

Structure:

1. Logics should be in your services and should be called from your controllers.
2. Use DTOs for validating and converting data for entities.
3. Place your queries in your repository.
4. Place your tests in `tests` folder.
5. Add more here if you think we need!

Delete "composer.lock" and run `composer update` if you pull the main project to make a new "composer.lock" for all dependencies.

Cron jobs:
1. Make migration
2. Add new commands: bin/console cron:create
3. Start using the bundle: nohup bin/console cron:start -b &
- More details: https://github.com/Cron/Symfony-Bundle
