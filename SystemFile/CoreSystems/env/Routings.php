<?php

class Routings
{
    public function Viewer($uri, $view, $param=array(), $methodlimitation="no")
    {
        if(is_array($param)) {
            $template = file_get_contents(dirname(__FILE__) . "/" . "../../../Surface/View_Template/Juice/" . $view . ".html");
        }
    }
}