<?php

class Vhost {

	var $vhost;
	var $conf;

	function __construct($name = 'error') {
		$this->vhost = $name;
		$this->conf ='<VirtualHost *:80>
	ServerName '. $this->vhost . '.domain.com
	ServerAlias '. $this->vhost . '
	ServerAdmin webmaster@localhost
	DocumentRoot /var/www/'. $this->vhost . '
</VirtualHost>';
	}

	//functions
	public function setup() {
		if ($this->vhost == 'error') {
			return false;
		} else {
			shell_exec('mkdir /var/www/' . $this->vhost);
			shell_exec('echo "' . $this->conf . '" | sudo tee /etc/apache2/sites-available/' . $this->vhost . '.conf > /dev/null');
			$this->enable();
			return true;
		}
	}

	public function delete() {
		if ($this->vhost == 'error') {
			return false;
		} else {
			$this->disable();
			shell_exec('sudo trash-put /var/www/' . $this->vhost);
			shell_exec('sudo trash-put /etc/apache2/sites-available/' . $this->vhost . '.conf');
			return true;
		}
	}

	//Apache functions
	public function reload() {
		shell_exec('sudo service apache2 reload');
	}

	public function disable() {
		shell_exec('sudo a2dissite ' . $this->vhost . '.conf');
		$this->reload();
	}

	public function enable() {
		shell_exec('sudo a2ensite ' . $this->vhost . '.conf');
		$this->reload();
	}
}
?>
