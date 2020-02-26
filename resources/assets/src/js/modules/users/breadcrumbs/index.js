import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('users.list', breadcrumb => {
    breadcrumb.push(
        $t('Users'),
        'users.list'
    );
});

breadcrumbs.register('users.create', breadcrumb => {
    breadcrumb.parent('users.list');

    breadcrumb.push(
        $t('New user'),
        'users.create'
    );
});

breadcrumbs.register('users.view', (breadcrumb, { user }) => {
    breadcrumb.parent('users.list');

    breadcrumb.push(
        user.full_name,
        {
            name: 'users.view',
            params: {
                userId: user.id
            }
        }
    );
});

breadcrumbs.register('users.edit', (breadcrumb, { user }) => {
    breadcrumb.parent('users.view', { user });

    breadcrumb.push(
        $t('Edit'),
        {
            name: 'users.edit',
            params: {
                userId: user.id
            }
        }
    );
});

export default breadcrumbs;
