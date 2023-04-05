<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

class <?= $class_name; ?> implements <?= $repository_interface_class_name . "\n"; ?>
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return EntityRepository<<?= $entity_class_name ?>>
     */
    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(<?= $entity_class_name ?>::class);
    }
}
