import breadcrumbs from '@plugins/breadcrumbs';
import { $t } from '@utils/i18n';

breadcrumbs.register('tours.list', breadcrumb => {
    breadcrumb.push(
        $t('Tours'),
        'tours.list'
    );
});

breadcrumbs.register('tours.view', (breadcrumb, model) => {
    breadcrumb.parent('tours.list');
    breadcrumb.push(
        model.name,
        {
            name: 'tours.view',
            params: {
                tourId: model.id
            }
        }
    );
});
export default breadcrumbs;