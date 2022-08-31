#!/bin/bash

bin/console --env=test doctrine:database:create --if-not-exists
bin/console --env=test doctrine:schema:drop -n -q --force --full-database
bin/console --env=test doctrine:database:drop -n -q --force
bin/console --env=test doctrine:database:create --if-not-exists
bin/console --env=test doctrine:schema:create
