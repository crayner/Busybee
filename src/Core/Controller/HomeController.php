<?php
namespace App\Core\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage");
     */
    public function home()
    {
        return $this->redirectToRoute('home');
    }
}