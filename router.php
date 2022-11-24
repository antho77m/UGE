<?php

$basepath = ""; // ex: "/app"
$request = $_SERVER['REQUEST_URI'];
$request = explode('?', $request)[0];
$request = str_replace($basepath, '', $request);

define('ROOT', __DIR__);

// charger tous les fichiers de du dossier functions
$functions = glob(dirname(__FILE__) . '/includes/functions/*.php');
foreach ($functions as $function) {
    require_once $function;
}

// génération du lien canonique de la page demandée

const CANONICAL_QUERY_STRINGS = ['seed'];

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $request;
$queryStrings = explode('&', $_SERVER['QUERY_STRING']);
$canonicalQueryStrings = [];

foreach ($queryStrings as $key => $queryString) {
    $queryString = explode('=', $queryString);
    if (in_array($queryString[0], CANONICAL_QUERY_STRINGS)) {
        $canonicalQueryStrings[] = $queryString[0] . '=' . $queryString[1];
    }
}

$canonical = $url . "?hl=fr" . (sizeof($canonicalQueryStrings) > 0 ? "&" . implode('&', $canonicalQueryStrings) : '');

function loadAsset($page, $type)
{
    global $basepath;
    if ($type == 'css' && file_exists("src/styles/pages/" . $page . ".css")) {
        return "<link rel=\"stylesheet\" href=\"$basepath/src/styles/pages/" . $page . ".css?v=" . md5_file("src/styles/pages/" . $page . ".css") . "\">";
    }
    if ($type == 'js' && file_exists("src/scripts/pages/" . $page . ".js")) {
        return "<script src=\"$basepath/src/scripts/pages/" . $page . ".js?v=" . md5_file("src/scripts/pages/" . $page . ".js") . "\" defer></script>";
    }
    return "";
}

function loadPage($page, $with_head = true)
{
    global $basepath, $og, $canonical;
    $path =  "pages" . DIRECTORY_SEPARATOR . $page . ".php";
    $headPath = "pages" . DIRECTORY_SEPARATOR . $page . ".head.php";
    if (file_exists($path)) {
        if ($with_head) {
            $appendHead = loadAsset($page, 'css');
            $externalHeadFile = file_exists($headPath) ? $headPath : false;
            include "includes/head.php";
        }
        include($path);
        if ($with_head) {
            $appendBody = loadAsset($page, 'js');
            include "includes/endbody.php";
        }
    }
}

switch ($request) {
    case "/":
        $og = (object) [
            "title" => "Accueil",
            "description" => "Accueil du site"
        ];
        loadPage("index");
        break;
    case "/login":
        $og = (object) [
            "title" => "Connexion",
            "description" => "Connexion au site"
        ];
        loadPage("login");
        break;
    case "/verify_login":
        $og = (object) [
            "title" => "Authentification...",
            "description" => "Authentification de l'utilisateur"
        ];
        loadPage("verify_login");
        break;
    case "/home":
        session_start();
        if (isset($_SESSION['niveau'])) {
            (($_SESSION['niveau'] == 1) ? $level = 'Mon compte' : (($_SESSION['niveau'] == 2) ? $level = 'Gestion des comptes' : $level = 'Acceuil'));
        }
        $og = (object) [
            "title" => $level,
            "description" => "Acceuil du site"
        ];
        loadPage("home");
        break;
    case "/client":
        $og = (object) [
            "title" => "Gestion des comptes",
            "description" => "Gestion des comptes"
        ];
        loadPage("client");
        break;
    case "/product_owner":
        $og = (object) [
            "title" => "Acceuil",
            "description" => "Acceuil du site"
        ];
        loadPage("product_owner");
        break;
    default:
        // retourner le fichier par défaut
        return false;
}
