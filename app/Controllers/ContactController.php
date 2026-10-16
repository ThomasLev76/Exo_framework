<?php

namespace Controllers;

use App\Request;
use App\Response;

class ContactController
{

    public function index(Request $request): Response
    {
        $contact = [
            ['id' => 1, 'nom' => 'Keenan'],
            ['id' => 2, 'nom' => 'Nathan'],
            ['id' => 3, 'nom' => 'Gabin'],
        ];
        $html = '<h1>Contacts</h1><ul>';
        foreach ($contact as $c) {
            $nom = htmlspecialchars($c['nom'], ENT_QUOTES, 'UTF-8');
            $html .= "<li><a href=\"/contacts/{$c['id']}\">{$nom}</a></li>";
            $html .= "<button action=\"/contacts/{$c['id']}/delete\" method=\"post\" class=\"delete-button\" data-id=\"{$c['id']}\">Supprimer</button>";
        }
        $html .= '</ul>';

        // Bouton + formulaire d'ajout
        $html .= '
            <form action="/contacts" method="post">
                <input type="text" name="nom" placeholder="Nom du contact" required>
                <button type="submit">Ajouter</button>
            </form>
        ';


        return Response::html($html);
    }

    public function show(Request $request, array $params): Response
    {
        $id = (int) $params['id'];
        return Response::html("<h1>Contact n°$id</h1><p>Description du contact...</p>");
    }

    public function add(Request $request): Response
    {
        $nom = $request->input('nom', 'Sans titre');
        return Response::json(['created' => true, 'nom' => $nom], 201);
    }

     public function delete(Request $request): Response
    {
        $nom = $request->input('nom', 'Sans titre');
        return Response::json(['deleted' => true, 'nom' => $nom], 201);
    }
}