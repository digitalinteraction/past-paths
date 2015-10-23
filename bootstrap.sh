#!/usr/bin/env bash
export DEBIAN_FRONTEND=noninteractive
#sudo -sHu vagrant env
swapsize=512

# does the swap file already exist?
grep -q "swapfile" /etc/fstab
# if not then create it
if [ $? -ne 0 ]; then
  echo 'Swapfile not found. Adding swapfile.'
  fallocate -l ${swapsize}M /swapfile
  chmod 600 /swapfile
  mkswap /swapfile
  swapon /swapfile
  echo '/swapfile none swap defaults 0 0' >> /etc/fstab
 fi


#if ! apache2 -v > /dev/null 2>&1; then

  # apt-key adv --keyserver keyserver.ubuntu.com --recv 7F0CEB10
  # echo 'deb http://downloads-distro.mongodb.org/repo/ubuntu-upstart dist 10gen' | tee /etc/apt/sources.list.d/10gen.list

  apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10

  echo "deb http://repo.mongodb.org/apt/ubuntu trusty/mongodb-org/3.0 multiverse" | tee /etc/apt/sources.list.d/mongodb-org-3.0.list

  # get neo stuff
  wget -O - http://debian.neo4j.org/neotechnology.gpg.key | apt-key add -
  echo 'deb http://debian.neo4j.org/repo stable/' > /etc/apt/sources.list.d/neo4j.list

  apt-get update -q
  apt-get install -q -y -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" \
     git make mongodb-org=3.0.1 htop php5 libapache2-mod-php5 php5-mcrypt neo4j=2.1.8 php5-mongo php5-gd

  a2enmod rewrite
  echo -e "\n--- Allowing Apache override to all ---\n"
  sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/apache2.conf
  service apache2 restart
# fi

if ! [ -L /var/www/html ]; then
  rm -rf /var/www/html
  ln -fs /vagrant /var/www/html
fi

#defaut passwords / add default config file from copy
# if ! [ -e /vagrant/config/local.js ]; then
#   echo 'Copying default Bootlegger settings.'
#   cp /vagrant/config/local.example /vagrant/config/local.js
# fi
#setup app config

#exit 0;
