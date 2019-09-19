<?php

namespace DorsetDigital\EdgePublisher\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

class ConfigExtension extends DataExtension
{
    private static $db = [
        'CloudflareNamespaceID' => 'Varchar(40)'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        parent::updateCMSFields($fields);
        $fields->addFieldToTab('Root.EdgePublisher',
            TextField::create('CloudflareNamespaceID')
                ->setDescription(_t(__CLASS__ . '.NSIDDesc',
                    "Do not change this unless you really know what you're doing!"))
        );
    }

}