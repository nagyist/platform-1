<?php

namespace Oro\Bundle\EntityExtendBundle\Serializer;

use Oro\Component\EntitySerializer\EntityFieldFilterInterface;

use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;

class ExtendEntityFieldFilter implements EntityFieldFilterInterface
{
    /** @var ConfigManager */
    protected $configManager;

    /** @var bool */
    protected $allowExtendedFields;

    /**
     * @param ConfigManager $configManager
     * @param bool          $allowExtendedFields
     */
    public function __construct(ConfigManager $configManager, $allowExtendedFields = false)
    {
        $this->configManager       = $configManager;
        $this->allowExtendedFields = $allowExtendedFields;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableField($className, $fieldName)
    {
        $fieldModel = $this->configManager->getConfigFieldModel($className, $fieldName);
        if (!$fieldModel) {
            // this serializer works with non configurable entities as well
            return true;
        }

        if ($fieldModel->isHidden()) {
            // exclude hidden fields
            return false;
        }

        $extendConfigProvider = $this->configManager->getProvider('extend');
        $extendConfig         = $extendConfigProvider->getConfig($className, $fieldName);

        if (!$this->allowExtendedFields && $extendConfig->is('is_extend')) {
            // exclude extended fields if it is requested
            return false;
        }

        if ($extendConfig->is('is_deleted') || $extendConfig->is('state', ExtendScope::STATE_NEW)) {
            // exclude deleted and not created yet fields
            return false;
        }

        if ($extendConfig->has('target_entity')
            && $extendConfigProvider->getConfig($extendConfig->get('target_entity'))->is('is_deleted')
        ) {
            // exclude associations with deleted custom entities
            return false;
        }

        return true;
    }
}
