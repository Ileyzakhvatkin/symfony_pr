<?php

namespace App\Controller;


use App\Entity\Module;
use App\Entity\User;
use App\Repository\ModuleRepository;
use App\Services\LicenseLevelControl;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

#[IsGranted('ROLE_USER')]
class ModuleController extends AbstractController
{
    #[Route('/dashboard-modules/', name: 'modules')]
    public function modules(
        ModuleRepository $moduleRepository,
        LicenseLevelControl $licenseLevelControl,
        EntityManagerInterface $em,
        Request $request
    ): Response
    {
        $authUser = $this->getUser();
        $licenseInfo = $licenseLevelControl->update($authUser);
        $modules = $moduleRepository->modulesList($authUser->getId());

        $form = $this->createFormBuilder()
            ->add('title', TextType::class)
            ->add('code', TextareaType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $authUser */
            $authUser = $this->getUser();
            /** @var Module $module */
            $data = $form->getData();
//            $module
//                ->setTitle($data['title'])
//                ->setCode($data['code'])
//                ->setUser($authUser)
//                ->setUpdatedAt(Carbon::now())
//                ->setUpdatedAt(Carbon::now());
//            $em->persist($module);
//            $em->flush();
//            return $this->redirectToRoute('modules');
            return $this->json($data);
        }

        return $this->render('dashboard/modules.html.twig', [
            'itemActive' => 6,
            'licenseInfo' => $licenseInfo,
            'modules' => $modules,
            'form' => $form,
        ]);
    }


//    #[Route('/dashboard-deleted-modules/', name: 'deleted_module', methods: ['DELETE'])]
//    #[IsGranted('MANAGE', subject: 'module')]
//    public function deleteModule(): JsonResponse
//    {
//        return $this->json(json_encode(['module' => 'deleted']));
//    }
}
