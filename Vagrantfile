# Vagrant file for provisioning Bootlegger Server Dev Environment
# Run:
#   vagrant up
#
# Website will be accessible on port localhost:8080
# Mongo is accessible on port 27018
#
# You will need to edit your AWS, Google and Facebook credentials in
#   /vagrant/config/local.js
#
# To restart server:
#   vagrant ssh
#   pm2 restart app


# -*- mode: ruby -*-
# vi: set ft=ruby :
Vagrant.configure(2) do |config|
  config.vm.hostname = "Past Paths Dev"
  config.vm.box = "ubuntu/trusty64"
  config.vm.hostname ="past-paths-dev"
  config.vm.provider "virtualbox" do |v|
    v.memory = 2048
  end
  config.vm.network "forwarded_port", guest: 80, host: 2200, auto_correct:true
  config.vm.network "forwarded_port", guest: 7474, host: 7575, auto_correct:true
  config.vm.network "forwarded_port", guest: 27017, host: 27018, auto_correct:true
  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"
  config.vm.provision :shell, path: "bootstrap.sh"
  config.vm.synced_folder ".", "/vagrant", owner: "www-data", group: "www-data"

  config.vm.post_up_message = "Past Paths Server Development Environment Started. View the README.md file for more information."
end

