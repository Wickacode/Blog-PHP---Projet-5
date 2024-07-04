<?php
require "../vendor/autoload.php";
use Controllers\ArticlesController;
use Controllers\ContactController;
use Controllers\HomeController;
use Controllers\AuthController;

//Activation de la super globale
session_start();


//Définition d'une constante contenant le dossier racine du projet
define('ROOT', dirname(__DIR__));

$action = $_GET["action"] ?? "index";

switch ($action) {
    case "index":
        $controller = new HomeController();
        $controller->index();
        break;

    case "articles":
        $controller = new ArticlesController();
        $controller->listArticles();
        break;
    
    case "article":
        $controller = new ArticlesController();
        $controller->Article();
        break;

    case "createArticle":
        $controller = new ArticlesController();
        $controller->createArticle();
        break;

    case "createComment":
        $controller = new ArticlesController();
        $controller -> createComment();
        break;

    case "approveComments":
        $controller = new ArticlesController();
        $controller -> approveComments();

    case "contact":
        $controller = new ContactController();
        $controller->contact();
        break;

    case "register":
        $controller = new AuthController();
        $controller->register();
        break;

    case "login":
        $controller = new AuthController();
        $controller->login();
        break;

    case "logout":
        $controller = new AuthController();
        $controller->logout();
        break;

    case "administration":
        $controller = new AdminController();
        $controller->administration();
        break;
    
    case "userManagement":
        $controller = new UsersController();
        $controller->userManagement();
        break;

}

