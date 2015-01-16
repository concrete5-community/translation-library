<?php
namespace C5TL\Parser;

/**
 * Extract translatable strings from CIF (Content Import Format) xml files
 */
class Cif extends \C5TL\Parser
{
    /**
     * @see \C5TL\Parser::getParserName()
     */
    public function getParserName()
    {
        return 'CIF XML Parser';
    }

    /**
     * @see \C5TL\Parser::canParseDirectory()
     */
    public function canParseDirectory()
    {
        return true;
    }

    /**
     * @see \C5TL\Parser::canParseRunningConcrete5()
     */
    public function canParseRunningConcrete5()
    {
        return false;
    }

    /**
     * @see \C5TL\Parser::parseRunningConcrete5Do()
     */
    protected function parseRunningConcrete5Do(\Gettext\Translations $translations, $concrete5version)
    {
        throw new \Exception('This parser does not support parsing a running concrete5 instance');
    }

    /**
     * @see \C5TL\Parser::parseDirectoryDo()
     */
    protected function parseDirectoryDo(\Gettext\Translations $translations, $rootDirectory, $relativePath)
    {
        $prefix = strlen($relativePath) ? "$relativePath/" : '';
        foreach (array_merge(array(''), $this->getDirectoryStructure($rootDirectory)) as $child) {
            $shownDirectory = $prefix . (strlen($child) ? "$child/" : '');
            $fullDirectoryPath = "$rootDirectory/$child";
            $contents = @scandir($fullDirectoryPath);
            if ($contents === false) {
                throw new \Exception("Unable to parse directory $fullDirectoryPath");
            }
            foreach ($contents as $file) {
                if (strpos($file, '.') !== 0) {
                    $fullFilePath = "$fullDirectoryPath/$file";
                    if (preg_match('/^(.*)\.xml$/', $file) && is_file($fullFilePath)) {
                        static::parseXml($translations, $fullFilePath, $shownDirectory.$file);
                    }
                }
            }
        }
    }

    /**
     * Parses an XML CIF file and extracts translatable strings
     * @param \Gettext\Translations $translations
     * @param string $realPath
     * @param string $shownPath
     * @throws \Exception
     */
    private static function parseXml(\Gettext\Translations $translations, $realPath, $shownPath)
    {
        $xml = \DOMDocument::load($realPath);
        if ($xml === false) {
            global $php_errormsg;
            if (isset($php_errormsg) && strlen($php_errormsg)) {
                throw new \Exception("Error loading '$realPath': $php_errormsg");
            } else {
                throw new \Exception("Error loading '$realPath'");
            }
        }
        if ($xml->documentElement->tagName === 'concrete5-cif') {
            static::parseXmlNode($translations, $xml->documentElement, $shownPath, '');
        }
    }

