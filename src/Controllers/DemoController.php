<?php

namespace Hummel\PhpFrame\Controllers;

use Hummel\PhpFrame\Models\User;
use Hummel\PhpFrame\Controllers\BaseController;
use App\Models\Comment;


class DemoController extends BaseController

{
    private User $user;

    public function index()
    {

        $users = (new User())->getAll();

        $comment = new Comment();
        $comment->getOneBy(['email' => "prout"]);

        $this->render('src/views/users.php', [
            'users' => $users,
            'superprout' => $comment,
        ]);
    }

    public function showOnce()
    {
        $id = $this->route->getParams()['id'];
        // extract($this->route->getParams(), EXTR_OVERWRITE);

        $user = new User();
        $user->getOneBy(['id' => (int)$id]);

        $comment = new Comment();
        $comment->getOneBy(['email' => "prout"]);

        $this->render('src/views/template.php', [
            'viewUser' => $user,
            'superprout' => $comment,
        ]);
    }

    public function routesTestAction() {

        extract($this->route->getParams(), EXTR_OVERWRITE);

        var_dump($name, $number, $id);

    }


}
