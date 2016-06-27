<?php
/**
 * AbstractEditor
 *
 * PHP version 5
 *
 * @category    Editor
 * @package     Xpressengine\Editor
 * @author      XE Team (developers) <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/lgpl-3.0-standalone.html LGPL
 * @link        http://www.xpressengine.com
 */
namespace Xpressengine\Editor;

use Xpressengine\Config\ConfigEntity;
use Xpressengine\Plugin\ComponentInterface;
use Xpressengine\Plugin\ComponentTrait;
use Xpressengine\Skin\SkinHandler;
use Xpressengine\Support\MobileSupportTrait;
use Xpressengine\Permission\Instance;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Auth\Access\Gate;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class AbstractEditor
 *
 * @category    Editor
 * @package     Xpressengine\Editor
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */
abstract class AbstractEditor implements ComponentInterface
{
    use ComponentTrait, MobileSupportTrait;

    /**
     * EditorHandler instance
     *
     * @var EditorHandler
     */
    protected $editors;

    /**
     * UrlGenerator instance
     *
     * @var UrlGenerator
     */
    protected $urls;

    /**
     * Gate instance
     *
     * @var Gate
     */
    protected $gate;

    /**
     * SkinHandler instance
     *
     * @var SkinHandler
     */
    protected $skins;

    /**
     * Instance identifier
     *
     * @var string
     */
    protected $instanceId;

    /**
     * ConfigEntity instance
     *
     * @var ConfigEntity|null
     */
    protected $config;

    /**
     * Given arguments for the editor
     *
     * @var array
     */
    protected $arguments = [];

    protected $files = [];

    /**
     * Indicates if used only javascript.
     *
     * @var bool
     */
    protected $scriptOnly = false;

    /**
     * The registered tools for the editor
     *
     * @var AbstractTool[]
     */
    protected $tools;

    /**
     * The image resolver
     *
     * @var callable
     */
    protected static $imageResolver;

    /**
     * Default editor arguments
     *
     * @var array
     */
    protected $defaultArguments = [
        'content' => '',
        'contentDomName' => 'content',
        'contentDomId' => 'xeContentEditor',
        'contentDomOptions' => [
            'class' => 'form-control',
            'rows' => '20',
            'cols' => '80'
        ]
    ];

    /**
     * The file input name
     *
     * @var string
     */
    protected $fileInputName = '_files';

    /**
     * The tag input name
     *
     * @var string
     */
    protected $tagInputName = '_tags';

    /**
     * The mention input name
     *
     * @var string
     */
    protected $mentionInputName = '_mentions';

    /**
     * The image class name
     *
     * @var string
     */
    protected $imageClassName = '__xe_image';

    /**
     * The tag class name
     *
     * @var string
     */
    protected $tagClassName = '__xe_hashtag';

    /**
     * The mention class name
     *
     * @var string
     */
    protected $mentionClassName = '__xe_mention';

    /**
     * The image identifier attribute name
     *
     * @var string
     */
    protected $imageIdentifierAttrName = 'data-id';

    /**
     * The mention identifier attribute name
     *
     * @var string
     */
    protected $mentionIdentifierAttrName = 'data-id';

    /**
     * AbstractEditor constructor.
     *
     * @param EditorHandler $editors    EditorHandler instance
     * @param UrlGenerator  $urls       UrlGenerator instance
     * @param Gate          $gate       Gate instance
     * @param SkinHandler   $skins      SkinHandler instance
     * @param string        $instanceId Instance identifier
     */
    public function __construct(EditorHandler $editors, UrlGenerator $urls, Gate $gate, SkinHandler $skins, $instanceId)
    {
        $this->editors = $editors;
        $this->urls = $urls;
        $this->gate = $gate;
        $this->skins = $skins;
        $this->instanceId = $instanceId;
    }

