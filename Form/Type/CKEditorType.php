<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Form\Type;

use Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper,
    Ivory\CKEditorBundle\Model\ConfigManagerInterface,
    Ivory\CKEditorBundle\Model\PluginManagerInterface,
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Templating\Helper\CoreAssetsHelper;

/**
 * CKEditor type.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorType extends AbstractType
{
    /** @var boolean */
    protected $enable;

    /** @var string */
    protected $basePath;

    /** @var string */
    protected $jsPath;

    /** @var \Ivory\CKEditorBundle\Model\ConfigManagerInterface */
    protected $configManager;

    /** @var \Ivory\CKEditorBundle\Model\PluginManagerInterface */
    protected $pluginManager;

    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelper;

    /** @var \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper */
    protected $assetsVersionTrimerHelper;

    /**
     * Creates a CKEditor type.
     *
     * @param boolean                                                $enable                    TRUE if you want to use ckeditor widget, FALSE if you want to use textarea widget.
     * @param string                                                 $basePath                  The CKEditor base path.
     * @param string                                                 $jsPath                    The CKEditor JS path.
     * @param \Ivory\CKEditorBundle\Model\ConfigManagerInterface     $configManager             The CKEditor config manager.
     * @param \Ivory\CKEditorBundle\Model\PluginManagerInterface     $pluginManager             The CKEditor plugin manager.
     * @param \Symfony\Component\Templating\Helper\CoreAssetsHelper  $assetsHelper              The assets helper.
     * @param \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper $assetsVersionTrimerHelper The assets version trimer helper.
     */
    public function __construct(
        $enable,
        $basePath,
        $jsPath,
        ConfigManagerInterface $configManager,
        PluginManagerInterface $pluginManager,
        CoreAssetsHelper $assetsHelper,
        AssetsVersionTrimerHelper $assetsVersionTrimerHelper
    )
    {
        $this->isEnable($enable);
        $this->setBasePath($basePath);
        $this->setJsPath($jsPath);
        $this->setConfigManager($configManager);
        $this->setPluginManager($pluginManager);
        $this->setAssetsHelper($assetsHelper);
        $this->setAssetsVersionTrimerHelper($assetsVersionTrimerHelper);

    }

    /**
     * Sets/Checks if the widget is enabled.
     *
     * @param bolean $enable TRUE if the widget is enabled else FALSE.
     *
     * @return boolean TRUE if the widget is enabled else FALSE.
     */
    public function isEnable($enable = null)
    {
        if ($enable !== null) {
            $this->enable = (bool) $enable;
        }

        return $this->enable;
    }

    /**
     * Gets the CKEditor base path.
     *
     * @return string The CKEditor base path.
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Sets the CKEditor base path.
     *
     * @param string $basePath The CKEditor base path.
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Gets the CKEditor JS path.
     *
     * @return string The CKEditor JS path.
     */
    public function getJsPath()
    {
        return $this->jsPath;
    }

    /**
     * Sets the CKEditor JS path.
     *
     * @param string $jsPath The CKEditor JS path.
     */
    public function setJsPath($jsPath)
    {
        $this->jsPath = $jsPath;
    }

    /**
     * Gets the CKEditor config manager.
     *
     * @return type The CKEditor config manager.
     */
    public function getConfigManager()
    {
        return $this->configManager;
    }

    /**
     * Sets the CKEditor config manager.
     *
     * @param \Ivory\CKEditorBundle\Model\ConfigManagerInterface $configManager The CKEditor config manager.
     */
    public function setConfigManager(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * Gets the CKEditor plugin manager.
     *
     * @return type The CKEditor plugin manager.
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    /**
     * Sets the CKEditor plugin manager.
     *
     * @param \Ivory\CKEditorBundle\Model\PluginManagerInterface $pluginManager The CKEditor plugin manager.
     */
    public function setPluginManager(PluginManagerInterface $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * Gets the assets helper.
     *
     * @return \Symfony\Component\Templating\Helper\CoreAssetsHelper The assets helper.
     */
    public function getAssetsHelper()
    {
        return $this->assetsHelper;
    }

    /**
     * Sets the assets helper.
     *
     * @param \Symfony\Component\Templating\Helper\CoreAssetsHelper $assetsHelper The assets helper.
     */
    public function setAssetsHelper(CoreAssetsHelper $assetsHelper)
    {
        $this->assetsHelper = $assetsHelper;
    }

    /**
     * Gets the assets version trimer helper.
     *
     * @return \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper The assets version trimer helper.
     */
    public function getAssetsVersionTrimerHelper()
    {
        return $this->assetsVersionTrimerHelper;
    }

    /**
     * Sets the assets version trimer helper.
     *
     * @param \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper $assetsVersionTrimerHelper The assets version trimer helper.
     */
    public function setAssetsVersionTrimerHelper(AssetsVersionTrimerHelper $assetsVersionTrimerHelper)
    {
        $this->assetsVersionTrimerHelper = $assetsVersionTrimerHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->setAttribute('enable', (bool) $options['enable']);

        if ($builder->getAttribute('enable')) {
            $builder->setAttribute('base_path', $options['base_path']);
            $builder->setAttribute('js_path', $options['js_path']);

            $config = $options['config'];
            if ($options['config_name'] === null) {
                $name = uniqid('ivory', true);

                $options['config_name'] = $name;
                $this->configManager->setConfig($name, $config);
            } else {
                $this->configManager->mergeConfig($options['config_name'], $config);
            }

            $this->pluginManager->setPlugins($options['plugins']);

            $builder->setAttribute('config', $this->configManager->getConfig($options['config_name']));
            $builder->setAttribute('plugins', $this->pluginManager->getPlugins());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('enable', $form->getAttribute('enable'));

        if ($form->getAttribute('enable')) {
            $view->set(
                'base_path',
                $this->assetsVersionTrimerHelper->trim($this->assetsHelper->getUrl($form->getAttribute('base_path')))
            );

            $view->set('js_path', $this->assetsHelper->getUrl($form->getAttribute('js_path')));
            $view->set('config', json_encode($form->getAttribute('config')));
            $view->set('plugins', $form->getAttribute('plugins'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'required'    => false,
            'enable'      => $this->enable,
            'base_path'   => $this->basePath,
            'js_path'     => $this->jsPath,
            'config_name' => null,
            'config'      => array(),
            'plugins'     => array(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOptionValues(array $options)
    {
        return array('required' => array(false));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ckeditor';
    }
}
