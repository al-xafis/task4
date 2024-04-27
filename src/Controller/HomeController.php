<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface as ContainerContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private $em;
    private $security;
    protected $container;


    public function __construct(EntityManagerInterface $em, Security $security, ContainerContainerInterface $container)
    {
        $this->em = $em;
        $this->security = $security;
        $this->container = $container;
    }

    #[Route('/', name: 'app_home')]
    public function home(UserRepository $userRepository): Response
    {
        return $this->redirectToRoute('app_users');
    }

    #[Route('/users', name: 'app_users')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('home/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/action', name: 'app_action', methods: ["POST"])]
    public function action(Request $request, UserRepository $userRepository): Response
    {
        $params = $request->request->all();
        $action = $params['action'];
        unset($params['action']);
        $session = $request->getSession();

        $current_user_id = $this->security->getUser()->getId();

        if ($action === 'delete') {
            foreach($params as $user_id => $val) {
                $user = $userRepository->find($user_id);
                if ($current_user_id == $user->getId()) {
                    $this->redirectToRoute('app_logout');
                    $tokenStorage = $this->container->get('security.token_storage');
                    $tokenStorage->setToken(null);
                }
                $this->em->remove($user);
            }
        } elseif ($action === 'block') {
            foreach($params as $user_id => $val) {
                if ($current_user_id == $user_id) {
                    $session->set($current_user_id, 'Blocked');
                }
                $user = $userRepository->find($user_id);
                $user->setStatus('Blocked');
            }
        } elseif ($action === 'unblock') {
            foreach($params as $user_id => $val) {
                $user = $userRepository->find($user_id);
                $user->setStatus('Active');
            }
        }
        $this->em->flush();


        return $this->redirectToRoute('app_users');

    }

}
