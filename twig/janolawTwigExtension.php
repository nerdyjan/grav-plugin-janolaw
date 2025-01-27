<?php
namespace Grav\Plugin;

use Grav\Common\Grav;
use Grav\Common\Twig\Extension\GravExtension;

class janolawTwigExtension extends GravExtension
{

    public function getName()
    {
        return 'janolawTwigExtension';
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('janolaw', [$this, 'getCachedContent'])
        ];
    }

    public function getCachedContent($type)
    {
        $cache = Grav::instance()['cache'];
        $cache_id = 'janolaw_' . $type . '_' . $this->grav['language']->getActive();

        if ($cache->fetch($cache_id)) {
            return $cache->fetch($cache_id);
        } else {
            $output = $this->getJanolawContent($type);
            $cache->save($cache_id, $output);
            return $output;
        }
    }

    private function getJanolawContent($type)
    {
        $base_url = 'https://www.janolaw.de/agb-service/shops';
        $config = $this->config->get('plugins.janolaw');
        $language = $this->grav['language']->getActive();
        if ($language == 'en') { $language = 'gb'; }

        $error = "<div class='notices red'><p><b>Server Error:</b>&nbsp;&nbsp;&nbsp;&nbsp;Document <u>".$type."</u> not found! Please contact your website administrator!</p></div>";
        $error_lang = "<div class='notices red'><p><b>Server Error:</b>&nbsp;&nbsp;&nbsp;&nbsp;Document <u>".$type."</u> with language <u>".$this->grav['language']->getLanguage()."</u> not found! Please contact your website administrator!</p></div>";
        
        # v1 check
        $headers = @get_headers($base_url.'/'.$config['userid'].'/'.$config['shopid'].'/'.$type.'_include.html');
        if($headers[0] == 'HTTP/1.1 200 OK') {
            return file_get_contents($base_url.'/'.$config['userid'].'/'.$config['shopid'].'/'.$type.'_include.html');
        }
        
        $error = $error_lang;
        
        # v2 check
        $headers = @get_headers($base_url.'/'.$config['userid'].'/'.$config['shopid'].'/'.$language.'/'.$type.'_include.html');
        if($headers[0] == 'HTTP/1.1 200 OK') {
            $this->config->set('plugins.janolaw.version', 'v2');
            return file_get_contents($base_url.'/'.$config['userid'].'/'.$config['shopid'].'/'.$language.'/'.$type.'_include.html');
        }

        # v3 check
        $headers = @get_headers($base_url.'/'.$config['userid'].'/'.$config['shopid'].'/'.$language.'/'.$type.'.pdf');
        if($headers[0] == 'HTTP/1.1 200 OK') {
            return file_get_contents($base_url.'/'.$config['userid'].'/'.$config['shopid'].'/'.$language.'/'.$type.'_include.html');
        }

        return $error;
    }
}