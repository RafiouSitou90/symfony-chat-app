<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Service\Mercure\MercureCookieGenerator;
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
     * @var MercureCookieGenerator
     */
    private MercureCookieGenerator $mercureCookieGenerator;

    /**
     * HomeController constructor.
     * @param UsersRepository $usersRepository
     * @param MercureCookieGenerator $mercureCookieGenerator
     */
    public function __construct (UsersRepository $usersRepository, MercureCookieGenerator $mercureCookieGenerator)
    {
        $this->usersRepository = $usersRepository;
        $this->mercureCookieGenerator = $mercureCookieGenerator;
    }
    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index (): Response
    {
        /** @var Users $user */
        $user = $this->getUser();

        $response = $this->render('home/index.html.twig', [
            'users' => $this->usersRepository->findBy([], ['username' => 'ASC'])
        ]);

        $response->headers->setCookie($this->mercureCookieGenerator->generate($user));

        return $response;
    }

}
