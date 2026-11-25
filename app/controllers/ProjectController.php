<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Models\ProjectModel;
use App\Services\ProjectService;
use App\Utils\Session;
use App\Utils\Validator;

final class ProjectController extends Controller
{
    public function __construct(
        private ProjectModel $projects = new ProjectModel(),
        private ProjectService $service = new ProjectService()
    ) {
    }

    public function index(): void
    {
        $userId = $this->requireLogin();
        $this->view('app/my-projects', [
            'page'      => 'app-my-projects',
            'title'     => 'Mes projets',
            'projects'  => $this->projects->listByUser($userId),
            'languages' => Config::get('languages', []),
        ]);
    }

    public function store(): void
    {
        $userId    = $this->requireLogin();
        $languages = Config::get('languages', []);
        $in        = ['name' => $this->input('name'), 'language' => $this->input('language')];

        $v = new Validator($in);
        $v->required('name', 'Le nom du projet')->length('name', 'Le nom du projet', 1, 50);
        $errors = $v->errors();
        if (!array_key_exists($in['language'], $languages)) {
            $errors['language'] = 'Choisissez une langue dans la liste.';
        }

        if (!$errors) {
            $result = $this->service->create($userId, $in['name'], $in['language']);
            if ($result['ok']) {
                Session::flash('success', 'Projet créé.');
                $this->redirect('/my-projects');
            }
            $errors['name'] = $result['error'];
        }
        Session::flash('errors', $errors);
        Session::flash('old', $in);
        $this->redirect('/my-projects');
    }

    public function destroy(array $params): void
    {
        $userId = $this->requireLogin();
        // Le modèle filtre par user_id : impossible de supprimer le projet d'un autre auteur.
        if ($this->projects->delete((int) $params['project_id'], $userId)) {
            Session::flash('success', 'Projet supprimé.');
        } else {
            Session::flash('error', 'Projet introuvable.');
        }
        $this->redirect('/my-projects');
    }
}
