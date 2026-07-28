<?php

namespace Src\Modules\Admin\Attribute\Application\UseCase;

use Core\Support\DebugHelper;
use Src\Modules\Admin\Attribute\Domain\Query\AttributeQuery;
use Src\Modules\Admin\Attribute\Domain\Repository\AttributeRepositoryInterface;
use Src\Modules\Shared\Application\UseCase\ResultUseCase;

final class ShowAttributeTreeUseCase
{
    public function __construct(private readonly AttributeRepositoryInterface $attributeRepo) {}

    public function execute(): ResultUseCase
    {
        $datas = $this->attributeRepo->findAttributes(new AttributeQuery(withAttributeGroup: true));

        DebugHelper::verboseServer("datas = ");
        DebugHelper::verboseServer($datas);
        return ResultUseCase::success($datas);
    }
}
