<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Entity;

use DMK\DuplicateCheckBundle\Model\ExtendDuplicate;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\OrganizationAwareTrait;

/**
 * @ORM\Entity(repositoryClass="DMK\DuplicateCheckBundle\Repository\DuplicateRepository")
 * @ORM\Table(name="dmk_duplicate", indexes={@ORM\Index(name="dmk_duplicate_idx", columns={"class", "object_id"})})
 * @Config(defaultValues={
 *     "ownership"={
 *          "owner_type"="ORGANIZATION",
 *          "owner_field_name"="organization",
 *          "owner_column_name"="organization_id"
 *     }
 * })
 */
class Duplicate extends ExtendDuplicate
    implements OrganizationAwareInterface
{
    use OrganizationAwareTrait;
}
