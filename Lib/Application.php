<?php

namespace Lib;

class Application
{
    protected $routing;

    public function __construct($dbConfiguration)
    {
        Persistence::configure(
            $dbConfiguration['host'],
            $dbConfiguration['db'],
            $dbConfiguration['user'],
            $dbConfiguration['pass']
        );
        $this->routing = new Routing();
    }

    public function get($path, $controller)
    {
        $this->routing->add($path, $controller);
    }

    public function put($path, $controller)
    {
        $this->routing->add($path, $controller, ['PUT']);
    }

    public function delete($path, $controller)
    {
        $this->routing->add($path, $controller, ['DELETE']);
    }

    public function run()
    {
        $controller = $this->routing->match($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

        if (!$controller) {
            http_response_code(404);
            echo 'Address not found';
            exit();
        }

        $result = $controller();
        $content = '';

        switch (true) {
            case $result instanceof \Exception:
                http_response_code($result->getCode());
                $content = $result->getMessage();
                break;

            case $result instanceof \JsonSerializable:
            case is_array($result):
                $content = json_encode($result);
                break;

            case is_int($result):
                http_response_code($result);
                break;
        }

        echo $content;
        exit();
    }
}
