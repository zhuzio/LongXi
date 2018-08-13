<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class IntroductionController extends Controller {
	public function aboutus() {
		return view('web.introduction.aboutus', ['data' => $this->getContent('aboutus')]);
	}
	public function userhelp() {
		return view('web.introduction.userhelp', ['data' => $this->getContent('userhelp')]);
	}

	public function license() {
        return view('web.introduction.userhelp', ['data' => $this->getContent('license')]);
	}

	private function getContent($key) {
		return @file_get_contents(resource_path() . '/introduction/' . $key);
	}
}