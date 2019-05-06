<?php

namespace DMK\DuplicateCheckBundle\EventListener;

use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DuplicateWidgetGridListener implements EventSubscriberInterface
{
    const GRID_PARAM_CLASS      = 'object_class';
    const GRID_PARAM_OBJECT_ID  = 'object_id';

    public static function getSubscribedEvents()
    {
        return [
            'oro_datagrid.datagrid.build.after.dmk-duplicates-grid' => 'onBuildAfter'
        ];
    }

    /**
     * @param BuildAfter $event
     */
    public function onBuildAfter(BuildAfter $event)
    {
        $datagrid = $event->getDatagrid();
        $datasource = $datagrid->getDatasource();
        if ($datasource instanceof OrmDatasource) {
            $parameters = $datagrid->getParameters();
            $queryBuilder = $datasource->getQueryBuilder();

            $queryParameters = array(
                'objectClass' => str_replace('_', '\\', $parameters->get(self::GRID_PARAM_CLASS, '')),
            );

            $queryParameters['objectId'] = $parameters->get(self::GRID_PARAM_OBJECT_ID, 0);

            $queryBuilder->setParameters($queryParameters);
        }
    }
}
