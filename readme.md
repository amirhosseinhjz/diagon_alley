initialize project step by step:

1) `$docker-compose up --build`
2) open a new terminal and run:
`$docker-compose exec diagonalley bash`
3) `$composer install` 
4) for setting up testDatabase run:
`$bin/console --env=test doctrine:database:create --if-not-exists`
5) for building schema:
`bin/console --env=test doctrine:schema:create`

Structure:
1. Logics should be in your services and should be called from your controllers.
2. Use DTOs for validating and converting data for entities.
3. Place your queries in your repository.
4. Place your tests in `tests` folder.
5. Add more here if you think we need!