<?php

declare(strict_types=1);

namespace FH\Bundle\MakerBundle\Doctrine;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use ApiPlatform\Metadata\ApiResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class EntityClassGenerator
{
    public function __construct(
        private Generator $generator,
        private DoctrineHelper $doctrineHelper,
    ) {
    }

    public function generateEntityClass(ClassNameDetails $entityClassDetails, bool $apiResource, bool $resolver, ?string $domain): string
    {
        $tableName = $this->doctrineHelper->getPotentialTableName($entityClassDetails->getFullName());

        $useStatements = new UseStatementGenerator([
            ['Doctrine\\ORM\\Mapping' => 'ORM'],
        ]);

        if ($apiResource) {
            // @legacy Drop annotation class when annotations are no longer supported.
            $useStatements->addUseStatement(class_exists(ApiResource::class) ? ApiResource::class : \ApiPlatform\Core\Annotation\ApiResource::class);
        }

        $entityPath = $this->generator->generateClass(
            $entityClassDetails->getFullName(),
            __DIR__ . '/../Resources/skeleton/doctrine/Entity.tpl.php',
            [
                'use_statements' => $useStatements,
                'api_resource' => $apiResource,
                'should_escape_table_name' => $this->doctrineHelper->isKeyword($tableName),
                'table_name' => $tableName,
            ]
        );

        $repositoryNamespace = ($domain ? $domain . '\\' : '') . 'Repository\\';

        $repoInterfaceClassDetails = $this->generator->createClassNameDetails(
            $entityClassDetails->getRelativeName(),
            'Domain\\' . $repositoryNamespace,
            'RepositoryInterface'
        );

        $repoClassDetails = $this->generator->createClassNameDetails(
            $entityClassDetails->getRelativeName(),
            'Infrastructure\\' . $repositoryNamespace,
            'Repository'
        );

        $this->generateRepositoryInterfaceClass(
            $repoInterfaceClassDetails->getFullName(),
            $entityClassDetails->getFullName(),
        );

        $this->generateRepositoryClass(
            $repoClassDetails->getFullName(),
            $entityClassDetails->getFullName(),
            $repoInterfaceClassDetails->getFullName(),
        );

        if ($resolver) {
            $repositoryNamespace = ($domain ? $domain . '\\' : '') . 'Resolver\\';

            $resolverClassDetails = $this->generator->createClassNameDetails(
                $entityClassDetails->getRelativeName(),
                'Domain\\' . $repositoryNamespace,
                'Resolver'
            );

            $this->generateResolverClass(
                $resolverClassDetails->getFullName(),
                $entityClassDetails->getFullName(),
                $repoInterfaceClassDetails->getFullName(),
            );
        }

        return $entityPath;
    }

    public function generateRepositoryClass(string $repositoryClass, string $entityClass, string $repositoryInterfaceClass): void
    {
        $shortEntityClass = Str::getShortClassName($entityClass);
        $entityAlias = strtolower($shortEntityClass[0]);

        $shortInterfaceClass = Str::getShortClassName($repositoryInterfaceClass);

        $useStatements = new UseStatementGenerator([
            $entityClass,
            EntityManagerInterface::class,
            EntityRepository::class,
            $repositoryInterfaceClass,
        ]);

        $this->generator->generateClass(
            $repositoryClass,
            __DIR__ . '/../Resources/skeleton/doctrine/Repository.tpl.php',
            [
                'use_statements' => $useStatements,
                'entity_class_name' => $shortEntityClass,
                'entity_alias' => $entityAlias,
                'repository_interface_class_name' => $shortInterfaceClass,
            ]
        );
    }

    public function generateRepositoryInterfaceClass(string $repositoryClass, string $entityClass): void
    {
        $shortEntityClass = Str::getShortClassName($entityClass);
        $entityAlias = strtolower($shortEntityClass[0]);

        $useStatements = new UseStatementGenerator([
            $entityClass,
            EntityManagerInterface::class,
        ]);

        $this->generator->generateClass(
            $repositoryClass,
            __DIR__ . '/../Resources/skeleton/doctrine/RepositoryInterface.tpl.php',
            [
                'use_statements' => $useStatements,
                'entity_class_name' => $shortEntityClass,
                'entity_alias' => $entityAlias,
            ]
        );
    }

    public function generateResolverClass(string $resolverClass, string $entityClass, string $repositoryInterfaceClass): void
    {
        $shortEntityClass = Str::getShortClassName($entityClass);
        $entityAlias = strtolower($shortEntityClass[0]);
        $shortInterfaceClass = Str::getShortClassName($repositoryInterfaceClass);

        $useStatements = new UseStatementGenerator([
            $repositoryInterfaceClass,
        ]);

        $this->generator->generateClass(
            $resolverClass,
            __DIR__ . '/../Resources/skeleton/doctrine/Resolver.tpl.php',
            [
                'use_statements' => $useStatements,
                'entity_class_name' => $shortEntityClass,
                'entity_alias' => $entityAlias,
                'repository_interface_class_name' => $shortInterfaceClass,
            ]
        );
    }
}
