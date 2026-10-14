<?php

namespace Controllers;

use App\Request;
use App\Response;

class ArticleController
{

    public function index(Request $request): Response
    {
        $articles = [
            ['id' => 1, 'title' => 'Introduction au PHP'],
            ['id' => 2, 'title' => 'Comprendre MVC'],
            ['id' => 3, 'title' => 'Les design patterns'],
        ];
        $html = '<h1>Articles</h1><ul>';
        foreach ($articles as $a) {
            $html .= "<li><a href=\"/articles/{$a['id']}\">{$a['title']}</a></li>";
        }
        $html .= '</ul>';
        return Response::html($html);
    }

    public function show(Request $request, array $params): Response
    {
        $id = (int) $params['id'];
        return Response::html("<h1>Article n°$id</h1><p>Contenu de l'article...</p>");
    }

    public function add(Request $request): Response
    {
        $title = $request->input('title', 'Sans titre');
        return Response::json(['created' => true, 'title' => $title], 201);
    }
}
