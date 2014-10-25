<?php

namespace Ubirimi\Yongo\Controller\Administration\Field;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Repository\General\UbirimiLog;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Field\Custom;
use Ubirimi\Yongo\Repository\Field\FieldType;
use Ubirimi\Yongo\Repository\Project\YongoProject;

class AddDataController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $issueTypes = FieldType::getAll($session->get('client/id'));
        $projects = $this->getRepository(YongoProject::class)->getByClientId($session->get('client/id'));

        $fieldTypeCode = $request->get('type');

        $emptyName = false;
        $duplicateName = false;

        if ($request->request->has('finish_custom_field')) {
            $name = Util::cleanRegularInputField($request->request->get('name'));
            $description = Util::cleanRegularInputField($request->request->get('description'));
            $issueType = $request->request->get('issue_type');
            $project = $request->request->get('project');

            $fieldType = FieldType::getByCode($fieldTypeCode);
            $fieldTypeId = $fieldType['id'];

            if (empty($name)) {
                $emptyName = true;
            } else {
                // check for duplicate name

                $duplicateField = $this->getRepository(CustomField::class)->getByNameAndType($session->get('client/id'), $name, $fieldTypeId);
                if ($duplicateField)
                    $duplicateName = true;
            }
            if (!$emptyName && !$duplicateName) {
                $date = Util::getServerCurrentDateTime();

                $fieldId = CustomField::create(
                    $session->get('client/id'),
                    $fieldTypeCode,
                    $name,
                    $description,
                    $issueType,
                    $project,
                    $date
                );

                $this->getRepository(UbirimiLog::class)->add(
                    $session->get('client/id'),
                    SystemProduct::SYS_PRODUCT_YONGO,
                    $session->get('user/id'),
                    'ADD Yongo Custom Field ' . $name,
                    $date
                );

                return new RedirectResponse('/yongo/administration/custom-field/edit-field-screen/' . $fieldId);
            }
        }

        $menuSelectedCategory = 'issue';

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Create Custom Field Data';

        return $this->render(__DIR__ . '/../../../Resources/views/administration/field/AddData.php', get_defined_vars());
    }
}
