#!/bin/bash
if [ "$#" -ne 2 ];then
  echo "Usage: $(basename $0) start|stop|kill|restart|status [worker-number]"
  exit 1
fi

APP_NAME=lego-worker.php
LOG_FILE=/mnt/d/Projects/PHP/home/react-async-slim/logs/worker$2.log

pgrep -u $USER -f "$APP_NAME --worker $2$" > /dev/null #(-l will list the full command with args)
RUNNING="$?"
MY_PID=$(pgrep -u $USER -f "$APP_NAME --worker $2$")
#echo "$APP_NAME===>>$MY_PID"
case "$1" in
  start)
    if [ "$RUNNING" -eq 1 ]; then
      echo -e "Worker is not running! Starting it..."
      nohup php $APP_NAME --worker $2 > $LOG_FILE 2>&1 &
    else
      echo "Worker is running. Processes:"
      pgrep -f "$APP_NAME --worker $2$"
    fi
    ;;
  stop)
    if [ "$RUNNING" -eq 1 ]; then
      echo -e "Worker is not running; nothing to stop!"
    else
      echo -e "Worker is running; killing it!"
      kill -15 $MY_PID
    fi
  ;;
  kill)
    if [ "$RUNNING" -eq 1 ]; then
      echo -e "Worker is not running; nothing to stop!"
    else
      echo -e "Worker is running; killing it!"
      kill $MY_PID
    fi
  ;;
  status)
  if [ "$RUNNING" -eq 1 ]; then
    echo -e "Worker is not running $2!"
  else
    echo "Worker is running. Processes:"
    pgrep -u $USER -f "$APP_NAME --worker $2$" –l
  fi
  ;;
  restart)
    if [ "$RUNNING" -eq 1 ]; then
      echo -e "Worker is not running; starting it!"
      nohup php $APP_NAME --worker $2 > $LOG_FILE 2>&1 &
    else
      echo -e "Worker is running; restarting it!"
      kill -15 $MY_PID
      nohup php $APP_NAME --worker $2 > $LOG_FILE 2>&1 &
    fi
  ;;
  *)
    echo "Usage: $(basename $0) start|stop|restart|status --worker [worker-number]"
    exit 1
esac
