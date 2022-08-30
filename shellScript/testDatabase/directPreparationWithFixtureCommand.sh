#!/bin/bash

bin/console --env=test doctrine:schema:drop -n -q --force --full-database
bin/console --env=test doctrine:database:drop -n -q --force
bin/console --env=test doctrine:database:create --if-not-exists
bin/console --env=test doctrine:schema:create

read -p 'groups (attention-> for entering one and only one group enter=> --group=groupName,
 but for multiple groups enter=> --group=groupName1 --group=groupName2 --group=groupName3 ... ): ' groups

bin/console --env=test doctrine:fixtures:load $groups