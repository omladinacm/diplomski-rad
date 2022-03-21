#!/usr/bin/env bash

#By default, verbose output is not enabled
verbose=0

if [[ -z "${ENVIRONMENT_TYPE}" ]]; then
  ENVIRONMENT_TYPE=local
fi

for i in "$@"; do
  case ${i} in
  -e=* | --environment=*)
    ENVIRONMENT_TYPE="${i#*=}"
    shift # past argument=value
    ;;
  -m=* | --method=*)
    method="${i#*=}"
    shift # past argument=value
    ;;
  -v)
    verbose=1
    shift
    ;;
  -a=* | --argument=*)
    argument="${i#*=}"
    shift
    ;;
  *)
    # unknown option, ignore it
    ;;
  esac
done

return_check()
{
  if [[ $1 != 0 ]]; then
    echo "Error occurred, returned code $1"
  else
    echo "done"
  fi
}

create_env_file() {
  if [[ ! -f ".env" ]]; then
    cp .env.example .env
  fi
}

docker_compose_up()
{
  echo "Bringing up the ${ENVIRONMENT_TYPE} environment ... "
  if [ ! -d "logs/php" ]; then
    mkdir -p logs/php
  fi
  if [ ! -f "logs/php/fpm-error.log" ]; then
    touch logs/php/fpm-error.log
  fi
  if [ ! -f "logs/php/cli-error.log" ]; then
    touch logs/php/cli-error.log
  fi
  if [[ ${verbose} == 1 ]]; then
    docker-compose -f docker/docker-compose.yml -f docker/docker-compose."${ENVIRONMENT_TYPE}".yml -p diplomski up -d nginx
  else
    docker-compose -f docker/docker-compose.yml -f docker/docker-compose."${ENVIRONMENT_TYPE}".yml -p diplomski up -d nginx &> /dev/null
  fi

  return_check $?
}

docker_compose_down()
{
  echo "Bringing down the ${ENVIRONMENT_TYPE} environment ... "
  if [[ ${verbose} == 1 ]]; then
    docker-compose -f docker/docker-compose.yml -f docker/docker-compose."${ENVIRONMENT_TYPE}".yml -p diplomski down
  else
    docker-compose -f docker/docker-compose.yml -f docker/docker-compose."${ENVIRONMENT_TYPE}".yml -p diplomski down &> /dev/null
  fi

  return_check $?
}

docker_exec()
{
  docker exec -it php $1
}

composer()
{
  docker-compose run --rm composer $1

  return_check $?
}

composer_install()
{
  echo "Running composer install ... "
  if [[ ${verbose} == 1 ]]; then
    if [[ ${ENVIRONMENT_TYPE} == "production" ]]; then
      composer "install --no-dev"
    else
      composer "install"
    fi
  else
    if [[ ${ENVIRONMENT_TYPE} == "production" ]]; then
      composer "install --no-dev" &> /dev/null
    else
      composer "install" &> /dev/null
    fi
  fi

  return_check $?
}

composer_update()
{
  echo "Running composer update ... "
  if [[ ${verbose} == 1 ]]; then
    if [[ ${ENVIRONMENT_TYPE} == "production" ]]; then
      composer "update --no-dev"
    else
      composer "update"
    fi
  else
    if [[ ${ENVIRONMENT_TYPE} == "production" ]]; then
      composer "update --no-dev" &> /dev/null
    else
      composer "update" &> /dev/null
    fi
  fi

  return_check $?
}

composer_require()
{
  if [[ ${verbose} == 1 ]]; then
    composer "require $1"
  else
    composer "require $1" &> /dev/null
  fi

  return_check $?
}

composer_require_dev()
{
  if [[ ${verbose} == 1 ]]; then
    composer "require --dev $1"
  else
    composer "require --dev $1" &> /dev/null
  fi

  return_check $?
}

composer_dump_autoload()
{
  if [[ ${verbose} == 1 ]]; then
    composer "dump-autoload -o"
  else
    composer "dump-autoload -o" &> /dev/null
  fi

  return_check $?
}

load()
{
  create_env_file
  docker_compose_up
}

unload()
{
    docker_compose_down
}

helper()
{
  echo $""
  echo $"Usage: $0 args [options]"
  echo $"   $0 -m=load [-v]"
  echo $""
  echo $"Available arguments:"
  echo $""
  echo $"   -m, --method      Method to be executed, listed below"
  echo $"   -e, --environment Custom environment setting (one of: local, development, staging, production)"
  echo $"   -h, --help        Shows this :)"
  echo $""
  echo $"Available methods:"
  echo $""
  echo $"   env_create                Creates .env file from .env.example if it doesn't exist"
  echo $"   exec                      Execs into PHP Docker container"
  echo $"   load                      Bring up the environment"
  echo $"   unload                    Bring down the environment"
  echo $"   reload                    Reload the environment (basically two upper commands in one)"
  echo $"   composer                  Execute any composer command (must use -a option)"
  echo $"   composer_install          Execute composer install"
  echo $"   composer_update           Execute composer update"
  echo $"   composer_require          Adds required package through composer, uses -a option"
  echo $"   composer_require_dev      Adds required development package through composer, uses -a option"
  echo $"   dump_autoload             Execute composer optimized dump-autoload"
  echo $""
  echo $"Available options:"
  echo $""
  echo $"   -v                                Verbose mode"
  echo $"   -a=, --argument=\"method argument\" Additional argument used for methods that support it"
  echo $""
}

if [[ $1 == "--help" || $1 == "-h" ]]; then
  helper
fi

case "${method}" in
  create_env_file)
    create_env_file
    ;;
  exec)
    docker_exec bash
    ;;
  load)
    load
    ;;
  unload)
    unload
    ;;
  reload)
    unload
    load
    ;;
  composer)
    composer "${argument}"
    ;;
  composer_install)
    composer_install
    ;;
  composer_update)
    composer_update
    ;;
  composer_require)
    composer_require "${argument}"
    ;;
  composer_require_dev)
    composer_require_dev "${argument}"
    ;;
  dump_autoload)
    composer_dump_autoload
    ;;
  *)
    echo $""
    echo $"Run \"$0 --help\" to get list of commands"
    echo $""
    exit 1
esac
