<?php
declare(strict_types=1);

namespace DMK\DuplicateCheckBundle\Entity;

use DMK\DuplicateCheckBundle\Model\DuplicateInterface;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks()
 */
abstract class BaseDuplicate implements DuplicateInterface
{
    /**
     * @var integer
     *
     * @ORM\Id @ORM\Column(type="integer", name="id", options={"unsigned":true}) @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="class")
     */
    protected $class;

    /**
     * @var float
     *
     * @ORM\Column(type="smallint", length=3, name="weight")
     */
    protected $weight;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", name="object_id")
     */
    protected $objectId;

    /**
     * @var object
     */
    protected $object;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    static public function create(EntityManagerInterface $em, $object, $weight = 0.5): self
    {
        return new static(
            ClassUtils::getClass($object),
            $em->getClassMetadata($instance->class)->getIdentifierValues($object)[0],
            $weight
        );
    }

    public function __construct(string $class, int $id, float $weight = 0.5)
    {
        $this->weight = $weight * 100;
        $this->class = $class;
        $this->id = $id;
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight(): float
    {
        return $this->weight / 100;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function onLoad(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();

        $this->object = $em->find($this->class, $this->id);
    }
}