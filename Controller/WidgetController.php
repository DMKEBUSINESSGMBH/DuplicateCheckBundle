<?php

<<<<<<< HEAD
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Controller;


=======
namespace DMK\DuplicateCheckBundle\Controller;

>>>>>>> d9b336ca0fa4f4a9a5dc79952101ee8da425a495
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
<<<<<<< HEAD
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/dmk/duplicates")
=======

/**
 * @Route("/dmk/duplicate")
>>>>>>> d9b336ca0fa4f4a9a5dc79952101ee8da425a495
 */
class WidgetController extends Controller
{
    /**
     * @Route(
<<<<<<< HEAD
     *     path="/{entity}/{id}.{_format}",
=======
     *     "/{entity}/{id}.{_format}",
>>>>>>> d9b336ca0fa4f4a9a5dc79952101ee8da425a495
     *     name="dmk_duplicate_widget_index",
     *     requirements={"entity"="[a-zA-Z0-9_]+", "id"="\d+"},
     *     defaults={"entity"="entity", "id"=0, "_format" = "html"}
     * )
     * @Template()
<<<<<<< HEAD
     * @Acl(type="entity", class="DMKDuplicateCheckBundle:Duplicate", permission="VIEW", id="dmk_duplicate_view")
     */
    public function indexAction(string $class, int $id)
    {
        return [
            'gridName' => 'dmk-duplicates-grid',
            'entityClass' => $class,
=======
     * @Acl(
     *     type="entity",
     *     class="DMKDuplicateCheckBundle:Duplicate",
     *     permission="VIEW",
     *     id="dmk_duplicate_index"
     * )
     */
    public function indexAction($entity, $id)
    {
        return [
            'gridName' => 'dmk-duplicates-grid',
            'entityClass' => $entity,
>>>>>>> d9b336ca0fa4f4a9a5dc79952101ee8da425a495
            'entityId' => $id
        ];
    }
}
