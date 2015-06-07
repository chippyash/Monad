#!/bin/bash
cd ~/Projects/chippyash/source/Monad
vendor/phpunit/phpunit/phpunit -c test/phpunit.xml --testdox-html contract.html test/
tdconv -t "Chippyash Monad" contract.html docs/Test-Contract.md
rm contract.html

