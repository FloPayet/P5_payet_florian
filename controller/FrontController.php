<?php

namespace Controller;

class FrontController extends Controller {
    public function indexAction() {
        return $this->render('test.html.twig');
    }
}