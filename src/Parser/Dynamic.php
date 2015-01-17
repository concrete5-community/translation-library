<?php
namespace C5TL\Parser;

/**
 * Extract translatable strings from block type templates
 */
class Dynamic extends \C5TL\Parser
{
    /**
     * @see \C5TL\Parser::getParserName()
     */
    public function getParserName()
    {
        return 'Block templates';
    }

    /**
     * @see \C5TL\Parser::canParseRunningConcrete5()
     */
    public function canParseRunningConcrete5()
    {
        return true;
    }

    /**
     * @see \C5TL\Parser::parseRunningConcrete5Do()
     */
    protected function parseRunningConcrete5Do(\Gettext\Translations $translations, $concrete5version)
    {
        $parsed = static::parseRunningConcrete5Do_common($concrete5version);
        if (version_compare($concrete5version, '5.7') < 0) {
            $parsed = array_merge_recursive($parsed, static::parseRunningConcrete5Do_Pre57($concrete5version));
        } else {
            static::parseRunningConcrete5Do_From57($translations, $concrete5version);
        }
        foreach ($parsed as $context => $strings) {
            $validStrings = array();
            foreach ($strings as $string) {
                if (is_string($string) && strlen($string)) {
                    $validStrings[] = $string;
                }
            }
            foreach (array_unique($validStrings) as $string) {
                if (!$translations->find($context, $string)) {
                    $translations->insert($context, $string);
                }
            }
        }
    }

    private static function parseRunningConcrete5Do_common($concrete5version)
    {
        $parsed = array();
        $db = \Loader::db();
        // Areas
        $rs = $db->Execute('select distinct (binary arHandle) as AreaName from Areas order by arHandle');
        while ($row = $rs->FetchRow()) {
            $parsed['AreaName'][] = $row['AreaName'];
        }
        $rs->Close();
        // Attribute key categories
        $akcNameMap = array(
            'collection' => 'Page attributes',
            'user' => 'User attributes',
            'file' => 'File attributes',
        );
        if (version_compare($concrete5version, '5.7') < 0) {
            $akcClass = '\AttributeKeyCategory';
        } else {
            $akcClass = '\Concrete\Core\Attribute\Key\Category';
        }
        foreach (call_user_func($akcClass .'::getList') as $akc) {
            $akcHandle = $akc->getAttributeKeyCategoryHandle();
            $parsed[''][] = array_key_exists($akcHandle, $akcNameMap) ? $akcNameMap[$akcHandle] : static::uncamelcase($akcHandle);
        }
        // Permission key categories
        $pkcNameMap = array(
            'page' => 'Page',
            'single_page' => 'Single page',
            'stack' => 'Stack',
            'composer_page' => 'Composer page',
            'user' => 'User',
            'file_set' => 'File set',
            'file' => 'File',
            'area' => 'Area',
            'block_type' => 'Block type',
            'block' => 'Block',
            'admin' => 'Administration',
            'sitemap' => 'Site map',
            'marketplace_newsflow' => 'MarketPlace newsflow',
            'basic_workflow' => 'Basic workflow',
        );
        if (version_compare($concrete5version, '5.7') < 0) {
            if (version_compare($concrete5version, '5.6') >= 0) {
                $pkcClass = '\PermissionKeyCategory';
            } else {
                $pkcClass = '';
            }
        } else {
            $pkcClass = '\Concrete\Core\Permission\Category';
        }
        if (strlen($pkcClass)) {
            foreach (call_user_func($pkcClass .'::getList') as $pkc) {
                $pkcHandle = $pkc->getPermissionKeyCategoryHandle();
                $parsed[''][] = array_key_exists($pkcHandle, $pkcNameMap) ? $pkcNameMap[$pkcHandle] : static::uncamelcase($pkcHandle);
            }
        }

        return $parsed;
    }
    private static function parseRunningConcrete5Do_From57(\Gettext\Translations $translations, $concrete5version)
    {
        foreach (array(
            // Attribute sets
            '\Concrete\Core\Attribute\Set',
            // Attribute keys
            '\Concrete\Core\Attribute\Key\Key',
            // Select attribute values
            '\Concrete\Attribute\Select\Option',
            // Attribute types
            '\Concrete\Core\Attribute\Type',
            // Permission keys
            '\Concrete\Core\Permission\Key\Key',
            // Permission access entity types
            '\Concrete\Core\Permission\Access\Entity\Type',
            // Groups
            '\Concrete\Core\User\Group\Group',
            // Group sets
            '\Concrete\Core\User\Group\GroupSet',
            // Job sets
            '\Concrete\Core\Job\Set',
        ) as $fqClassName) {
            if (class_exists($fqClassName, true) && method_exists($fqClassName, 'exportTranslations')) {
                $translations->mergeWith(call_user_func($fqClassName .'::exportTranslations'));
            }
        }
    }

    private static function parseRunningConcrete5Do_Pre57($concrete5version)
    {
        $db = \Loader::db();
        $parsed = array();

        foreach (\AttributeKeyCategory::getList() as $akc) {
            $akcHandle = $akc->getAttributeKeyCategoryHandle();
            // Attribute sets
            foreach ($akc->getAttributeSets() as $as) {
                $parsed['AttributeSetName'][] = $as->getAttributeSetName();
            }
            // Attribute keys
            foreach (\AttributeKey::getList($akcHandle) as $ak) {
                $parsed['AttributeKeyName'][] = $ak->getAttributeKeyName();
                if ($ak->getAttributeType()->getAttributeTypeHandle() == 'select') {
                    // Select attribute values
                    foreach ($ak->getController()->getOptions() as $option) {
                        $parsed['SelectAttributeValue'][] = $option->getSelectAttributeOptionValue(false);
                    }
                }
            }
        }
        // Attribute types
        foreach (\AttributeType::getList() as $at) {
            $parsed['AttributeTypeName'][] = $at->getAttributeTypeName();
        }
        // Permission keys
        if (version_compare($concrete5version, '5.6') >= 0) {
            foreach (\PermissionKeyCategory::getList() as $pkc) {
                $pkcHandle = $pkc->getPermissionKeyCategoryHandle();
                foreach (\PermissionKey::getList($pkcHandle) as $pk) {
                    $parsed['PermissionKeyName'][] = $pk->getPermissionKeyName();
                    $parsed['PermissionKeyDescription'][] = $pk->getPermissionKeyDescription();
                }
            }
        }
        // Permission access entity types
        if (version_compare($concrete5version, '5.6') >= 0) {
            foreach (\PermissionAccessEntityType::getList() as $accessEntityType) {
                $parsed['PermissionAccessEntityTypeName'][] = $accessEntityType->getAccessEntityTypeName();
            }
        }
        // Groups
        $gl = new \GroupList(null, false, true);
        foreach ($gl->getGroupList() as $g) {
            $parsed['GroupName'][] = $g->getGroupName();
            $parsed['GroupDescription'][] = $g->getGroupDescription();
        }
        // Group sets
        if (version_compare($concrete5version, '5.6') >= 0) {
            foreach (\GroupSet::getList() as $gs) {
                $parsed['GroupSetName'][] = $gs->getGroupSetName();
            }
        }
        // Job sets
        if (version_compare($concrete5version, '5.6.2') >= 0) {
            foreach (\JobSet::getList() as $jobSet) {
                $parsed['JobSetName'][] = $jobSet->getJobSetName();
            }
        }

        return $parsed;
    }
}
