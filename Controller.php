<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller
{
    /**
     * @Route("/dmk/duplicates", name="dmk_duplicate_check_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        if (!$class = $request->query->get('class')) {
            throw new NotFoundHttpException();
        }

        if (!$id = $request->query->getInt('id')) {
            throw new NotFoundHttpException();
        }

        return [
            'class' => $class,
            'id'    => $id
        ];
    }
}