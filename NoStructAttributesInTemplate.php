<?php

namespace NoStructAttributesInTemplate;

use Enlight_Event_EventArgs;
use Shopware\Bundle\StoreFrontBundle\Struct\Property\Set;
use Shopware\Components\Plugin;
use Shopware\Bundle\StoreFrontBundle\Struct\Extendable;

class NoStructAttributesInTemplate extends Plugin
{
    const ARG_EXTENDABLE_KEYS = [
        'Legacy_Struct_Converter_Convert_Blog' => 'blog',
        'Legacy_Struct_Converter_Convert_Category' => 'category',
        'Legacy_Struct_Converter_Convert_Configurator_Group' => 'configurator_group',
        'Legacy_Struct_Converter_Convert_Configurator_Option' => 'configurator_options',
        'Legacy_Struct_Converter_Convert_Configurator_Settings' => 'configurator_set',
        'Legacy_Struct_Converter_Convert_Country' => 'country',
        'Legacy_Struct_Converter_Convert_List_Product' => 'product',
        'Legacy_Struct_Converter_Convert_Manufacturer' => 'manufacturer',
        'Legacy_Struct_Converter_Convert_Media' => 'media',
        'Legacy_Struct_Converter_Convert_Property_Group' => 'property_group',
        'Legacy_Struct_Converter_Convert_Property_Option' => 'property_option',
        'Legacy_Struct_Converter_Convert_Price' => 'price',
        'Legacy_Struct_Converter_Convert_Product_Price' => 'price',
        'Legacy_Struct_Converter_Convert_Related_Product_Stream' => 'product_stream',
        'Legacy_Struct_Converter_Convert_State' => 'state',
        'Legacy_Struct_Converter_Convert_Unit' => 'unit',
        'Legacy_Struct_Converter_Convert_Vote' => 'vote',
        'Legacy_Struct_Converter_Convert_Vote_Average' => 'average',
        'Legacy_Struct_Converter_List_Product_Data' => 'product',
    ];

    public static function getSubscribedEvents()
    {
        return array_fill_keys(array_keys(self::ARG_EXTENDABLE_KEYS), 'addAttributes') +
            ['Legacy_Struct_Converter_Convert_Property_Set' => 'addPropertySetAttributes'];
    }

    public function addAttributes(Enlight_Event_EventArgs $args)
    {
        if (array_key_exists($args->getName(), self::ARG_EXTENDABLE_KEYS)) {
            $args->setReturn(array_merge(
                $this->convertExtendable($args->get(self::ARG_EXTENDABLE_KEYS[$args->getName()])),
                $args->getReturn()
            ));
        }
    }

    public function addPropertySetAttributes(Enlight_Event_EventArgs $args)
    {
        $data = $args->getReturn();
        /** @var Set $object */
        $object = $args->get('property_set');

        foreach ($object->getGroups() as $group) {
            if (array_key_exists($group->getId(), $data)) {
                $data[$group->getId()] = array_merge($this->convertExtendable($group), $data[$group->getId()]);
            }
        }

        $args->setReturn($data);
    }

    /**
     * @param Extendable $struct
     *
     * @return array
     */
    public function convertExtendable(Extendable $struct)
    {
        $data = [];

        $attributes = [];
        foreach ($struct->getAttributes() as $key => $attribute) {
            $attributes[$key] = $attribute->toArray();
        }

        if (!empty($attributes)) {
            $data['attributes'] = $attributes;
        }

        if (array_key_exists('core', $attributes)) {
            $data['attribute'] = $attributes['core'];
        }

        return $data;
    }
}
