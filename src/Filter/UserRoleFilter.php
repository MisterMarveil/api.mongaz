<?php
namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;

class UserRoleFilter extends AbstractFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ($property !== 'role' || $value === null) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $parameterName = $queryNameGenerator->generateParameterName($property);

        // Use MySQL JSON_CONTAINS to check if role exists in JSON array
        $queryBuilder
            ->andWhere(sprintf("JSON_CONTAINS(%s.roles, :%s) = 1", $alias, $parameterName))
            ->setParameter($parameterName, json_encode($value));
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'role' => [
                'property' => 'role',
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'description' => 'Filter users by role (must exactly match a role in the roles JSON array)',
                'openapi' => [
                    'example' => 'ROLE_DRIVER'
                ]
            ],
        ];
    }
}
