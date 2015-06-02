<?php
/*
* Custom Markup For User v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/

class ThreePointStudio_CustomMarkupForUser_Route_PrefixAdmin_CMFU implements XenForo_Route_Interface {
    protected $_subComponents = array(
        'presets' => array(
            'intId' => 'preset_id',
            'title' => 'title',
            'actionPrefix' => 'presets'
        )
    );

    /**
     * Match a specific route for an already matched prefix.
     *
     * @see XenForo_Route_Interface::match()
     */
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router) {
        $controller = 'ThreePointStudio_CustomMarkupForUser_ControllerAdmin_CMFU';
        $action = $router->getSubComponentAction($this->_subComponents, $routePath, $request, $controller);

        if ($action === false) {
            $action = $router->resolveActionWithIntegerParam($routePath, $request, 'preset_id');
        }

        return $router->getRouteMatch($controller, $action, 'users', '3ps-cmfu/');
    }

    /**
     * Method to build a link to the specified page/action with the provided
     * data and params.
     *
     * @see XenForo_Route_BuilderInterface
     */
    public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams) {
        $link = XenForo_Link::buildSubComponentLink($this->_subComponents, $outputPrefix, $action, $extension, $data);

        if (!$link)
        {
            $link = XenForo_Link::buildBasicLinkWithIntegerParam($outputPrefix, $action, $extension, $data, '');
        }

        return $link;
    }
}
