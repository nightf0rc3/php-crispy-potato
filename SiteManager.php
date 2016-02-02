<?php
include 'dns.php';
include 'vhost.php';


class SiteManager {

	var $zone;
	var $cname;
	var $data;
	var $id;

	function __construct($mail, $token) {
		$this->dns = new Dns($mail, $token);
	}

	public function setZone($zone) {
		$this->dns->zone = $zone;
	}

	public function setSite($site) {
		$this->cname = strtolower($site);
		$this->vhost = new Vhost($this->cname);
		$this->data = array(
			'type'    => 'CNAME',
			'name'    => $this->cname,
			'content' => 'domain.com',
			'ttl'     => 120
		);
	}

	public function getSite() {
		return $this->cname;
	}

	public function getAllSites() {
		return array_diff(scandir('/etc/apache2/sites-available/'), array('..', '.'));
	}

	public function getId() {
		$this->id = $this->dns->info($this->cname . '.domain.com')->result[0]->id;
	}

	//Create new Site
	public function create() {

		if ($this->dns->create($this->data)->success) {
			$this->vhost->setup();
			return true;
		} else {
			return false;
		}
	}

	//Delete Site
	public function delete() {
		$this->getId();
		$this->dns->delete($this->id);
		$this->vhost->delete();
	}
}
?>
