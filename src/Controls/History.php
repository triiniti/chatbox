<?php
namespace ChatBox\App\Controls;
use League\Plates\Engine;

class History
{
    private $templates;

    public function __construct()
    {
        $this->templates = new Engine('templates');
    }

    public function renderPage(): string
    {
        // Render a template
        return $this->templates->render('history', []);
    }
}


?>