    /**
     * Set config for the editor
     *
     * @param ConfigEntity $config config instance
     * @return $this
     */
    public function setConfig(ConfigEntity $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set arguments for the editor
     *
     * @param array $arguments arguments
     * @return $this
     */
    public function setArguments($arguments = [])
    {
        $this->arguments = $arguments;

        if ($arguments === false) {
            $this->scriptOnly = true;
        }

        return $this;
    }

    /**
     * Get arguments for the editor
     *
     * @return array
     */
    public function getArguments()
    {
        return array_merge($this->defaultArguments, $this->arguments);
    }

    /**
     * Set files the editor used
     *
     * @param array $files file instances
     * @return void
     */
    public function setFiles($files = [])
    {
        $this->files = $files;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = [
            'fileUpload' => [
                'upload_url' => $this->urls->route('editor.file.upload'),
                'source_url' => $this->urls->route('editor.file.source'),
                'download_url' => $this->urls->route('editor.file.download'),
                'destroy_url' => $this->urls->route('editor.file.destroy'),
            ],
            'suggestion' => [
                'hashtag_api' => $this->urls->route('editor.hashTag'),
                'mention_api' => $this->urls->route('editor.mention'),
            ],
            'names' => [
                'file' => [
                    'input' => $this->getFileInputName(),
                    'image' => [
                        'class' => $this->getImageClassName(),
                        'identifier' => $this->getImageIdentifierAttrName(),
                    ]
                ],
                'tag' => [
                    'input' => $this->getTagInputName(),
                    'class' => $this->getTagClassName(),
                ],
                'mention' => [
                    'input' => $this->getMentionInputName(),
                    'class' => $this->getMentionClassName(),
                    'identifier' => $this->getMentionIdentifierAttrName(),
                ],
            ]
        ];

        return array_merge($options, $this->getDynamicOption());
    }

    /**
     * Get a editor name
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Get dynamic option data for the editor
     *
     * @return array
     */
    protected function getDynamicOption()
    {
        $data = array_except($this->config->all(), 'tools');
        $data['fontFamily'] = isset($data['fontFamily']) ? array_map(function ($v) {
            return trim($v);
        }, explode(',', $data['fontFamily'])) : [];
        $data['extensions'] = isset($data['extensions']) ? array_map(function ($v) {
            return trim($v);
        }, explode(',', $data['extensions'])) : [];
        $instance = new Instance($this->editors->getPermKey($this->instanceId));
        $data['perms'] = [
            'html' => $this->gate->allows('html', $instance),
            'tool' => $this->gate->allows('tool', $instance),
            'upload' => $this->gate->allows('upload', $instance),
        ];

        $data['files'] = $this->files;

        return $data;
    }

    /**
     * Get activated tool's identifier for the editor
     *
     * @return array
     */
    public function getActivateToolIds()
    {
        return $this->config->get('tools', []);
    }

    /**
     * Load tools
     *
     * @return void
     */
    protected function loadTools()
    {
        foreach ($this->getTools() as $tool) {
            $tool->initAssets();
        }
    }

    /**
     * Get activated tools for the editor
     *
     * @return AbstractTool[]
     */
    public function getTools()
    {
        if ($this->tools === null) {
            $this->tools = [];
            foreach ($this->getActivateToolIds() as $toolId) {
                if ($tool = $this->editors->getTool($toolId, $this->instanceId)) {
                    $this->tools[] = $tool;
                }
            }
        }

        return $this->tools;
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render()
    {
        $this->loadTools();

        $htmlString = [];
        if ($this->scriptOnly === false) {
            $htmlString[] = $this->getContentHtml();
            $htmlString[] = $this->getEditorScript($this->getOptions());
        }

        return implode('', $htmlString);
    }

    /**
     * Compile the raw content to be useful
     *
     * @param string $content content
     * @return string
     */
    public function compile($content)
    {
        $content = $this->hashTag($content);
        $content = $this->mention($content);
        $content = $this->link($content);
        $content = $this->image($content);

        return $this->compileBody($content) . $this->getFileView();
    }

    /**
     * Compile content body
     *
     * @param string $content content
     * @return string
     */
    abstract protected function compileBody($content);

    /**
     * Get file list view
     *
     * @return \Illuminate\Contracts\Support\Renderable|string
     */
    protected function getFileView()
    {
        if (count($this->files) < 1) {
            return '';
        }

        return $this->skins->getAssigned('editor')->setView('files')->setData(['files' => $this->files])->render();
    }

    /**
     * Get a content html tag string
     *
     * @return string
     */
    protected function getContentHtml()
    {
        $args = $this->getArguments();
        $html =
            '<textarea ' .
            'name="' . $args['contentDomName'] . '" ' .
            'id="' . $args['contentDomId'] . '" ' .
            $this->getContentDomHtmlOption($args['contentDomOptions']) .
            ' placeholder="' . xe_trans('xe::content') . '">'.
            $args['content'] .
            '</textarea>';

        return $html;
    }

    /**
     * Get attributes string for content html tag
     *
     * @param array $domOptions dom options
     * @return string
     */
    protected function getContentDomHtmlOption($domOptions)
    {
        $optionsString = '';
        foreach ($domOptions as $key => $val) {
            $optionsString.= "$key='{$val}' ";
        }

        return $optionsString;
    }

    /**
     * Get script for running the editor
     *
     * @param array $options options
     * @return mixed
     */
    protected function getEditorScript(array $options)
    {
        $editorScript = '
        <script>
            $(function() {
                XEeditor.getEditor(\'%s\').create(\'%s\', %s, %s, %s);
            });
        </script>';

        return sprintf(
            $editorScript,
            $this->getName(),
            $this->getArguments()['contentDomId'],
            json_encode($options),
            json_encode($this->getCustomOptions()),
            json_encode($this->getTools())
        );
    }

    /**
     * Get options for some editor only
     *
     * @return array
     */
    public function getCustomOptions()
    {
        return [];
    }

    /**
     * Compile tags in content body
     *
     * @param string $content content
     * @return string
     */
    protected function hashTag($content)
    {
        $tags = $this->getData($content, '.' . $this->getTagClassName());
        foreach ($tags as $tag) {
            $word = ltrim($tag['text'], '#');
            $content = str_replace(
                $tag['html'],
                sprintf('<a href="#%s" class="%s">#%s</a>', $word, $this->getTagClassName(), $word),
                $content
            );
        }

        return $content;
    }

    /**
     * Compile mentions in content body
     *
     * @param string $content content
     * @return string
     */
    protected function mention($content)
    {
        $mentions = $this->getData($content, '.' . $this->getMentionClassName(), 'data-id');
        foreach ($mentions as $mention) {
            $name = ltrim($mention['text'], '@');
            $content = str_replace(
                $mention['html'],
                sprintf(
                    '<span role="button" class="%s" data-toggle="xeUserMenu" data-user-id="%s">@%s</span>',
                    $this->getMentionClassName(),
                    $mention['data-id'],
                    $name
                ),
                $content
            );
        }

        return $content;
    }

    /**
     * Compile links in content body
     *
     * @param string $content content
     * @return string
     */
    protected function link($content)
    {
        return $content;
    }

    /**
     * Compile images in content body
     *
     * @param string $content content
     * @return string
     */
    protected function image($content)
    {
        $list = $this->getData($content, 'img.' . $this->getImageClassName(), 'data-id');

        $ids = array_column($list, 'data-id');
        $images = static::resolveImage($ids);
        $temp = [];
        foreach ($images as $image) {
            $temp[$image->getOriginKey()] = $image;
        }
        $images = $temp;
        unset($temp);
        
        foreach ($list as $data) {
            $image = $images[$data['data-id']];

            $attrStr = trim($data['html'], ' </>');
            $content = str_replace(
                [
                    '<' . $attrStr . '>',
                    '<' . $attrStr . '/>',
                    '<' . $attrStr . ' >',
                    '<' . $attrStr . ' />',
                ],
                sprintf(
                    '<img src="%s" class="%s" data-id="%s" />',
                    $image->url(),
                    $this->getImageClassName(),
                    $data['data-id']
                ),
                $content
            );
        }

        return $content;
    }

    /**
     * Get html node data
     *
     * @param string $content    content
     * @param string $selector   selector string
     * @param array  $attributes attribute names
     * @return array
     */
    private function getData($content, $selector, $attributes = [])
    {
        $attributes = !is_array($attributes) ? [$attributes] : $attributes;

        $crawler = $this->createCrawler($content);
        return $crawler->filter($selector)->each(function ($node, $i) use ($attributes) {
            $dom = $node->getNode(0);
            $data = [
                'html' => $dom->ownerDocument->saveHTML($dom),
                'inner' => $node->html(),
                'text' => $node->text(),
            ];

            foreach ($attributes as $attr) {
                $data[$attr] = $node->attr($attr);
            }

            return $data;
        });
    }

    /**
     * Set the image resolver
     *
     * @param callable $resolver resolver
     * @return void
     */
    public static function setImageResolver(callable $resolver)
    {
        static::$imageResolver = $resolver;
    }

    /**
     * Resolve image instances
     *
     * @param array $ids identifier list
     * @return array
     */
    public static function resolveImage($ids = [])
    {
        $ids = !is_array($ids) ? [$ids] : $ids;

        return call_user_func(static::$imageResolver, $ids);
    }

    /**
     * Create crawler instance
     *
     * @param string $content content
     * @return Crawler
     */
    private function createCrawler($content)
    {
        return new Crawler($content);
    }

    /**
     * Get uri for custom setting
     *
     * @param string $instanceId instance identifier
     * @return string|null
     */
    public static function getInstanceSettingURI($instanceId)
    {
        return null;
    }

    /**
     * Get the file input name
     *
     * @return string
     */
    public function getFileInputName()
    {
        return $this->fileInputName;
    }

    /**
     * Get the tag input name
     *
     * @return string
     */
    public function getTagInputName()
    {
        return $this->tagInputName;
    }

    /**
     * Get the mention input name
     *
     * @return string
     */
    public function getMentionInputName()
    {
        return $this->mentionInputName;
    }

    /**
     * Get the image class name
     *
     * @return string
     */
    public function getImageClassName()
    {
        return $this->imageClassName;
    }

    /**
     * Get the tag class name
     *
     * @return string
     */
    public function getTagClassName()
    {
        return $this->tagClassName;
    }

    /**
     * Get the mention class name
     *
     * @return string
     */
    public function getMentionClassName()
    {
        return $this->mentionClassName;
    }

    /**
     * Get the image identifier attribute name
     *
     * @return string
     */
    public function getImageIdentifierAttrName()
    {
        return $this->imageIdentifierAttrName;
    }

    /**
     * Get the mention identifier attribute name
     *
     * @return string
     */
    public function getMentionIdentifierAttrName()
    {
        return $this->mentionIdentifierAttrName;
    }
}
