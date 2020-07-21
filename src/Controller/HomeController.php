<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 *
 * @Route("/", name="app_home_")
 */
class HomeController extends AbstractController
{
    /**
     * @var UsersRepository
     */
    private UsersRepository $usersRepository;

    /**
     * HomeController constructor.
     * @param UsersRepository $usersRepository
     */
    public function __construct (UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index (): Response
    {
        return $this->render('home/index.html.twig', [
            'users' => $this->usersRepository->findBy([], ['username' => 'ASC'])
        ]);
    }
}
