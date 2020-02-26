import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

import '@modules/users/breadcrumbs';

breadcrumbs.register('user-roles.list', breadcrumb => {
    breadcrumb.parent('users.list');

    breadcrumb.push(
        $t('Ролі користувачів'),
        'user-roles.list'
    );
});

export default breadcrumbs;
