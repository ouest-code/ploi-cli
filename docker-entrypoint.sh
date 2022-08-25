#!/bin/sh

isCommand() {
  if [ "$1" = "sh" ]; then
    return 1
  fi

  php ploi help --no-interaction "$1" > /dev/null 2>&1
}

# check if the first argument passed in looks like a flag
if [ "${1#-}" != "$1" ]; then
  set -- /usr/bin/tini -- php ploi "$@"
# check if the first argument passed in is composer
elif [ "$1" = 'ploi' ]; then
  set -- /usr/bin/tini -- php "$@"
# check if the first argument passed in matches a known command
elif isCommand "$1"; then
  set -- /usr/bin/tini -- php ploi "$@"
fi

exec "$@"
