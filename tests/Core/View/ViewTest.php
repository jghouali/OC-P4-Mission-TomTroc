<?php

declare(strict_types=1);

namespace Tests\Core\View;

use Green\TomTroc\Core\View\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    public function testRender()
    {
        $data = [
            'message' => 'Hello',
        ];
        $testView = new View('Test View');

        $this->assertMatchesRegularExpression(
            '/contenthere/',
            $testView->render($data, ROOT_DIR . '/tests/Core/View/test.template.php')
        );
    }
    public function testRenderWithData()
    {
        $data = [
            'message' => 'Hellohere',
        ];
        $testView = new View('Test View');

        $this->assertMatchesRegularExpression(
            '/<title>Test View<\/title>/',
            $testView->render($data, ROOT_DIR . '/tests/Core/View/test.template.php')
        );
        $this->assertMatchesRegularExpression(
            '/contenthere/',
            $testView->render($data, ROOT_DIR . '/tests/Core/View/test.template.php')
        );
    }
}
