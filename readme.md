initialize project step by step:

1) `$docker-compose up --build`

2) open a new terminal and run:
`$docker-compose exec diagonalley bash`
3) `$composer install` 
4) for setting up testDatabase run:
`$bin/console doctrine:database:create --if-not-exists`
for building schema:
`bin/console --env=test doctrine:schema:create`

