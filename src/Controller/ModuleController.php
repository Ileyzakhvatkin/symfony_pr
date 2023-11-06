<?php

namespace App\Controller;

use App\Repository\ModuleRepository;
use App\Services\LicenseLevelControl;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
        Request $request,
        PaginatorInterface $paginator
    ): Response
    {
        $authUser = $this->getUser();
        $licenseInfo = $licenseLevelControl->update($authUser);

        $pagination = $paginator->paginate(
            $moduleRepository->modulesListQuery($authUser->getId()),
            $request->query->getInt('page', 1), /*page number*/
            5
        );

        $formModule = $this->createFormBuilder()
            ->add('title', TextType::class)
            ->add('code', TextareaType::class)
            ->getForm();

        $formModule->handleRequest($request);

        if ($formModule->isSubmitted() && $formModule->isValid()) {
            $moduleRepository->create($this->getUser(), $formModule->getData());
            $this->addFlash('flash_message', 'Модуль успешно создан');

            return $this->redirectToRoute('modules');
        }

        return $this->render('dashboard/modules.html.twig', [
            'itemActive' => 6,
            'licenseInfo' => $licenseInfo,
            'modules' => $pagination,
            'formModule' => $formModule,
            'itemNumberPerPage' => $pagination->getItemNumberPerPage()
        ]);
    }

    #[Route('/delete-module/{id}', name: 'delete_module')]
    public function deleteModule($id, ModuleRepository $moduleRepository, EntityManagerInterface $em)
    {
        $module = $moduleRepository->find($id);

        if ($module->getUser() && $module->getUser()->getId() == $this->getUser()->getId()) {
            $em->remove($module);
            $em->flush();
            $this->addFlash('flash_message', 'Модуль успешно удален');
        } else {
            $this->addFlash('flash_message', 'Не трогайте чужие модули');
        }

        return $this->redirectToRoute('modules');
    }
}
