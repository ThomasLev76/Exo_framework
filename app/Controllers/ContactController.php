<?php

namespace Controllers;

use App\Request;
use App\Response;
use App\View;

class ContactController
{
    private string $file = __DIR__ . '/../../data/data.json';

    public function index(Request $request): Response
    {
        $data = $this->lire();
        $view = new View(__DIR__ . '/../../views');
        return Response::html($view->renderWithLayout('contacts/index', [
            'pageTitle' => 'Mes contacts',
            'activePage' => 'contacts',
            'contacts' => $data->contacts,
        ]));
    }

    public function create(Request $request): Response
    {
        return Response::redirect('/contacts#nouveau-contact');
    }

    public function show(Request $request, array $params): Response
    {
        $id = (int) $params['id'];
        foreach ($this->lire()->contacts as $c) {
            if ($c->id === $id) {
                return $this->contactPage($c);
            }
        }
        return $this->contactPage(null);
    }

    private function contactPage(?object $contact): Response
    {
        $view = new View(__DIR__ . '/../../views');
        return Response::html($view->renderWithLayout('contacts/show', [
            'pageTitle' => $contact->nom ?? 'Contact introuvable',
            'activePage' => 'contacts',
            'contact' => $contact,
        ]), $contact ? 200 : 404);
    }

    public function add(Request $request): Response
    {
        $nom = trim($request->input('nom', ''));
        $email = trim($request->input('email', ''));
        $telephone = trim($request->input('telephone', ''));
        $data = $this->lire();

        if ($nom !== '') {
            $id = max(array_column($data->contacts, 'id') ?: [0]) + 1;
            $data->contacts[] = (object) ['id' => $id, 'nom' => $nom, 'email' => $email, 'telephone' => $telephone];
            $this->sauver($data);
        }

        header('Location: /contacts', true, 303);
        exit;
    }

    public function delete(Request $request, array $params): Response
    {
        $id = (int) $params['id'];
        $data = $this->lire();

        $data->contacts = array_values(
            array_filter($data->contacts, fn($c) => $c->id !== $id)
        );
        $this->sauver($data);

        header('Location: /contacts', true, 303);
        exit;
    }

    private function lire(): object
    {
        return json_decode(file_get_contents($this->file));
    }

    private function sauver(object $data): void
    {
        file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
