import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('rosters.list', breadcrumb => {
    breadcrumb.push(
        $t('Rosters'),
        'rosters.list'
    );
});

breadcrumbs.register('rosters.view', (breadcrumb, model) => {
    breadcrumb.parent('rosters.list');
    breadcrumb.push(
        $t('Roster details'),
        {
            name: 'rosters.view',
            params: {
                rosterId: model.id
            }
        }
    );
});

export default breadcrumbs;