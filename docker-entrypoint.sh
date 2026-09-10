#!/bin/sh
set -e

# Volumul Railway se monteaza peste public/uploads si ascunde tot ce era
# in imagine la calea aia. De aceea pozele demo stau in fixtures/images/
# si se copiaza aici la fiecare pornire.
mkdir -p public/uploads/ads public/uploads/profiles

# -n = no-clobber: nu suprascrie fisiere existente, deci pozele incarcate
# de utilizatori prin site raman intacte la redeploy.
if [ -d fixtures/images/ads ]; then
    cp -rn fixtures/images/ads/. public/uploads/ads/ 2>/dev/null || true
fi

if [ -d fixtures/images/profiles ]; then
    cp -rn fixtures/images/profiles/. public/uploads/profiles/ 2>/dev/null || true
fi

chmod -R 777 public/uploads var 2>/dev/null || true

exec "$@"
