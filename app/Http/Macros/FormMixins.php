<?php

namespace App\Http\Macros;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Module;

class FormMixins
{
    public function image_data()
    {
        return function ($image, $contents = false) {
            if (!$contents) {
                $contents = file_get_contents($image);
            } else {
                $contents = $image;
            }

            return $contents ? 'data:image/jpeg;base64,' . base64_encode($contents) : '';
        };
    }

    public function nav_link()
    {
        return function ($url, $text) {
            //$class = ( Request::is($url) || Request::is($url.'/*') || Request::is($url2.'/*') ) ? ' class="active"' : '';
            $class = (Request::is($url) || Request::is($url . '/*')) ? ' class="active"' : '';
            $title = trans("texts.$text") . Utils::getProLabel($text);

            return '<li' . $class . '><a href="' . URL::to($url) . '">' . $title . '</a></li>';
        };
    }

    public function tab_link()
    {
        return function ($url, $text, $active = false) {
            $class = $active ? ' class="active"' : '';

            return '<li' . $class . '><a href="' . URL::to($url) . '" data-toggle="tab">' . $text . '</a></li>';
        };
    }

    public function menu_link()
    {
        return function ($type) {
            $types = $type . 's';
            $Type = ucfirst($type);
            $Types = ucfirst($types);
            $class = (Request::is($types) || Request::is('*' . $type . '*')) && !Request::is('*settings*') ? ' active' : '';

            return '<li class="dropdown ' . $class . '">
                    <a href="' . URL::to($types) . '" class="dropdown-toggle">' . trans("texts.$types") . '</a>
                   </li>';
        };
    }

    public function flatButton()
    {
        return function ($label, $color) {
            return '<input type="button" value="' . trans("texts.{$label}") . '" style="background-color:' . $color . ';border:0 none;border-radius:5px;padding:12px 40px;margin:0 6px;cursor:hand;display:inline-block;font-size:14px;color:#fff;text-transform:none;font-weight:bold;"/>';
        };
    }

    public function emailViewButton()
    {
        return function ($link = '#', $entityType = ENTITY_INVOICE) {
            return view('partials.email_button')
                ->with([
                    'link' => $link,
                    'field' => "view_{$entityType}",
                    'color' => '#777',
                ])
                ->render();
        };
    }

    public function emailPaymentButton()
    {
        return function ($link = '#', $label = 'pay_now') {
            return view('partials.email_button')
                ->with([
                    'link' => $link,
                    'field' => $label,
                    'color' => '#36c157',
                ])
                ->render();
        };
    }

    public function breadcrumbs()
    {
        return function ($status = false) {

            $str = '<ol class="breadcrumb">';

            // Get the breadcrumbs by exploding the current path.
            $basePath = Utils::basePath();
            $parts = explode('?', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
            $path = $parts[0];

            if ($basePath != '/') {
                $path = str_replace($basePath, '', $path);
            }
            $crumbs = explode('/', $path);

            foreach ($crumbs as $key => $val) {
                if (is_numeric($val)) {
                    unset($crumbs[$key]);
                }
            }

            $crumbs = array_values($crumbs);
            for ($i = 0; $i < count($crumbs); $i++) {
                $crumb = trim($crumbs[$i]);
                if (!$crumb) {
                    continue;
                }
                if ($crumb == 'company') {
                    return '';
                }

                if (!Utils::isNinjaProd() && $module = Module::find($crumb)) {
                    $name = mtrans($crumb);
                } else {
                    $name = trans("texts.$crumb");
                }

                if ($i == count($crumbs) - 1) {
                    $str .= "<li class='active'>$name</li>";
                } else {
                    if (count($crumbs) > 2 && $crumbs[1] == 'proposals' && $crumb != 'proposals') {
                        $crumb = 'proposals/' . $crumb;
                    }
                    $str .= '<li>' . link_to($crumb, $name) . '</li>';
                }
            }

            if ($status) {
                $str .= $status;
            }

            return $str . '</ol>';
        };
    }

    public function human_filesize()
    {
        return function ($bytes, $decimals = 1) {
            $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            $factor = floor((strlen($bytes) - 1) / 3);
            if ($factor == 0) {
                $decimals = 0;
            }// There aren't fractional bytes
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
        };
    }
}