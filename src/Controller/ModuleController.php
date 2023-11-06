<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleFormType;
use App\Repository\ModuleRepository;
use App\Services\LicenseLevelControl;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ModuleController extends AbstractController
{
    #[Route('/dashboard-modules/', name: 'modules')]
    public function modules(
        ModuleRepository $moduleRepository,
        LicenseLevelControl $licenseLevelControl,
        Request $request,
        PaginatorInterface $paginator,
        EntityManagerInterface $em,
    ): Response
    {
        $authUser = $this->getUser();
        $licenseInfo = $licenseLevelControl->update($authUser);

        $pagination = $paginator->paginate(
            $moduleRepository->modulesListQuery($authUser->getId()),
            $request->query->getInt('page', 1), /*page number*/
            5
        );

        $formModule = $this->createForm(ModuleFormType::class);

        $formModule->handleRequest($request);

        if ($formModule->isSubmitted() && $formModule->isValid()) {
            /** @var Module $module */
            $module = $formModule->getData();
            $module->setUser($authUser);
            $module->setCreatedAt(Carbon::now());
            $module->setUpdatedAt(Carbon::now());
            $em->persist($module);
            $em->flush();
            $this->addFlash('flash_message', 'Модуль успешно создан');

            return $this->redirectToRoute('modules');
        }

        return $this->render('dashboard/modules.html.twig', [
            'itemActive' => 6,
            'licenseInfo' => $licenseInfo,
            'modules' => $pagination,
            'formModule' => $formModule->createView(),
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
