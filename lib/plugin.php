<?php
namespace Podlove\Beta;

class Plugin {

	public $slug;
	public $title;
	public $file;

	private $branches;

	public function __construct($slug, $data) {
		$this->slug  = $slug;
		$this->title = $data['title'];
		$this->file  = $data['file'];

		$this->branches = [];
		foreach ($data['branches'] as $branch_slug => $branch) {
			$this->branches[] = new Branch(
				$branch_slug, 
				isset($branch['state'])       ? $branch['state']       : 'alpha', 
				isset($branch['description']) ? $branch['description'] : ''
			);
		}
	}

	public function branches() {
		return $this->branches;
	}

}
