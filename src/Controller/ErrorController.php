<?php

declare(strict_types=1);

namespace Green\TomTroc\Controller;

use Exception;
use Green\TomTroc\Core\Http\Response;
use Green\TomTroc\Core\Settings\Settings;
use Green\TomTroc\Core\View\View;

class ErrorController
{
    public function showErrorPage(Exception $errorException): Response
    {
        switch ($errorException->getCode()) {
            case ($errorException->getCode() >= 500 && $errorException->getCode() < 600):
                $errorMessage = 'Internal Error';
                $errorCode = 500;
                break;
            case ($errorException->getCode() >= 400 && $errorException->getCode() < 500):
                $errorMessage = $errorException->getMessage();
                $errorCode = $errorException->getCode();
                break;
            default:
                $errorMessage = 'Unknown Error';
                $errorCode = 500;
                break;
        }

        $data = [
            'errorMessage' => $errorMessage,
        ];
        $errorView = new View('Oups');
        $errorPageContent = $errorView->render($data, ROOT_DIR . '/templates/error.php');

        return new Response($errorPageContent, $errorCode);
    }

    public function showDebugPage(Exception $errorException): Response
    {
        $data = [
            'errorException' => $errorException,
        ];
        $errorView = new View('Oups');

        $errorPageContent = $errorView->render($data, ROOT_DIR . '/templates/debug.php');

        return new Response($errorPageContent, (int) $errorException->getCode());
    }

    public function handleException(Exception $exception): Response
    {
        if (Settings::get(Settings::APP_DEV, false) === true) {
            $response = $this->showDebugPage($exception);
        } else {
            $response = $this->showErrorPage($exception);
        }

        return $response;
    }
}