    /** Parse an xml node and retrieves any associated POEntry.
     * @param \Gettext\Translations $translations Will be populated with found entries.
     * @param \DOMNode $node The current node.
     * @param string $filenameRel The relative file name of the xml file being read.
     * @param string $prePath The path of the node containing the current node.
     * @throws \Exception Throws an \Exception in case of errors.
     */
    private static function parseXmlNode(\Gettext\Translations $translations, \DOMNode $node, $filenameRel, $prePath)
    {
        $nodeClass = get_class($node);
        switch ($nodeClass) {
            case 'DOMElement':
                break;
            case 'DOMText':
            case 'DOMCdataSection':
            case 'DOMComment':
                return;
            default:
                throw new \Exception("Unknown node class '$nodeClass' in '$filenameRel'");
        }
        $path = $prePath . '/' . $node->tagName;
        $childnodesLimit = null;
        switch ($path) {
            case '/concrete5-cif':
            case '/concrete5-cif/attributecategories':
            case '/concrete5-cif/attributecategories/category':
            case '/concrete5-cif/attributekeys':
            case '/concrete5-cif/attributekeys/attributekey/type':
            case '/concrete5-cif/attributekeys/attributekey/type/options':
            case '/concrete5-cif/attributesets':
            case '/concrete5-cif/attributesets/attributeset/attributekey':
            case '/concrete5-cif/attributetypes':
            case '/concrete5-cif/attributetypes/attributetype/categories':
            case '/concrete5-cif/attributetypes/attributetype/categories/category':
            case '/concrete5-cif/blocktypes':
            case '/concrete5-cif/blocktypes/blocktype':
            case '/concrete5-cif/blocktypesets':
            case '/concrete5-cif/blocktypesets/blocktypeset/blocktype':
            case '/concrete5-cif/composercontroltypes':
            case '/concrete5-cif/conversationeditors':
            case '/concrete5-cif/conversationratingtypes':
            case '/concrete5-cif/gatheringitemtemplates':
            case '/concrete5-cif/gatheringitemtemplates/gatheringitemtemplate/feature':
            case '/concrete5-cif/gatheringsources':
            case '/concrete5-cif/imageeditor_components':
            case '/concrete5-cif/imageeditor_controlsets':
            case '/concrete5-cif/imageeditor_filters':
            case '/concrete5-cif/jobs':
            case '/concrete5-cif/jobs/job':
            case '/concrete5-cif/jobsets':
            case '/concrete5-cif/jobsets/jobset/job':
            case '/concrete5-cif/pagefeeds':
            case '/concrete5-cif/pages':
            case '/concrete5-cif/pages/page/area/block/arealayout':
            case '/concrete5-cif/pages/page/area/block/arealayout/columns':
            case '/concrete5-cif/pages/page/area/block/arealayout/columns/column':
            case '/concrete5-cif/pages/page/area/block/arealayout/columns/column/block/data':
            case '/concrete5-cif/pages/page/area/block/data':
            case '/concrete5-cif/pages/page/area/block/stack':
            case '/concrete5-cif/pages/page/attributes':
            case '/concrete5-cif/pages/page/attributes/attributekey':
            case '/concrete5-cif/pages/page/attributes/attributekey/topics':
            case '/concrete5-cif/pages/page/attributes/attributekey/value':
            case '/concrete5-cif/pages/page/attributes/attributekey/value/fID':
            case '/concrete5-cif/pages/page/attributes/attributekey/value/option':
            case '/concrete5-cif/pagetemplates':
            case '/concrete5-cif/pagetypecomposercontroltypes':
            case '/concrete5-cif/pagetypepublishtargettypes':
            case '/concrete5-cif/pagetypes':
            case '/concrete5-cif/pagetypes/pagetype/composer':
            case '/concrete5-cif/pagetypes/pagetype/composer/formlayout':
            case '/concrete5-cif/pagetypes/pagetype/composer/items':
            case '/concrete5-cif/pagetypes/pagetype/composer/items/attributekey':
            case '/concrete5-cif/pagetypes/pagetype/composer/output':
            case '/concrete5-cif/pagetypes/pagetype/composer/output/pagetemplate':
            case '/concrete5-cif/pagetypes/pagetype/composer/output/pagetemplate/page':
            case '/concrete5-cif/pagetypes/pagetype/composer/output/pagetemplate/page/area/blocks':
            case '/concrete5-cif/pagetypes/pagetype/formlayout':
            case '/concrete5-cif/pagetypes/pagetype/output':
            case '/concrete5-cif/pagetypes/pagetype/output/pagetemplate':
            case '/concrete5-cif/pagetypes/pagetype/page/area/block/data':
            case '/concrete5-cif/pagetypes/pagetype/page/attributes':
            case '/concrete5-cif/pagetypes/pagetype/page/attributes/attribute':
            case '/concrete5-cif/pagetypes/pagetype/page/attributes/attributekey':
            case '/concrete5-cif/pagetypes/pagetype/pagetemplates':
            case '/concrete5-cif/pagetypes/pagetype/pagetemplates/pagetemplate':
            case '/concrete5-cif/pagetypes/pagetype/target':
            case '/concrete5-cif/permissionaccessentitytypes':
            case '/concrete5-cif/permissionaccessentitytypes/permissionaccessentitytype/categories':
            case '/concrete5-cif/permissionaccessentitytypes/permissionaccessentitytype/categories/category':
            case '/concrete5-cif/permissioncategories':
            case '/concrete5-cif/permissioncategories/category':
            case '/concrete5-cif/permissionkeys':
            case '/concrete5-cif/permissionkeys/permissionkey/access':
            case '/concrete5-cif/permissionkeys/permissionkey/access/group':
            case '/concrete5-cif/singlepages':
            case '/concrete5-cif/singlepages/page/attributes':
            case '/concrete5-cif/singlepages/page/attributes/attributekey':
            case '/concrete5-cif/stacks':
            case '/concrete5-cif/stacks/stack/area/block/data':
            case '/concrete5-cif/stacks/stack/area/block/link':
            case '/concrete5-cif/stacks/stack/area/blocks';
            case '/concrete5-cif/systemcaptcha':
            case '/concrete5-cif/systemcontenteditorsnippets':
            case '/concrete5-cif/taskpermissions':
            case '/concrete5-cif/taskpermissions/taskpermission/access':
            case '/concrete5-cif/taskpermissions/taskpermission/access/group':
            case '/concrete5-cif/themes':
            case '/concrete5-cif/themes/theme':
            case '/concrete5-cif/thumbnailtypes':
            case '/concrete5-cif/trees':
            case '/concrete5-cif/trees/tree':
            case '/concrete5-cif/workflowprogresscategories':
            case '/concrete5-cif/workflowprogresscategories/category':
            case '/concrete5-cif/workflowtypes':
                // Skip this node
                break;
            case '/concrete5-cif/pages/page/area/block/data/record':
                // Skip this node and *almost* all its children
                $childnodesLimit = array('title');
                break;
            case '/concrete5-cif/pagefeeds/feed':
                // Skip this node and *almost* all its children
                $childnodesLimit = array('title', 'description');
                break;
            case '/concrete5-cif/banned_words':
            case '/concrete5-cif/config':
            case '/concrete5-cif/featurecategories':
            case '/concrete5-cif/features':
            case '/concrete5-cif/flag_types':
            case '/concrete5-cif/gatheringitemtemplatetypes':
            case '/concrete5-cif/pages/page/area/block/arealayout/columns/column/block/data/record':
            case '/concrete5-cif/pages/page/area/blocks';
            case '/concrete5-cif/pages/page/area/style';
            case '/concrete5-cif/pagetypes/pagetype/composer/output/pagetemplate/page/area/block':
            case '/concrete5-cif/pagetypes/pagetype/composer/output/pagetemplate/page/area/blocks/block';
            case '/concrete5-cif/pagetypes/pagetype/composer/output/pagetemplate/page/area/style':
            case '/concrete5-cif/pagetypes/pagetype/page/area/block/data/record':
            case '/concrete5-cif/sociallinks':
            case '/concrete5-cif/stacks/stack/area/block/data/record':
            case '/concrete5-cif/stacks/stack/area/blocks/block';
                // Skip this node and its children
                return;
            case '/concrete5-cif/pages/page/area/block':
            case '/concrete5-cif/pages/page/area/block/arealayout/columns/column/block':
            case '/concrete5-cif/pagetypes/pagetype':
            case '/concrete5-cif/pagetypes/pagetype/composer/items/block':
            case '/concrete5-cif/pagetypes/pagetype/page/area/block':
            case '/concrete5-cif/stacks/stack':
            case '/concrete5-cif/stacks/stack/area':
            case '/concrete5-cif/stacks/stack/area/block':
            case '/concrete5-cif/systemcaptcha/library':
            case '/concrete5-cif/workflowtypes/workflowtype':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel);
                break;
            case '/styles/set':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'StyleSetName');
                break;
            case '/styles/set/style':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'StyleName');
                break;
            case '/concrete5-cif/attributekeys/attributekey':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'AttributeKeyName');
                break;
            case '/concrete5-cif/attributekeys/attributekey/tree':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'TreeName');
                break;
            case '/concrete5-cif/thumbnailtypes/thumbnailtype':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'ThumbnailTypeName');
                break;
            case '/concrete5-cif/trees/tree/topic_category':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'TopicCategoryName');
                break;
            case '/concrete5-cif/trees/tree/topic':
            case '/concrete5-cif/trees/tree/topic_category/topic':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'TopicName');
                break;
            case '/concrete5-cif/attributesets/attributeset':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'AttributeSetName');
                break;
            case '/concrete5-cif/attributetypes/attributetype':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'AttributeTypeName');
                break;
            case '/concrete5-cif/permissionaccessentitytypes/permissionaccessentitytype':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'PermissionAccessEntityTypeName');
                break;
            case '/concrete5-cif/systemcontenteditorsnippets/snippet':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'SystemContentEditorSnippetName');
                break;
            case '/concrete5-cif/blocktypesets/blocktypeset':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'BlockTypeSetName');
                break;
            case '/concrete5-cif/composercontroltypes/type':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'ComposerControlTypeName');
                break;
            case '/concrete5-cif/gatheringsources/gatheringsource':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'GatheringDataSourceName');
                break;
            case '/concrete5-cif/gatheringitemtemplates/gatheringitemtemplate':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'GatheringItemTemplateName');
                break;
            case '/concrete5-cif/conversationeditors/editor':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'ConversationEditorName');
                break;
            case '/concrete5-cif/conversationratingtypes/conversationratingtype':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'ConversationRatingTypeName');
                break;
            case '/concrete5-cif/pages/page':
            case '/concrete5-cif/pagetypes/pagetype/page':
            case '/concrete5-cif/singlepages/page':
            case '/concrete5-cif/taskpermissions/taskpermission':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel);
                static::readXmlNodeAttribute($translations, $node, 'description', $filenameRel);
                break;
            case '/concrete5-cif/permissionkeys/permissionkey':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'PermissionKeyName');
                static::readXmlNodeAttribute($translations, $node, 'description', $filenameRel, 'PermissionKeyDescription');
                break;
            case '/concrete5-cif/pages/page/area/block/data/record/title':
                static::parseXmlNodeValue($translations, $node, $filenameRel);
                break;
            case '/concrete5-cif/pagefeeds/feed/title':
                static::parseXmlNodeValue($translations, $node, $filenameRel, 'FeedTitle');
                break;
            case '/concrete5-cif/pagefeeds/feed/description':
                static::parseXmlNodeValue($translations, $node, $filenameRel, 'FeedDescription');
                break;
            case '/concrete5-cif/singlepages/page/attributes/attributekey/value':
                switch ($node->parentNode->getAttribute('handle')) {
                    case 'meta_keywords':
                        static::readXmlPageKeywords($translations, $node, $filenameRel, $node->parentNode->parentNode->parentNode->getAttribute('path'));
                        break;
                }
                break;
            case '/concrete5-cif/jobsets/jobset':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'JobSetName');
                break;
            case '/concrete5-cif/pages/page/area':
            case '/concrete5-cif/pagetypes/pagetype/composer/output/pagetemplate/page/area':
            case '/concrete5-cif/pagetypes/pagetype/page/area':
            case '/concrete5-cif/singlepages/page/area':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'AreaName');
                break;
            case '/concrete5-cif/pagetypes/pagetype/output/pagetemplate/page':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'PageTemplatePageName');
                static::readXmlNodeAttribute($translations, $node, 'description', $filenameRel, 'PageTemplatePageDescription');
                break;
            case '/concrete5-cif/attributekeys/attributekey/type/options/option':
                $attributeKeyType = $node/*option*/->parentNode/*options*/->parentNode/*type*/->parentNode/*attributekey*/->getAttribute('type');
                switch (strlen($attributeKeyType) ? strval($attributeKeyType) : '') {
                    case 'select':
                        static::readXmlNodeAttribute($translations, $node, 'value', $filenameRel, 'SelectAttributeValue');
                        break;
                }
                break;
            case '/concrete5-cif/pagetemplates/pagetemplate':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'PageTemplateName');
                break;
            case '/concrete5-cif/imageeditor_controlsets/imageeditor_controlset':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'ImageEditorControlSetName');
                break;
            case '/concrete5-cif/imageeditor_components/imageeditor_component':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'ImageEditorComponentName');
                break;
            case '/concrete5-cif/imageeditor_filters/imageeditor_filter':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'ImageEditorFilterName');
                break;
            case '/concrete5-cif/pagetypepublishtargettypes/type':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'PageTypePublishTargetTypeName');
                break;
            case '/concrete5-cif/pagetypecomposercontroltypes/type':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'PageTypeComposerControlTypeName');
                break;
            case '/concrete5-cif/pagetypes/pagetype/formlayout/set':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'PageTypeFormLayoutSetName');
                break;
            case '/concrete5-cif/pagetypes/pagetype/formlayout/set':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'PageTypeFormLayoutSetName');
                break;
            case '/concrete5-cif/pagetypes/pagetype/composer/formlayout/set':
                static::readXmlNodeAttribute($translations, $node, 'name', $filenameRel, 'PageTypeComposerFormLayoutSetName');
                break;
            case '/concrete5-cif/pagetypes/pagetype/formlayout/set/control':
                static::readXmlNodeAttribute($translations, $node, 'custom-label', $filenameRel, 'PageTypeFormLayoutSetControlCustomLabel');
                break;
            case '/concrete5-cif/pagetypes/pagetype/composer/formlayout/set/control':
                static::readXmlNodeAttribute($translations, $node, 'custom-label', $filenameRel, 'PageTypeComposerFormLayoutSetControlCustomLabel');
                break;
            case '/concrete5-cif/pages/page/attributes/attributekey/topics/topic':
                static::parseXmlNodeValue($translations, $node, $filenameRel, 'Topic');
                break;
            default:
                throw new \Exception('Unknown tag name ' . $path . ' in ' . $filenameRel . "\n\nNode:\n" . $node->ownerDocument->saveXML($node));
        }
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if (is_null($childnodesLimit) || (is_a($child, 'DOMElement') && array_search($child->tagName, $childnodesLimit) !== false)) {
                    static::parseXmlNode($translations, $child, $filenameRel, $path);
                }
            }
        }
    }

    /** Parse a node attribute and create a POEntry item if it has a value.
     * @param \Gettext\Translations $translations Will be populated with found entries.
     * @param \DOMNode $node The current node.
     * @param string $filenameRel The relative file name of the xml file being read.
     * @param string $attributeName The name of the attribute.
     * @param string $context='' The translation context.
     */
    private static function readXmlNodeAttribute(\Gettext\Translations $translations, \DOMNode $node, $filenameRel, $attributeName, $context = '')
    {
        $value = $node->getAttribute($attributeName);
        if (strlen($value)) {
            $translation = $translations->find($context, $value);
            if (!$translation) {
                $translation = $translations->insert($context, $value);
            }
            $translation->addReference($filenameRel, $node->getLineNo());
        }
    }

    /** Parse a node attribute which contains the keywords for a page.
     * @param \Gettext\Translations $translations Will be populated with found entries.
     * @param \DOMNode $node The current node.
     * @param string $filenameRel The relative file name of the xml file being read.
     * @param string $pageUrl The url of the page for which the keywords are for.
     */
    private static function readXmlPageKeywords(\Gettext\Translations $translations, \DOMNode $node, $filenameRel, $pageUrl)
    {
        $keywords = (string) $node->nodeValue;
        if (strlen($keywords) > 0) {
            $translation = $translations->find('', $keywords);
            if (!$translation) {
                $translation = $translations->insert('', $keywords);
            }
            $translation->addReference($filenameRel, $node->getLineNo());
            $pageUrl = (string) $pageUrl;
            if (strlen($pageUrl) > 0) {
                $translation->addComment("Keywords for page $pageUrl");
            }
        }
    }

    /**
     *  Parse a node value and create a POEntry item if it has a value.
     * @param \Gettext\Translations $translations Will be populated with found entries.
     * @param \DOMNode $node The current node.
     * @param string $filenameRel The relative file name of the xml file being read.
     * @param string $context='' The translation context.
     */
    private static function parseXmlNodeValue(\Gettext\Translations $translations, \DOMNode $node, $filenameRel, $context = '')
    {
        $value = (string) $node->nodeValue;
        if (strlen($value) > 0) {
            $translation = $translations->find($context, $value);
            if (!$translation) {
                $translation = $translations->insert($context, $value);
            }
            $translation->addReference($filenameRel, $node->getLineNo());
        }
    }
}
