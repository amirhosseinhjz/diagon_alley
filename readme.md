initialize project step by step:

1) `$docker-compose up --build`

2) open a new terminal and run:
`$docker-compose exec diagonalley bash`

3) `$composer install` 

Swagger configuration:
1. Place all your routes under /api route.
2. Use `OpenApi\Attributes as OA` in your controllers.
3. Use it as attribute on your controllers.
4. Read https://symfony.com/bundles/NelmioApiDocBundle/current/index.html for more details.
4) for setting up testDatabase run:
`$bin/console doctrine:database:create --if-not-exists`
for building schema:
`bin/console --env=test doctrine:schema:create`

6) use lchrusciel/api-test-case package for functional (controllers)
testing, to do test extend JsonApiTestCase class

7) Generate the SSL keys:
`php bin/console lexik:jwt:generate-keypair --overwrite`
 use --overwrite option to overwrite keys because they already exist 
 
Structure:
1. Logics should be in your services and should be called from your controllers.
2. Use DTOs for validating and converting data for entities.
3. Place your queries in your repository.
4. Place your tests in `tests` folder.
5. Add more here if you think we need!

Delete "composer.lock" and run `composer update` if you pull the main project to make a new "composer.lock" for all dependencies.
