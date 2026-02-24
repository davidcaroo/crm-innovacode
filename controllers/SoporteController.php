<?php
// controllers/SoporteController.php
require_once __DIR__ . '/BaseController.php';

class SoporteController extends BaseController
{
    public function index()
    {
        $this->view('soporte/index');
    }
}
