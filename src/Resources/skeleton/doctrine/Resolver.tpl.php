<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

<?= $use_statements; ?>

final class <?= $class_name. "\n"; ?>
{
    public function __construct(private readonly <?= $repository_interface_class_name; ?> $repository)
    {
    }
}
