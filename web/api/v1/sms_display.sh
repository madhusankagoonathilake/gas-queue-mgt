#!/bin/bash

phone=

if [ $# -gt 0 ]
  then
    phone=$1
fi
PROMPT_COMMAND='echo -ne "\033]0;${phone}\007"'
tailf "display${phone}.txt"