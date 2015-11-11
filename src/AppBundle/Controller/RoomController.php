<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends Controller
{
    /**
     * @Route("/room/{id}", name="room")
     */
    public function indexAction($id)
    {
        $context = $this->get('security.token_storage');

        return $this->render('room.php', array(
            'roomID' => $id,
            'user' => $context->getToken()->getUsername(),
            'key' => $this->getParameter('jwt_secret')
        ));
    }
}
