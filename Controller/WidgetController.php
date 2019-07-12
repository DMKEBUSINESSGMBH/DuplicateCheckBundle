<?php

namespace DMK\DuplicateCheckBundle\Controller;

use DMK\DuplicateCheckBundle\Entity\Duplicate;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/dmk/duplicate")
 */
class WidgetController extends Controller
{
    /**
     * @Route(
     *     "/{entity}/{id}.{_format}",
     *     name="dmk_duplicate_widget_index",
     *     requirements={"entity"="[a-zA-Z0-9_]+", "id"="\d+"},
     *     defaults={"entity"="entity", "id"=0, "_format" = "html"}
     * )
     * @Template()
     * @Acl(
     *     type="entity",
     *     class="DMKDuplicateCheckBundle:Duplicate",
     *     permission="VIEW",
     *     id="dmk_duplicate_index"
     * )
     *
     * @param object $entity
     * @param int    $id
     *
     * @return array
     */
    public function indexAction($entity, int $id)
    {
        return [
            'gridName' => 'dmk-duplicates-grid',
            'entityClass' => $entity,
            'entityId' => $id,
        ];
    }

    /**
     * @Template()
     *
     * @param object $entity
     *
     * @return array
     */
    public function placeholderAction($entity)
    {
        $cnt = $this->getDoctrine()
            ->getRepository(Duplicate::class)
            ->getDuplicatesCnt($entity);

        return [
            'cnt' => $cnt,
            'entity' => $entity,
            'id' => $this->get('oro_entity.doctrine_helper')->getSingleEntityIdentifier($entity),
        ];
    }
}
