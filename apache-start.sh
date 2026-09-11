#!/bin/bash

echo "===== NETTOYAGE APACHE AU DEMARRAGE ====="

a2dismod mpm_event mpm_worker mpm_prefork || true

rm -f /etc/apache2/mods-enabled/mpm_event.*
rm -f /etc/apache2/mods-enabled/mpm_worker.*
rm -f /etc/apache2/mods-enabled/mpm_prefork.*

a2enmod mpm_prefork

echo "===== MPM ACTIF ====="
ls -la /etc/apache2/mods-enabled/*mpm*
apache2ctl -M 2>&1 | grep mpm

echo "===== DEMARRAGE APACHE ====="
exec apache2-foreground
