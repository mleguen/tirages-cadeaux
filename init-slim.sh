#!/usr/bin/env bash

# Sauvegarde le paramétrage local non versionné
mkdir api_backup
mv api/.env* api_backup/
mkdir api_backup/auth
mv api/var/auth/{.gitignore,*.prod} api_backup/auth/
mv api/var/doctrine api_backup/

rm -r api

# See https://github.com/slimphp/Slim-Skeleton
# required php extensions apt packages: php-xml php-mbstring
composer create-project slim/slim-skeleton api

# Restaure le paramétrage local non versionné
mv api_backup/{auth,doctrine} api/var/
mv api_backup/.env* api/
rmdir api_backup
echo '/.env*' >> api/.gitignore

function lowercase {
  s=$1
  echo "$(tr '[:upper:]' '[:lower:]' <<< ${s:0:1})${s:1}"
}

ACTION_DIR=api/src/Application/Actions
DOMAIN_DIR=api/src/Domain
PERSISTENCE_DIR=api/src/Infrastructure/Persistence
ACTION_TEST_DIR=api/tests/Application/Actions
DOMAIN_TEST_DIR=api/tests/Domain
PERSISTENCE_TEST_DIR=api/tests/Infrastructure/Persistence

function copy-entity {
  name=$1
  lname=$(lowercase $name)
  
  sed -Ee "s/(\/\/.*)User/\1Xxx/;s/InMemory/Doctrine/;/User/{h;s/User/${name}/g;p;g}" -i api/app/repositories.php
  
  mkdir $ACTION_DIR/$name
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $ACTION_DIR/User/UserAction.php > $ACTION_DIR/$name/${name}Action.php
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $ACTION_DIR/User/ListUsersAction.php > $ACTION_DIR/$name/List${name}Action.php
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $ACTION_DIR/User/ViewUserAction.php > $ACTION_DIR/$name/View${name}Action.php
  
  mkdir $DOMAIN_DIR/$name
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $DOMAIN_DIR/User/User.php > $DOMAIN_DIR/$name/$name.php
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $DOMAIN_DIR/User/UserNotFoundException.php > $DOMAIN_DIR/$name/${name}NotFoundException.php
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $DOMAIN_DIR/User/UserRepository.php > $DOMAIN_DIR/$name/${name}Repository.php
  
  mkdir $PERSISTENCE_DIR/$name
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $PERSISTENCE_DIR/User/InMemoryUserRepository.php > $PERSISTENCE_DIR/$name/InMemory${name}Repository.php
  
  mkdir $ACTION_TEST_DIR/$name
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $ACTION_TEST_DIR/User/ListUserActionTest.php > $ACTION_TEST_DIR/$name/List${name}ActionTest.php
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $ACTION_TEST_DIR/User/ViewUserActionTest.php > $ACTION_TEST_DIR/$name/View${name}ActionTest.php
  
  mkdir $DOMAIN_TEST_DIR/$name
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $DOMAIN_TEST_DIR/User/UserTest.php > $DOMAIN_TEST_DIR/$name/${name}Test.php
  
  mkdir $PERSISTENCE_TEST_DIR/$name
  sed -Ee "s/Users?/$name/g;s/(\\\$| |->)user/\1$lname/g" $PERSISTENCE_TEST_DIR/User/InMemoryUserRepositoryTest.php > $PERSISTENCE_TEST_DIR/$name/InMemory${name}RepositoryTest.php
}

function delete-entity-template {
  sed -Ee "/User/d" -i api/app/repositories.php

  rm -f $ACTION_DIR/User/ListUsersAction.php
  rm -f $ACTION_DIR/User/UserAction.php
  rm -f $ACTION_DIR/User/ViewUserAction.php
  rmdir $ACTION_DIR/User

  rm -f $DOMAIN_DIR/User/User.php
  rm -f $DOMAIN_DIR/User/UserNotFoundException.php
  rm -f $DOMAIN_DIR/User/UserRepository.php
  rmdir $DOMAIN_DIR/User

  rm -f $PERSISTENCE_DIR/User/InMemoryUserRepository.php
  rmdir $PERSISTENCE_DIR/User

  rm -f $ACTION_TEST_DIR/User/ListUserActionTest.php
  rm -f $ACTION_TEST_DIR/User/ViewUserActionTest.php
  rmdir $ACTION_TEST_DIR/User

  rm -f $DOMAIN_TEST_DIR/User/UserTest.php
  rmdir $DOMAIN_TEST_DIR/User

  rm -f $PERSISTENCE_TEST_DIR/User/InMemoryUserRepositoryTest.php
  rmdir $PERSISTENCE_TEST_DIR/User
}

MIDDLEWARE_DIR=api/src/Application/Middleware 

function copy-middleware {
  name=$1
  sed -Ee s/SessionMiddleware/${name}Middleware/g $MIDDLEWARE_DIR/SessionMiddleware.php > $MIDDLEWARE_DIR/${name}Middleware.php
  sed -Ee "s/((.*)SessionMiddleware(.*))/\1\n\2${name}Middleware\3/" -i api/app/middleware.php
}

function delete-middleware-template {
  rm -f $MIDDLEWARE_DIR/SessionMiddleware.php
  sed -Ee "/SessionMiddleware/d" -i api/app/middleware.php
}

copy-middleware Auth
delete-middleware-template

copy-entity Connexion
copy-entity Utilisateur
copy-entity Occasion
copy-entity Idee
copy-entity Resultat
delete-entity-template
