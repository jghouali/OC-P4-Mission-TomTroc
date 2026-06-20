<?php

declare(strict_types=1);

namespace Green\TomTroc\Core\View;

use RuntimeException;

class View
{
    private string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function renderPage(string $header, string $content, string $footer)
    {
        $title = $this->title;

        $template = TEMPLATE_DIR . '/main.template.php';
        if (file_exists($template)) {
            ob_start();
            require(TEMPLATE_DIR . '/main.template.php');
            $page = ob_get_clean();
        } else {
            throw new RuntimeException('Template file ' . TEMPLATE_DIR . '/header.template.php not found');
        }

        return $page;
    }

    public function header()
    {
        $title = $this->title;

        $template = TEMPLATE_DIR . '/header.template.php';
        if (file_exists($template)) {
            ob_start();
            include($template);
            return ob_get_clean();
        } else {
            throw new RuntimeException('Template file ' . TEMPLATE_DIR . '/header.template.php not found');
        }
    }

    public function footer()
    {
        $template = TEMPLATE_DIR . '/footer.template.php';
        if (file_exists($template)) {
            ob_start();
            include($template);
            return ob_get_clean();
        } else {
            throw new RuntimeException('Template file ' . TEMPLATE_DIR . '/footer.template.php not found');
        }
    }

    public function render(array $data, string $template)
    {
        if (file_exists($template)) {
            ob_start();
            include($template);
            $content = ob_get_clean();
        } else {
            throw new RuntimeException('Template file ' . TEMPLATE_DIR . '/' . $template . ' not found');
        }

        $header = $this->header();

        $footer = $this->footer();

        $result = $this->renderPage($header, $content, $footer);

        return $result;
    }
}
