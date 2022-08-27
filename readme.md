initialize project step by step:

1) $docker-compose up --build

2) open a new terminal and run ---> $docker-compose exec diagonalley bash 

3) $composer install 

Swagger configuration:
1. Place all your routes under /api route.
2. Use `OpenApi\Attributes as OA` in your controllers.
3. Use it as attribute on your controllers.
4. Read https://symfony.com/bundles/NelmioApiDocBundle/current/index.html for more details.
