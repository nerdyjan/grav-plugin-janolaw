<?php

namespace Grav\Plugin;
use \Grav\Common\Plugin;

class JanolawPlugin extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'onTwigExtensions' => ['onTwigExtensions', 0]
        ];
    }

    public function onTwigExtensions()
    {
        require_once(__DIR__ . '/twig/janolawTwigExtension.php');
        $this->grav['twig']->twig->addExtension(new janolawTwigExtension());
    }
}