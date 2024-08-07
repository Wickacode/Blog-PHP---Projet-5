<?php
namespace Controllers;

use Models\Entity\Article;
use Models\Repository\ArticlesRepository;
use Models\Entity\Comment;
use Models\Repository\CommentsRepository;


class ArticlesController extends Controller
{
    //Déclaration de la propriété privée 
    public function listArticles()
    {
        $repository = new ArticlesRepository();
        $articles = $repository->getArticles();
        echo $this->render('portfolio.html.twig', ["articles" => $articles]);
    }

    public function Article()
    {
        if (!empty($_GET['id_article'])) {
            $id = $_GET["id_article"];
            $repositoryArticles = new ArticlesRepository();
            $article = $repositoryArticles->getArticle($id);
            $repositoryComments = new CommentsRepository();
            $comments = $repositoryComments->getComments($id);
            echo $this->render('article.html.twig', ["article" => $article, "comments" => $comments]);
        } else {
            echo $this->render('error404.html.twig');
        }
    }

    public function createArticle()
    {
        $date = date('Y-m-d');
        //Gestion de la récupération et de la sauvegarde des données 
        if (isset($_POST['submitArticle'])) {
            if (isset($_FILES["uploadfile"]["name"]) && !empty($_FILES["uploadfile"]["name"])) {
                $filename = $_FILES["uploadfile"]["name"];
                $folder = "C:/wamp64/www/Blog-php/public/img/upload/" . $filename;
                if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $folder))
                    ;
                $stockImg = "http://localhost/BLOG-PHP/public/img/upload/" . $filename;
            }
            if (!empty($_POST['title']) && !empty($_POST['chapo']) && !empty($_POST['content']) && !empty($_POST['altImage'])) {
                $article = new Article([
                    "title" => $_POST['title'],
                    "chapo" => $_POST['chapo'],
                    "content" => $_POST['content'],
                    "date_publication" => $date,
                    "date_modification" => $date,
                    "image" => $stockImg,
                    "alt" => $_POST['altImage']
                ]);
                $repository = new ArticlesRepository();
                $repository->addArticle($article);
                $validAdd = "L'article a bien été ajouté";
            } else {
                $error = "Tous les champs doivent être saisis";
                echo $this->render('createArticle.html.twig', ["error" => $error]);
            }
        }
        if (isset($validAdd)) {
            echo $this->render('createArticle.html.twig', ["validAdd" => $validAdd]);
        } else {
            echo $this->render('createArticle.html.twig');
        }

    }

    public function getListArticles()
    {
        $repository = new ArticlesRepository();
        $articlesPublish = $repository->getArticlesPublishAdmin();
        $articlesNoPublish = $repository->getArticlesNoPublishAdmin();

        echo $this->render('listArticles.html.twig', ["articlesPublish" => $articlesPublish, "articlesNoPublish" => $articlesNoPublish]);
    }

    public function publishAdminArticle()
    {
        if (!empty($_GET['id_article'])) {
            $idArticle = $_GET['id_article'];
            $repository = new ArticlesRepository();
            $repository->publishArticleSQL($idArticle);
            return header('location: http://localhost/BLOG-PHP/public/index.php?action=getListArticles');

        } else {
            echo $this->render('error404.html.twig');
        }
    }

    public function formUpdateArticle()
    {
        if (!empty($_GET['id_article'])) {
            $idArticle = $_GET['id_article'];
            $repositoryArticle = new ArticlesRepository();
            $article = $repositoryArticle->getArticle($idArticle);
            echo $this->render('updateArticle.html.twig', ["article" => $article]);

        } else {
            echo $this->render('error404.html.twig');
        }
    }

    public function updateArticle()
    {
        $date = date('Y-m-d');
        $repositoryArticle = new ArticlesRepository();
        if (isset($_POST['modifyArticle'])) {
            if (isset($_FILES["uploadfile"]["name"]) && !empty($_FILES["uploadfile"]["name"])) {
                $filename = $_FILES["uploadfile"]["name"];
                $folder = "C:/wamp64/www/Blog-php/public/img/upload/" . $filename;
                if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $folder))
                    ;
                $stockImg = "http://localhost/BLOG-PHP/public/img/upload/" . $filename;
            }
            if (!empty($_POST['title'] && !empty($_POST['chapo']) && !empty($_POST['content']) && !empty($_POST['altImage']))) {
                $idArticle = $_GET['id_article'];
                $article = $repositoryArticle->getArticle($idArticle);
                if (isset($stockImg)) {
                    $newImage = $stockImg;
                } else {
                    $newImage = $article->getImage();
                }
                $article = new Article([
                    "title" => $_POST['title'],
                    "chapo" => $_POST['chapo'],
                    "content" => $_POST['content'],
                    "date_modification" => $date,
                    "image" => $newImage,
                    "alt" => $_POST['altImage']
                ]);
                $repositoryArticle->changeArticle($idArticle, $article);
                $articlesPublish = $repositoryArticle->getArticlesPublishAdmin();
                $articlesNoPublish = $repositoryArticle->getArticlesNoPublishAdmin();
                echo $this->render('listArticles.html.twig', ["articlesPublish" => $articlesPublish, "articlesNoPublish" => $articlesNoPublish]);

            }
        }
    }

    public function deleteArticle()
    {
        if (!empty($_GET['id_article'])) {
            $idArticle = $_GET['id_article'];
            $repositoryArticle = new ArticlesRepository();
            $repositoryComment = new CommentsRepository();
            $repositoryComment->removeCom($idArticle);
            $repositoryArticle->removeArticle($idArticle);
            $articlesPublish = $repositoryArticle->getArticlesPublishAdmin();
            $articlesNoPublish = $repositoryArticle->getArticlesNoPublishAdmin();
            echo $this->render('listArticles.html.twig', ["articlesPublish" => $articlesPublish, "articlesNoPublish" => $articlesNoPublish]);
        } else {
            echo $this->render('error404.html.twig');
        }
    }


    public function createComment()
    {
        if (isset($_POST['submitComment'])) {
            if (!empty($_POST['message'])) {
                $comment = new Comment([
                    "content" => $_POST['message'],
                    "id_user" => (int) $_SESSION['user']['idUser'],
                    "id_article" => (int) $_GET["id_article"]
                ]);

                $repositoryComment = new CommentsRepository();
                $repositoryComment->addComment($comment);
                $validAddComment = "Le commentaire est en cours de validation";
                return header('location: http://localhost/BLOG-PHP/public/index.php?action=article&id_article=' . $_GET["id_article"]);

            } else {
                $error = "Tous les champs doivent être saisis";
                echo $this->render('createArticle.html.twig', ["error" => $error]);
            }
        }
    }
    public function listComments()
    {
        $commentsRepository = new CommentsRepository();
        $comments = $commentsRepository->listComments();

        echo $this->render('listComments.html.twig', ["comments" => $comments]);
    }

    public function approveCom()
    {
        $idCom = $_GET["id_comment"];
        $commentsRepository = new CommentsRepository();
        $commentsRepository->validCom($idCom);
        return header('location: http://localhost/BLOG-PHP/public/index.php?action=listComments');
    }

    public function deleteCom()
    {
        $idCom = $_GET["id_comment"];
        $commentsRepository = new CommentsRepository();
        $commentsRepository->refuseCom($idCom);
        return header('location: http://localhost/BLOG-PHP/public/index.php?action=listComments');

    }

}