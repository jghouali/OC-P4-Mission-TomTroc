#!/usr/bin/env bash

set -u

BASE_URL="https://tomtroc.exos.localhost"
VNU_BIN="$HOME/.local/bin/vnu"

COOKIE_FILE="$(mktemp)"
TMP_DIR="$(mktemp -d)"

trap 'rm -f "$COOKIE_FILE"; rm -rf "$TMP_DIR"' EXIT

LOGGED_URLS=(
  "/"
  "/available-books"
  "/available-books?search=nar"
  "/book-detail?bookId=16"
  "/book-add"
  "/book-edit?bookId=2"
  "/profile?memberId=15"
  "/my-box?toUser=15"
  "/my-box"
  "/my-profile"
  "/login"
  "/register"
)
NOTLOGGED_URLS=(
  "/"
  "/available-books"
  "/available-books?search=nar"
  "/book-detail?bookId=16"
  "/profile?memberId=15"
  "/login"
  "/register"
)

CURL_OPTS=(-k -sS -L)

echo "Login..."

curl "${CURL_OPTS[@]}" -c "$COOKIE_FILE" -b "$COOKIE_FILE" \
  "$BASE_URL/login" \
  -d "email=Alexlecture@mail.com" \
  -d "password=password" \
  -o /dev/null

echo
echo "VNU validation logged..."
echo

for page in "${LOGGED_URLS[@]}"; do
  filename="$(echo "$page" | sed 's#^/##; s#[^A-Za-z0-9._-]#_#g')"

  if [ -z "$filename" ]; then
    filename="home"
  fi

  html_file="$TMP_DIR/${filename}.html"

  echo "=================================================="
  echo "URL: $BASE_URL$page"

  http_code="$(curl "${CURL_OPTS[@]}" -b "$COOKIE_FILE" \
    -w "%{http_code}" \
    -o "$html_file" \
    "$BASE_URL$page")"

  echo "HTTP: $http_code"

  if [ "$http_code" -lt 200 ] || [ "$http_code" -ge 400 ]; then
    echo "RESULT: HTTP ERROR"
    echo
    continue
  fi

  if output="$("$VNU_BIN" --no-stream "$html_file" 2>&1)"; then
    if [ -z "$output" ]; then
      echo "RESULT: VNU OK"
    else
      echo "RESULT: VNU OUTPUT"
      echo "$output"
    fi
  else
    echo "RESULT: VNU FAILED"
    echo "$output"
  fi

  echo
done

echo
echo "VNU validation not logged..."
echo

for page in "${NOTLOGGED_URLS[@]}"; do
  filename="$(echo "$page" | sed 's#^/##; s#[^A-Za-z0-9._-]#_#g')"

  if [ -z "$filename" ]; then
    filename="home"
  fi

  html_file="$TMP_DIR/${filename}.html"

  echo "=================================================="
  echo "URL: $BASE_URL$page"

  http_code="$(curl "${CURL_OPTS[@]}"  \
    -w "%{http_code}" \
    -o "$html_file" \
    "$BASE_URL$page")"

  echo "HTTP: $http_code"

  if [ "$http_code" -lt 200 ] || [ "$http_code" -ge 400 ]; then
    echo "RESULT: HTTP ERROR"
    echo
    continue
  fi

  if output="$("$VNU_BIN" --no-stream "$html_file" 2>&1)"; then
    if [ -z "$output" ]; then
      echo "RESULT: VNU OK"
    else
      echo "RESULT: VNU OUTPUT"
      echo "$output"
    fi
  else
    echo "RESULT: VNU FAILED"
    echo "$output"
  fi

  echo
done