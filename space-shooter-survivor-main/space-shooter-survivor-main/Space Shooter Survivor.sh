#!/bin/sh
echo -ne '\033c\033]0;Space Shooter Survivor\a'
base_path="$(dirname "$(realpath "$0")")"
"$base_path/Space Shooter Survivor.x86_64" "$@"
