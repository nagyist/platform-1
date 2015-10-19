<?php

namespace Oro\Bundle\ApiBundle\Processor\Get;

use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Oro\Bundle\ApiBundle\Normalizer\ItemNormalizer;

class NormalizeData implements ProcessorInterface
{
    /** @var ItemNormalizer */
    protected $itemNormalizer;

    /**
     * @param ItemNormalizer $itemNormalizer
     */
    public function __construct(ItemNormalizer $itemNormalizer)
    {
        $this->itemNormalizer = $itemNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        if (!$context->hasResult()) {
            // no result
            return;
        }

        $data = $context->getResult();
        if (empty($data)) {
            // nothing to do because of empty result
            return;
        }

        $context->setResult($this->itemNormalizer->normalizeItem($data));
    }
}
