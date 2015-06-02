<?php
/*
* Custom Markup For User v1.1.0 written by tyteen4a03@3.studIo.
* This software is licensed under the BSD 2-Clause modified License.
* See the LICENSE file within the package for details.
*/
class ThreePointStudio_CustomMarkupForUser_Deferred_UserMarkup extends XenForo_Deferred_Abstract {
    public function execute(array $deferred, array $data, $targetRunTime, &$status) {
        $data = array_merge(array(
            'position' => 0,
            'batch' => 150
        ), $data);
        $data['batch'] = max(1, $data['batch']);

        /* @var $userModel XenForo_Model_User */
        $userModel = XenForo_Model::create('XenForo_Model_User');

        $userIds = $userModel->getUserIdsInRange($data['position'], $data['batch']);
        if (sizeof($userIds) == 0)
        {
            return true;
        }

        foreach ($userIds AS $userId)
        {
            $data['position'] = $userId;

            /* @var $userDw XenForo_DataWriter_User */
            $userDw = XenForo_DataWriter::create('XenForo_DataWriter_User', XenForo_DataWriter::ERROR_SILENT);
            if ($userDw->setExistingData($userId))
            {
                $userDw->rebuildCustomMarkupCache();
            }
        }

        $actionPhrase = new XenForo_Phrase('rebuilding');
        $typePhrase = new XenForo_Phrase('3ps_cmfu_custom_markup');
        $status = sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, XenForo_Locale::numberFormat($data['position']));

        return $data;
    }

    public function canCancel()
    {
        return true;
    }
}