<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

<?= $use_statements; ?>

#[ORM\Entity]
<?php if ($should_escape_table_name): ?>#[ORM\Table(name: '`<?= $table_name ?>`')]
<?php endif ?>
<?php if ($api_resource): ?>
#[ApiResource]
<?php endif ?>
class <?= $class_name."\n" ?>
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
